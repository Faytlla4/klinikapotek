<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pesanan Online Pasien.
 *
 * Relasi: pesanan_online -> pasien; detail -> obat (+resep opsional).
 * Konversi sekali-jalan ke penjualan(TAGIHAN) existing saat apoteker
 * memproses; stok hanya berkurang di Penjualan_model::jual().
 * Status: MENUNGGU -> DIVERIFIKASI -> DIPROSES -> SIAP -> SELESAI (+BATAL).
 * Bayar: BELUM_DIBAYAR -> LUNAS (+BATAL), diselaraskan dari tagihan.
 */
class Pesanan_model extends BF_Model
{
    protected $table_name = 'pesanan_online';
    protected $key = 'id_pesanan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;
    protected $skip_validation = true;

    private $alur = array(
        'MENUNGGU'    => array('DIVERIFIKASI', 'BATAL'),
        'DIVERIFIKASI'=> array('DIPROSES', 'BATAL'),
        'DIPROSES'    => array('SIAP', 'BATAL'),
        'SIAP'        => array('SELESAI', 'BATAL'),
        'SELESAI'     => array(),
        'BATAL'       => array(),
    );

    private $resep_valid = array('DIBUAT', 'DIPROSES', 'SIAP');

    public function __construct()
    {
        parent::__construct();
    }

    /** Daftar pesanan milik pasien (+ jumlah item). */
    public function daftar_pasien($id_pasien)
    {
        return $this->db->select('pesanan_online.*,
                (SELECT COUNT(*) FROM pesanan_online_detail WHERE id_pesanan = pesanan_online.id_pesanan) AS jml_item', false)
            ->where('id_pasien', $id_pasien)
            ->order_by('id_pesanan', 'DESC')
            ->get('pesanan_online')
            ->result();
    }

    /** Satu pesanan milik pasien (false bila bukan miliknya). */
    public function milik_pasien($id_pesanan, $id_pasien)
    {
        $row = $this->find($id_pesanan);
        if (! $row || (int) $row->id_pasien !== (int) $id_pasien) {
            return false;
        }
        return $row;
    }

    /** Detail + item + info obat (ownership dicek dulu). */
    public function detail_pasien($id_pesanan, $id_pasien)
    {
        $row = $this->milik_pasien($id_pesanan, $id_pasien);
        if (! $row) {
            return false;
        }
        $row->items = $this->db->select('pesanan_online_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = pesanan_online_detail.id_obat')
            ->where('id_pesanan', $id_pesanan)
            ->get('pesanan_online_detail')
            ->result();
        $this->selaraskan_bayar($row);
        return $row;
    }

    /** Daftar resep valid milik pasien yang memuat obat tertentu. */
    public function daftar_resep_obat($id_pasien, $id_obat)
    {
        $rows = $this->db->select('resep.id_resep, resep.nomor_resep, resep.tanggal_resep, resep_detail.jumlah AS jml_resep')
            ->join('resep_detail', 'resep_detail.id_resep = resep.id_resep AND resep_detail.id_obat = ' . (int) $id_obat)
            ->where('resep.id_pasien', $id_pasien)
            ->where_in('resep.status', $this->resep_valid)
            ->order_by('resep.tanggal_resep', 'DESC')
            ->get('resep')
            ->result();
        return array_values(array_filter($rows, function ($r) {
            return ! $this->resep_terpakai($r->id_resep);
        }));
    }

    /** Resep valid milik pasien untuk satu obat (false bila tak ada). */
    public function resep_valid_untuk($id_pasien, $id_obat, $jumlah)
    {
        $rows = $this->db->select('resep.*, resep_detail.jumlah AS jml_resep')
            ->join('resep_detail', 'resep_detail.id_resep = resep.id_resep AND resep_detail.id_obat = ' . (int) $id_obat)
            ->where('resep.id_pasien', $id_pasien)
            ->where_in('resep.status', $this->resep_valid)
            ->order_by('resep.tanggal_resep', 'DESC')
            ->get('resep')
            ->result();
        foreach ($rows as $r) {
            if ((int) $jumlah <= (int) $r->jml_resep && ! $this->resep_terpakai($r->id_resep)) {
                return $r;
            }
        }
        return false;
    }

    /** True bila resep sudah dipakai penjualan SELESAI / pesanan aktif. */
    public function resep_terpakai($id_resep)
    {
        if ($this->db->where('id_resep', $id_resep)->where('status', 'SELESAI')->get('penjualan_obat')->row()) {
            return true;
        }
        return (bool) $this->db->select('pesanan_online_detail.id_detail')
            ->join('pesanan_online', 'pesanan_online.id_pesanan = pesanan_online_detail.id_pesanan')
            ->where('pesanan_online_detail.id_resep', $id_resep)
            ->where('pesanan_online.status !=', 'BATAL')
            ->get('pesanan_online_detail')->row();
    }

    /**
     * Validasi satu item dari sisi server (harga & stok selalu dari DB).
     * @return array|bool array(id_obat,jumlah,harga,subtotal,id_resep?) atau false.
     */
    public function validasi_item($id_pasien, $id_obat, $jumlah, $id_resep = null)
    {
        $obat = $this->db->where('id_obat', $id_obat)->get('obat')->row();
        if (! $obat || $obat->status !== 'AKTIF') {
            $this->error = 'Obat tidak tersedia.';
            return false;
        }
        if (! preg_match('/^\d+$/', (string) $jumlah) || (int) $jumlah <= 0) {
            $this->error = 'Jumlah harus bilangan bulat lebih dari nol.';
            return false;
        }
        $stok = $this->db->where('id_obat', $id_obat)->get('stok_obat')->row();
        if (! $stok || (int) $stok->jumlah_stok < (int) $jumlah) {
            $this->error = 'Stok ' . $obat->nama_obat . ' tidak cukup.';
            return false;
        }
        $pakai_resep = null;
        // PG mengembalikan boolean sebagai string 't'/'f' (keduanya truthy!).
        $wajib = in_array($obat->wajib_resep, array(true, 1, '1', 't', 'T'), true);
        if ($wajib) {
            if (empty($id_resep)) {
                $this->error = $obat->nama_obat . ' wajib resep dokter.';
                return false;
            }
            $r = $this->db->where('id_resep', $id_resep)->get('resep')->row();
            if (! $r || (int) $r->id_pasien !== (int) $id_pasien
                || ! in_array($r->status, $this->resep_valid)
                || $this->resep_terpakai($id_resep)
            ) {
                $this->error = 'Resep tidak valid untuk ' . $obat->nama_obat . '.';
                return false;
            }
            $rd = $this->db->where(array('id_resep' => $id_resep, 'id_obat' => $id_obat))->get('resep_detail')->row();
            if (! $rd || (int) $jumlah > (int) $rd->jumlah) {
                $this->error = 'Jumlah melebihi resep dokter.';
                return false;
            }
            $pakai_resep = (int) $id_resep;
        }
        $harga = (float) $obat->harga;
        return array(
            'id_obat' => (int) $id_obat, 'jumlah' => (int) $jumlah,
            'harga' => $harga, 'subtotal' => $harga * (int) $jumlah,
            'id_resep' => $pakai_resep,
        );
    }

    /**
     * Buat pesanan (checkout). $items: list array(id_obat, jumlah, id_resep?).
     * Semua harga/stok/resep divalidasi ulang dari database.
     */
    public function buat($id_pasien, $alamat, $items)
    {
        if (trim($alamat) === '') {
            $this->error = 'Alamat pengiriman wajib diisi.';
            return false;
        }
        if (empty($items)) {
            $this->error = 'Keranjang kosong.';
            return false;
        }
        $valid = array();
        $resep_ids = array();
        foreach ($items as $it) {
            $v = $this->validasi_item($id_pasien, $it['id_obat'], $it['jumlah'], isset($it['id_resep']) ? $it['id_resep'] : null);
            if (! $v) {
                return false;
            }
            if ($v['id_resep']) {
                $resep_ids[$v['id_resep']] = true;
            }
            $valid[] = $v;
        }
        if (count($resep_ids) > 1) {
            $this->error = 'Satu pesanan hanya untuk satu resep dokter.';
            return false;
        }
        $this->db->trans_start();
        $total = 0;
        foreach ($valid as $v) {
            $total += $v['subtotal'];
        }
        $id = $this->insert(array(
            'id_pasien' => $id_pasien,
            'nomor_pesanan' => nomor_baru('PO', 'pesanan_online', 'nomor_pesanan'),
            'tanggal_pesanan' => date('Y-m-d H:i:s'),
            'alamat_kirim' => trim($alamat),
            'total' => $total,
            'status' => 'MENUNGGU',
            'status_bayar' => 'BELUM_DIBAYAR',
        ));
        if ($id) {
            foreach ($valid as &$v) {
                $v['id_pesanan'] = $id;
            }
            unset($v);
            $this->db->insert_batch('pesanan_online_detail', $valid);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id) {
            $this->error = 'Gagal menyimpan pesanan.';
            return false;
        }
        return $id;
    }

    /** Ubah status mengikuti alur (aplikasi + apoteker). */
    public function ubah_status($id_pesanan, $status_baru)
    {
        $row = $this->find($id_pesanan);
        if (! $row) {
            $this->error = 'Pesanan tidak ditemukan.';
            return false;
        }
        $boleh = isset($this->alur[$row->status]) ? $this->alur[$row->status] : array();
        if (! in_array($status_baru, $boleh)) {
            $this->error = "Status {$row->status} tidak dapat berubah ke {$status_baru}.";
            return false;
        }
        return $this->update($id_pesanan, array('status' => $status_baru, 'updated_at' => date('Y-m-d H:i:s')));
    }

    /** Pasien membatalkan pesanannya sendiri (hanya dari MENUNGGU). */
    public function batal_pasien($id_pesanan, $id_pasien)
    {
        if (! $this->milik_pasien($id_pesanan, $id_pasien)) {
            $this->error = 'Pesanan tidak ditemukan.';
            return false;
        }
        return $this->ubah_status($id_pesanan, 'BATAL');
    }

    /**
     * Proses oleh apoteker: kunci baris, validasi ulang stok, buat SATU
     * penjualan ONLINE + SATU tagihan. Anti double-processing via
     * SELECT FOR UPDATE + guard id_penjualan.
     */
    public function proses($id_pesanan)
    {
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM pesanan_online WHERE id_pesanan = ? FOR UPDATE', array($id_pesanan))->row();
        if (! $row) {
            $this->db->trans_complete();
            $this->error = 'Pesanan tidak ditemukan.';
            return false;
        }
        if (! in_array($row->status, array('MENUNGGU', 'DIVERIFIKASI'))) {
            $this->db->trans_complete();
            $this->error = "Pesanan berstatus {$row->status}, tidak dapat diproses.";
            return false;
        }
        if (! empty($row->id_penjualan)) {
            $this->db->trans_complete();
            $this->error = 'Pesanan sudah diproses sebelumnya.';
            return false;
        }
        $items = $this->db->select('pesanan_online_detail.*, obat.wajib_resep')
            ->join('obat', 'obat.id_obat = pesanan_online_detail.id_obat')
            ->where('id_pesanan', $id_pesanan)
            ->get('pesanan_online_detail')->result();
        if (empty($items)) {
            $this->db->trans_complete();
            $this->error = 'Pesanan tidak memiliki item.';
            return false;
        }
        // Validasi ulang stok dalam transaksi yang sama.
        $id_resep = null;
        $jual_items = array();
        foreach ($items as $it) {
            $stok = $this->db->where('id_obat', $it->id_obat)->get('stok_obat')->row();
            if (! $stok || (int) $stok->jumlah_stok < (int) $it->jumlah) {
                $this->db->trans_complete();
                $this->error = 'Stok tidak cukup saat diproses.';
                return false;
            }
            if ($it->id_resep) {
                if ($id_resep && (int) $id_resep !== (int) $it->id_resep) {
                    $this->db->trans_complete();
                    $this->error = 'Pesanan memakai lebih dari satu resep.';
                    return false;
                }
                $id_resep = (int) $it->id_resep;
            }
            $jual_items[] = array('id_obat' => (int) $it->id_obat, 'jumlah' => (int) $it->jumlah);
        }
        $this->load->model('penjualan/penjualan_model');
        $jual = $this->penjualan_model->jual('ONLINE', $id_resep, $row->id_pasien, $jual_items);
        if (! $jual) {
            $this->db->trans_complete();
            $this->error = $this->penjualan_model->error ?: 'Gagal membuat penjualan.';
            return false;
        }
        $this->load->model('tagihan/tagihan_model');
        $tag_items = array();
        foreach ($items as $it) {
            $tag_items[] = array(
                'jenis_item' => 'OBAT', 'id_referensi' => (int) $it->id_obat,
                'nama_item' => $it->id_obat, 'jumlah' => (int) $it->jumlah,
                'harga' => (float) $it->harga,
            );
        }
        // nama_item diisi dari obat agar tagihan terbaca.
        foreach ($tag_items as &$ti) {
            $ob = $this->db->where('id_obat', $ti['id_referensi'])->get('obat')->row();
            $ti['nama_item'] = $ob ? $ob->nama_obat : ('Obat #' . $ti['id_referensi']);
        }
        unset($ti);
        $tagihan = $this->tagihan_model->buat(null, $tag_items);
        if (! $tagihan) {
            $this->db->trans_complete();
            $this->error = $this->tagihan_model->error ?: 'Gagal membuat tagihan.';
            return false;
        }
        $this->update($id_pesanan, array(
            'id_penjualan' => $jual['id_penjualan'],
            'id_tagihan' => $tagihan['id_tagihan'],
            'status' => 'DIPROSES',
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->error = 'Transaksi proses gagal.';
            return false;
        }
        return array('id_penjualan' => $jual['id_penjualan'], 'id_tagihan' => $tagihan['id_tagihan']);
    }

    /** Selaraskan status_bayar dari tagihan (dipanggil saat baca). */
    private function selaraskan_bayar($row)
    {
        if (empty($row->id_tagihan) || $row->status_bayar === 'LUNAS') {
            return;
        }
        $t = $this->db->where('id_tagihan', $row->id_tagihan)->get('tagihan')->row();
        if ($t && $t->status === 'LUNAS' && $row->status_bayar !== 'LUNAS') {
            $this->update($row->id_pesanan, array('status_bayar' => 'LUNAS', 'updated_at' => date('Y-m-d H:i:s')));
            $row->status_bayar = 'LUNAS';
        }
    }
}
