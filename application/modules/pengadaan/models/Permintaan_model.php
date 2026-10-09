<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Permintaan Pengadaan: apotek minta obat -> diajukan -> disetujui/ditolak
 * -> diproses jadi PO -> selesai saat PO terpenuhi. Tiap pindah status
 * dicatat di permintaan_status_log (siapa, kapan, dari-ke, catatan).
 */
class Permintaan_model extends BF_Model
{
    protected $table_name = 'permintaan_pengadaan';
    protected $key = 'id_permintaan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;
    protected $skip_validation = true;

    private $alur = array(
        'DRAFT'     => array('DIAJUKAN', 'DIBATALKAN'),
        'DIAJUKAN'  => array('DISETUJUI', 'DITOLAK', 'DIBATALKAN'),
        'DISETUJUI' => array('DIPROSES', 'DIBATALKAN'),
        'DIPROSES'  => array('SELESAI'),
        'DITOLAK'   => array(),
        'DIBATALKAN' => array(),
        'SELESAI'   => array(),
    );

    public function __construct()
    {
        parent::__construct();
    }

    /** Daftar + jumlah item (filter status opsional). */
    public function daftar($status = null)
    {
        $this->db->select('permintaan_pengadaan.*,
                (SELECT COUNT(*) FROM permintaan_pengadaan_detail WHERE id_permintaan = permintaan_pengadaan.id_permintaan) AS jml_item', false)
            ->order_by('id_permintaan', 'DESC');
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->get('permintaan_pengadaan')->result();
    }

    /** Satu permintaan + detail (+nama obat, satuan, stok terkini). */
    public function detail($id_permintaan)
    {
        $row = $this->find($id_permintaan);
        if (! $row) {
            return false;
        }
        $row->items = $this->db->select('permintaan_pengadaan_detail.*, obat.nama_obat, obat.kode_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = permintaan_pengadaan_detail.id_obat')
            ->where('id_permintaan', $id_permintaan)
            ->order_by('obat.nama_obat', 'ASC')
            ->get('permintaan_pengadaan_detail')->result();
        $row->riwayat = $this->db->where('id_permintaan', $id_permintaan)
            ->order_by('id_log', 'ASC')->get('permintaan_status_log')->result();
        return $row;
    }

    /**
     * Buat draft: snapshot harga referensi (harga_satuan, fallback harga)
     * dan stok saat permintaan. $items: list array(id_obat, jumlah_minta, catatan?).
     */
    public function buat($id_pemohon, $items, $catatan = '', $unit = 'APOTEK')
    {
        if (empty($items)) {
            $this->error = 'Item permintaan kosong.';
            return false;
        }
        $this->db->trans_start();
        $valid = array();
        $obat_ids = array();
        foreach ($items as $it) {
            if (empty($it['id_obat'])) {
                $this->db->trans_complete();
                $this->error = 'Pilih obat dulu pada tiap baris item.';
                return false;
            }
            if (! preg_match('/^\d+$/', (string) ($it['jumlah_minta'] ?? '')) || (int) $it['jumlah_minta'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah diminta harus bilangan bulat positif.';
                return false;
            }
            $id_obat = (int) $it['id_obat'];
            if (isset($obat_ids[$id_obat])) {
                $this->db->trans_complete();
                $this->error = 'Obat tidak boleh muncul lebih dari sekali.';
                return false;
            }
            $obat_ids[$id_obat] = true;
            $obat = $this->db->where('id_obat', $id_obat)->get('obat')->row();
            if (! $obat || $obat->status !== 'AKTIF') {
                $this->db->trans_complete();
                $this->error = "Obat ID {$id_obat} tidak tersedia.";
                return false;
            }
            $stok = $this->db->where('id_obat', $id_obat)->get('stok_obat')->row();
            $valid[] = array(
                'id_obat' => $id_obat,
                'satuan' => $obat->satuan,
                'harga_referensi' => (float) ($obat->harga_satuan > 0 ? $obat->harga_satuan : $obat->harga),
                'stok_saat_minta' => $stok ? (int) $stok->jumlah_stok : 0,
                'jumlah_minta' => (int) $it['jumlah_minta'],
                'catatan' => isset($it['catatan']) ? trim($it['catatan']) : null,
            );
        }
        $id = $this->insert(array(
            'nomor_permintaan' => nomor_baru('PP', 'permintaan_pengadaan', 'nomor_permintaan'),
            'tanggal_permintaan' => date('Y-m-d H:i:s'),
            'id_pemohon' => (int) $id_pemohon,
            'unit' => trim($unit) !== '' ? trim($unit) : 'APOTEK',
            'status' => 'DRAFT',
            'catatan' => trim($catatan),
            'dibuat_oleh' => (int) $id_pemohon,
        ));
        if ($id) {
            foreach ($valid as &$v) {
                $v['id_permintaan'] = $id;
            }
            unset($v);
            $this->db->insert_batch('permintaan_pengadaan_detail', $valid);
            $this->catat_log($id, null, 'DRAFT', $id_pemohon, 'Permintaan dibuat.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id) {
            $this->error = 'Gagal menyimpan permintaan.';
            return false;
        }
        return $id;
    }

    /** Ubah item draft (harga/stok snapshot dihitung ulang). */
    public function ubah_draft($id_permintaan, $items, $catatan, $id_user)
    {
        $row = $this->find($id_permintaan);
        if (! $row) {
            $this->error = 'Permintaan tidak ditemukan.';
            return false;
        }
        if ($row->status !== 'DRAFT') {
            $this->error = "Draft yang sudah {$row->status} tidak dapat diubah.";
            return false;
        }
        $this->db->trans_start();
        $this->db->where('id_permintaan', $id_permintaan)->delete('permintaan_pengadaan_detail');
        $obat_ids = array();
        foreach ($items as $it) {
            if (empty($it['id_obat'])) {
                $this->db->trans_complete();
                $this->error = 'Pilih obat dulu pada tiap baris item.';
                return false;
            }
            if (! preg_match('/^\d+$/', (string) ($it['jumlah_minta'] ?? '')) || (int) $it['jumlah_minta'] <= 0) {
                $this->db->trans_complete();
                $this->error = 'Jumlah diminta harus bilangan bulat positif.';
                return false;
            }
            $id_obat = (int) $it['id_obat'];
            if (isset($obat_ids[$id_obat])) {
                $this->db->trans_complete();
                $this->error = 'Obat tidak boleh muncul lebih dari sekali.';
                return false;
            }
            $obat_ids[$id_obat] = true;
            $obat = $this->db->where('id_obat', $id_obat)->get('obat')->row();
            if (! $obat || $obat->status !== 'AKTIF') {
                $this->db->trans_complete();
                $this->error = "Obat ID {$id_obat} tidak tersedia.";
                return false;
            }
            $stok = $this->db->where('id_obat', $id_obat)->get('stok_obat')->row();
            $this->db->insert('permintaan_pengadaan_detail', array(
                'id_permintaan' => $id_permintaan,
                'id_obat' => $id_obat,
                'satuan' => $obat->satuan,
                'harga_referensi' => (float) ($obat->harga_satuan > 0 ? $obat->harga_satuan : $obat->harga),
                'stok_saat_minta' => $stok ? (int) $stok->jumlah_stok : 0,
                'jumlah_minta' => (int) $it['jumlah_minta'],
                'catatan' => isset($it['catatan']) ? trim($it['catatan']) : null,
            ));
        }
        $this->update($id_permintaan, array(
            'catatan' => trim($catatan),
            'diubah_oleh' => (int) $id_user,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal menyimpan perubahan draft.';
            return false;
        }
        return true;
    }

    /** Pindah status sesuai alur + tulis histori. */
    public function ubah_status($id_permintaan, $status_baru, $id_user, $catatan = '')
    {
        $row = $this->find($id_permintaan);
        if (! $row) {
            $this->error = 'Permintaan tidak ditemukan.';
            return false;
        }
        $boleh = isset($this->alur[$row->status]) ? $this->alur[$row->status] : array();
        if (! in_array($status_baru, $boleh)) {
            $this->error = "Status {$row->status} tidak dapat berubah ke {$status_baru}.";
            return false;
        }
        if (in_array($status_baru, array('DITOLAK', 'DIBATALKAN')) && trim($catatan) === '') {
            $this->error = 'Penolakan/pembatalan wajib disertai alasan.';
            return false;
        }
        $data = array('status' => $status_baru, 'diubah_oleh' => (int) $id_user, 'updated_at' => date('Y-m-d H:i:s'));
        if ($status_baru === 'DISETUJUI') {
            $data['disetujui_oleh'] = (int) $id_user;
            $data['tanggal_persetujuan'] = date('Y-m-d H:i:s');
            $data['catatan_persetujuan'] = trim($catatan);
        }
        $this->db->trans_start();
        $this->update($id_permintaan, $data);
        $this->catat_log($id_permintaan, $row->status, $status_baru, $id_user, $catatan);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Gagal mengubah status permintaan.';
            return false;
        }
        return true;
    }

    /**
     * Buat PO dari permintaan DISETUJUI (item disalin, harga boleh dinego).
     * $harga_override: array(id_obat => harga_baru). Kembali array PO.
     */
    public function buat_po($id_permintaan, $id_supplier, $harga_override, $id_user)
    {
        $row = $this->find($id_permintaan);
        if (! $row) {
            $this->error = 'Permintaan tidak ditemukan.';
            return false;
        }
        if ($row->status !== 'DISETUJUI') {
            $this->error = 'Hanya permintaan DISETUJUI yang dapat dibuatkan PO.';
            return false;
        }
        if ((int) $id_supplier <= 0 || ! $this->db->where('id_supplier', $id_supplier)->get('supplier')->row()) {
            $this->error = 'Supplier tidak valid.';
            return false;
        }
        $items = $this->db->where('id_permintaan', $id_permintaan)->get('permintaan_pengadaan_detail')->result();
        if (empty($items)) {
            $this->error = 'Permintaan tidak memiliki item.';
            return false;
        }
        $po_items = array();
        foreach ($items as $it) {
            $harga = isset($harga_override[$it->id_obat]) && is_numeric($harga_override[$it->id_obat]) && (float) $harga_override[$it->id_obat] >= 0
                ? (float) $harga_override[$it->id_obat]
                : (float) $it->harga_referensi;
            $po_items[] = array('id_obat' => (int) $it->id_obat, 'jumlah_pesan' => (int) $it->jumlah_minta, 'harga' => $harga);
        }
        $this->load->model('pengadaan/pengadaan_model');
        $po = $this->pengadaan_model->pesan($id_supplier, $po_items, $id_permintaan);
        if (! $po) {
            $this->error = $this->pengadaan_model->error ?: 'Gagal membuat PO.';
            return false;
        }
        if (! $this->ubah_status($id_permintaan, 'DIPROSES', $id_user, 'PO ' . $po['nomor_pengadaan'])) {
            // ponytail: PO sudah jadi; kegagalan catat status jangan batalkan PO.
            $this->error = null;
        }
        return $po;
    }

    /**
     * Saran restock: obat AKTIF yang stoknya di bawah minimum, kecuali yang
     * sudah masuk permintaan aktif. Saran jumlah = tutup sampai batas minimum.
     * Info saja — PO tidak pernah dibuat otomatis.
     */
    public function saran()
    {
        $diminta = array();
        foreach ($this->db->select('DISTINCT permintaan_pengadaan_detail.id_obat', false)
            ->join('permintaan_pengadaan', 'permintaan_pengadaan.id_permintaan = permintaan_pengadaan_detail.id_permintaan')
            ->where_in('permintaan_pengadaan.status', array('DRAFT', 'DIAJUKAN', 'DISETUJUI', 'DIPROSES'))
            ->get('permintaan_pengadaan_detail')->result() as $d) {
            $diminta[] = (int) $d->id_obat;
        }
        $this->db->select('obat.id_obat, obat.nama_obat, obat.satuan, obat.stok_minimum, COALESCE(stok_obat.jumlah_stok, 0) AS stok', false)
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('obat.status', 'AKTIF')
            ->where('COALESCE(stok_obat.jumlah_stok, 0) < obat.stok_minimum', null, false)
            ->order_by('obat.nama_obat', 'ASC');
        if (! empty($diminta)) {
            $this->db->where_not_in('obat.id_obat', $diminta);
        }
        $rows = $this->db->get('obat')->result();
        foreach ($rows as $r) {
            $r->saran = max(1, (int) $r->stok_minimum - (int) $r->stok);
        }
        return $rows;
    }

    private function catat_log($id_permintaan, $lama, $baru, $id_user, $catatan)
    {
        $this->db->insert('permintaan_status_log', array(
            'id_permintaan' => (int) $id_permintaan,
            'status_lama' => $lama,
            'status_baru' => $baru,
            'id_user' => (int) $id_user,
            'tanggal' => date('Y-m-d H:i:s'),
            'catatan' => trim((string) $catatan),
        ));
    }
}
