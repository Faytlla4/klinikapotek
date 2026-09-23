<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Retur Apotek Online (full-order, diajukan pasien, diputus apoteker).
 *
 * Status: DIMINTA -> SELESAI | DITOLAK. Stok hanya bergerak saat disetujui
 * (RESTOCK via Stok_model::masuk, MUSNAH dicatat tanpa menambah stok).
 * Uang kembali manual (dicatat metode+nominal); tagihan lama tidak diubah.
 * Resep tidak dibuka kuncinya oleh retur.
 */
class Retur_model extends BF_Model
{
    protected $table_name = 'retur_online';
    protected $key = 'id_retur';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;
    protected $skip_validation = true;

    private $aktif = array('DIMINTA', 'SELESAI');
    private $disposisi_valid = array('RESTOCK', 'MUSNAH');
    private $refund_valid = array('TUNAI', 'TRANSFER', 'E_WALLET', 'LAINNYA');

    public function __construct()
    {
        parent::__construct();
    }

    /** Daftar retur + pesanan + pasien (filter status opsional). */
    public function daftar($status = null)
    {
        $this->db->select('retur_online.*, pesanan_online.nomor_pesanan, pesanan_online.total, pasien.nama AS nama_pasien')
            ->join('pesanan_online', 'pesanan_online.id_pesanan = retur_online.id_pesanan')
            ->join('pasien', 'pasien.id_pasien = pesanan_online.id_pasien')
            ->order_by('retur_online.id_retur', 'DESC');
        if ($status) {
            $this->db->where('retur_online.status', $status);
        }
        return $this->db->get('retur_online')->result();
    }

    /** Satu retur + item + info pesanan/pasien (false bila tak ada). */
    public function detail($id_retur)
    {
        $row = $this->find($id_retur);
        if (! $row) {
            return false;
        }
        $row->items = $this->db->select('retur_online_detail.*, obat.nama_obat, obat.satuan, pesanan_online_detail.harga')
            ->join('obat', 'obat.id_obat = retur_online_detail.id_obat')
            ->join('pesanan_online_detail', 'pesanan_online_detail.id_pesanan = ' . (int) $row->id_pesanan . ' AND pesanan_online_detail.id_obat = retur_online_detail.id_obat')
            ->where('id_retur', $id_retur)
            ->order_by('obat.nama_obat', 'ASC')
            ->get('retur_online_detail')->result();
        // ponytail: kadaluarsa terdekat + kedatangan terakhir per obat (satu query, bukan N+1).
        $exp_map = array();
        if (! empty($row->items) && $this->db->field_exists('tanggal_kadaluarsa', 'penerimaan_obat_detail')) {
            $ids = array();
            foreach ($row->items as $it) {
                $ids[(int) $it->id_obat] = true;
            }
            $exp = $this->db->select("penerimaan_obat_detail.id_obat,
                    MIN(penerimaan_obat_detail.tanggal_kadaluarsa)::date AS kadaluarsa_terdekat,
                    (MIN(penerimaan_obat_detail.tanggal_kadaluarsa)::date - CURRENT_DATE) AS sisa_hari,
                    MAX(penerimaan_obat.tanggal_terima) AS datang_terakhir", false)
                ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
                ->where_in('penerimaan_obat_detail.id_obat', array_keys($ids))
                ->group_by('penerimaan_obat_detail.id_obat')
                ->get('penerimaan_obat_detail')->result();
            foreach ($exp as $e) {
                $exp_map[(int) $e->id_obat] = $e;
            }
        }
        foreach ($row->items as $it) {
            $e = isset($exp_map[(int) $it->id_obat]) ? $exp_map[(int) $it->id_obat] : null;
            $it->kadaluarsa_terdekat = $e ? $e->kadaluarsa_terdekat : null;
            $it->sisa_hari = $e && $e->kadaluarsa_terdekat ? (int) $e->sisa_hari : null;
            $it->datang_terakhir = $e ? $e->datang_terakhir : null;
        }
        $row->pesanan = $this->db->where('id_pesanan', $row->id_pesanan)->get('pesanan_online')->row();
        $row->pasien = $row->pesanan
            ? $this->db->select('nama, no_hp')->where('id_pasien', $row->pesanan->id_pasien)->get('pasien')->row()
            : false;
        return $row;
    }

    /** Retur terakhir satu pesanan milik pasien (false bila bukan miliknya/tak ada). */
    public function retur_untuk_pesanan($id_pesanan, $id_pasien)
    {
        $psn = $this->db->where('id_pesanan', $id_pesanan)->get('pesanan_online')->row();
        if (! $psn || (int) $psn->id_pasien !== (int) $id_pasien) {
            return false;
        }
        return $this->db->where('id_pesanan', $id_pesanan)
            ->order_by('id_retur', 'DESC')->limit(1)->get('retur_online')->row();
    }

    /**
     * Pasien mengajukan retur full-order. $alasan wajib bermakna.
     * @return int|bool id_retur atau false (lihat $this->error).
     */
    public function ajukan($id_pesanan, $id_pasien, $alasan)
    {
        $alasan = trim((string) $alasan);
        if (mb_strlen($alasan) < 10) {
            $this->error = 'Alasan retur wajib diisi (minimal 10 karakter).';
            return false;
        }
        $this->db->trans_start();
        $psn = $this->db->query('SELECT * FROM pesanan_online WHERE id_pesanan = ? FOR UPDATE', array($id_pesanan))->row();
        if (! $psn || (int) $psn->id_pasien !== (int) $id_pasien) {
            $this->db->trans_complete();
            $this->error = 'Pesanan tidak ditemukan.';
            return false;
        }
        if ($psn->status !== 'SELESAI') {
            $this->db->trans_complete();
            $this->error = 'Hanya pesanan SELESAI yang dapat diretur.';
            return false;
        }
        $tagihan = ! empty($psn->id_tagihan)
            ? $this->db->where('id_tagihan', $psn->id_tagihan)->get('tagihan')->row()
            : false;
        if (! $tagihan || $tagihan->status !== 'LUNAS') {
            $this->db->trans_complete();
            $this->error = 'Pesanan hanya dapat diretur setelah pembayaran lunas.';
            return false;
        }
        $ada = $this->db->where('id_pesanan', $id_pesanan)->where_in('status', $this->aktif)->get('retur_online')->row();
        if ($ada) {
            $this->db->trans_complete();
            $this->error = $ada->status === 'DIMINTA' ? 'Pengajuan retur masih menunggu keputusan apoteker.' : 'Pesanan ini sudah pernah diretur.';
            return false;
        }
        $items = $this->db->where('id_pesanan', $id_pesanan)->get('pesanan_online_detail')->result();
        if (empty($items)) {
            $this->db->trans_complete();
            $this->error = 'Pesanan tidak memiliki item.';
            return false;
        }
        $id_retur = $this->insert(array(
            'id_pesanan' => (int) $id_pesanan,
            'nomor_retur' => nomor_baru('RO', 'retur_online', 'nomor_retur'),
            'status' => 'DIMINTA',
            'alasan' => mb_substr($alasan, 0, 1000),
        ));
        if ($id_retur) {
            foreach ($items as $it) {
                $this->db->insert('retur_online_detail', array(
                    'id_retur' => $id_retur,
                    'id_obat' => (int) $it->id_obat,
                    'jumlah' => (int) $it->jumlah,
                ));
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id_retur) {
            $this->error = 'Gagal menyimpan pengajuan retur.';
            return false;
        }
        return $id_retur;
    }

    /**
     * Apoteker menyetujui: disposisi per item + refund manual, atomik.
     * $disposisi: array(id_detail => RESTOCK|MUSNAH).
     */
    public function setujui($id_retur, $disposisi, $metode_refund, $nominal_refund, $catatan, $id_apoteker)
    {
        $catatan = trim((string) $catatan);
        if (! in_array($metode_refund, $this->refund_valid)) {
            $this->error = 'Metode refund tidak valid.';
            return false;
        }
        if (! is_numeric($nominal_refund) || (float) $nominal_refund < 0) {
            $this->error = 'Nominal refund tidak valid.';
            return false;
        }
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM retur_online WHERE id_retur = ? FOR UPDATE', array($id_retur))->row();
        if (! $row) {
            $this->db->trans_complete();
            $this->error = 'Retur tidak ditemukan.';
            return false;
        }
        if ($row->status !== 'DIMINTA') {
            $this->db->trans_complete();
            $this->error = "Retur berstatus {$row->status}, tidak dapat disetujui.";
            return false;
        }
        $psn = $this->db->where('id_pesanan', $row->id_pesanan)->get('pesanan_online')->row();
        if (! $psn || (float) $nominal_refund > (float) $psn->total) {
            $this->db->trans_complete();
            $this->error = 'Nominal refund melebihi total pesanan.';
            return false;
        }
        $details = $this->db->where('id_retur', $id_retur)->get('retur_online_detail')->result();
        if (empty($details)) {
            $this->db->trans_complete();
            $this->error = 'Retur tidak memiliki item.';
            return false;
        }
        $this->load->model('stok/stok_model');
        foreach ($details as $d) {
            if (! isset($disposisi[$d->id_detail]) || ! in_array($disposisi[$d->id_detail], $this->disposisi_valid)) {
                $this->db->trans_complete();
                $this->error = 'Disposisi tiap item wajib dipilih (Restock/Musnah).';
                return false;
            }
            $disp = $disposisi[$d->id_detail];
            $this->db->where('id_detail', $d->id_detail)->update('retur_online_detail', array('disposisi' => $disp));
            // ponytail: MUSNAH = keluar tanpa kembali ke stok jual; hanya RESTOCK menambah stok.
            if ($disp === 'RESTOCK') {
                if (! $this->stok_model->masuk((int) $d->id_obat, (int) $d->jumlah, 'RETUR_ONLINE', (int) $id_retur, 'Retur online ' . $psn->nomor_pesanan)) {
                    $this->db->trans_complete();
                    $this->error = $this->stok_model->error ?: 'Gagal mengembalikan stok.';
                    return false;
                }
            }
        }
        $this->update($id_retur, array(
            'status' => 'SELESAI',
            'metode_refund' => $metode_refund,
            'nominal_refund' => (float) $nominal_refund,
            'catatan_apoteker' => $catatan !== '' ? mb_substr($catatan, 0, 1000) : null,
            'tanggal_keputusan' => date('Y-m-d H:i:s'),
            'diproses_oleh' => (int) $id_apoteker,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Transaksi persetujuan gagal.';
            return false;
        }
        return true;
    }

    /** Apoteker menolak (catatan wajib agar pasien tahu alasannya). */
    public function tolak($id_retur, $catatan, $id_apoteker)
    {
        $catatan = trim((string) $catatan);
        if (mb_strlen($catatan) < 10) {
            $this->error = 'Catatan penolakan wajib diisi (minimal 10 karakter).';
            return false;
        }
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM retur_online WHERE id_retur = ? FOR UPDATE', array($id_retur))->row();
        if (! $row) {
            $this->db->trans_complete();
            $this->error = 'Retur tidak ditemukan.';
            return false;
        }
        if ($row->status !== 'DIMINTA') {
            $this->db->trans_complete();
            $this->error = "Retur berstatus {$row->status}, tidak dapat ditolak.";
            return false;
        }
        $this->update($id_retur, array(
            'status' => 'DITOLAK',
            'catatan_apoteker' => mb_substr($catatan, 0, 1000),
            'tanggal_keputusan' => date('Y-m-d H:i:s'),
            'diproses_oleh' => (int) $id_apoteker,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Transaksi penolakan gagal.';
            return false;
        }
        return true;
    }
}
