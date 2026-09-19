<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Tagihan Model (§17).
 *
 * Relasi: tagihan -> kunjungan; tagihan_detail (jenis_item: TINDAKAN|OBAT|
 * PELAYANAN|DOKTER, id_referensi, nama_item, jumlah, harga, subtotal).
 * Status: Belum bayar -> Lunas (+Batal; hanya bila Belum bayar).
 */
class Tagihan_model extends BF_Model
{
    protected $table_name = 'tagihan';
    protected $key = 'id_tagihan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array();
    protected $insert_validation_rules = array();
    protected $skip_validation = true;

    public function __construct()
    {
        parent::__construct();
    }

    /** Tagihan belum lunas + info pasien (halaman Pembayaran). */
    public function belum_lunas()
    {
        return $this->db->select("tagihan.*, pasien.no_rm, pasien.nama AS nama_pasien,
                COALESCE((SELECT SUM(pembayaran.jumlah_bayar) FROM pembayaran
                    JOIN transaksi ON transaksi.id_transaksi = pembayaran.id_transaksi
                    WHERE transaksi.id_tagihan = tagihan.id_tagihan), 0) AS sudah_dibayar", false)
            ->join('kunjungan', 'kunjungan.id_kunjungan = tagihan.id_kunjungan', 'left')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien', 'left')
            ->where('tagihan.status', 'BELUM_DIBAYAR')
            ->order_by('tagihan.id_tagihan', 'DESC')
            ->get('tagihan')
            ->result();
    }

    /**
     * Susun tagihan kunjungan otomatis: tarif pelayanan + tarif dokter +
     * total tindakan + total penjualan obat terkait kunjungan.
     *
     * @param int $id_kunjungan
     * @return array|bool array(id_tagihan, nomor_tagihan, total) atau false.
     */
    public function susun_dari_kunjungan($id_kunjungan)
    {
        $k = $this->db->select('kunjungan.*, pelayanan.nama_pelayanan, pelayanan.tarif AS tarif_pelayanan,
                dokter.nama_dokter, dokter.tarif AS tarif_dokter')
            ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan')
            ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter')
            ->where('kunjungan.id_kunjungan', $id_kunjungan)
            ->get('kunjungan')->row();
        if (! $k) {
            $this->error = 'Kunjungan tidak ditemukan.';
            return false;
        }
        $ada = $this->db->where('id_kunjungan', $id_kunjungan)
            ->where('status !=', 'BATAL')->get('tagihan')->row();
        if ($ada) {
            $this->error = 'Kunjungan sudah memiliki tagihan aktif.';
            return false;
        }

        $items = array();
        $items[] = array('jenis_item' => 'PELAYANAN', 'id_referensi' => $k->id_pelayanan,
            'nama_item' => $k->nama_pelayanan, 'jumlah' => 1, 'harga' => (float) $k->tarif_pelayanan);
        // ponytail: jenis_item DB hanya PELAYANAN|TINDAKAN|OBAT, jasa dokter dicatat sebagai TINDAKAN.
        $items[] = array('jenis_item' => 'TINDAKAN', 'id_referensi' => $k->id_dokter,
            'nama_item' => 'Jasa ' . $k->nama_dokter, 'jumlah' => 1, 'harga' => (float) $k->tarif_dokter);

        $tindakan = $this->db->select('tindakan.*, pemeriksaan.id_kunjungan')
            ->join('pemeriksaan', 'pemeriksaan.id_pemeriksaan = tindakan.id_pemeriksaan')
            ->where('pemeriksaan.id_kunjungan', $id_kunjungan)
            ->get('tindakan')->result();
        foreach ($tindakan as $t) {
            $items[] = array('jenis_item' => 'TINDAKAN', 'id_referensi' => $t->id_tindakan,
                'nama_item' => $t->nama_tindakan, 'jumlah' => 1, 'harga' => (float) $t->biaya);
        }

        $jual = $this->db->select('penjualan_obat_detail.*, penjualan_obat.id_pasien')
            ->join('penjualan_obat', 'penjualan_obat.id_penjualan = penjualan_obat_detail.id_penjualan')
            ->join('resep', 'resep.id_resep = penjualan_obat.id_resep')
            ->join('pemeriksaan', 'pemeriksaan.id_pemeriksaan = resep.id_pemeriksaan')
            ->where('pemeriksaan.id_kunjungan', $id_kunjungan)
            ->where('penjualan_obat.status', 'SELESAI')
            ->get('penjualan_obat_detail')->result();
        foreach ($jual as $j) {
            $obat = $this->db->select('nama_obat')->where('id_obat', $j->id_obat)->get('obat')->row();
            $items[] = array('jenis_item' => 'OBAT', 'id_referensi' => $j->id_detail,
                'nama_item' => $obat ? $obat->nama_obat : "Obat #{$j->id_obat}",
                'jumlah' => (int) $j->jumlah, 'harga' => (float) $j->harga);
        }

        return $this->buat($id_kunjungan, $items);
    }

    /**
     * Kirim penjualan resep ke tagihan kunjungan pasien.
     * Jika tagihan belum ada, seluruh tagihan kunjungan disusun; jika sudah
     * ada dan masih belum dibayar, hanya detail obat dari penjualan ini yang
     * ditambahkan.
     */
    public function tambahkan_penjualan_resep($id_penjualan)
    {
        $penjualan = $this->db->select('penjualan_obat.id_penjualan, pemeriksaan.id_kunjungan')
            ->join('resep', 'resep.id_resep = penjualan_obat.id_resep')
            ->join('pemeriksaan', 'pemeriksaan.id_pemeriksaan = resep.id_pemeriksaan')
            ->where('penjualan_obat.id_penjualan', $id_penjualan)
            ->get('penjualan_obat')->row();
        if (! $penjualan) {
            $this->error = 'Penjualan resep tidak ditemukan.';
            return false;
        }

        $tagihan = $this->db->where('id_kunjungan', $penjualan->id_kunjungan)
            ->where('status !=', 'BATAL')->get('tagihan')->row();
        if (! $tagihan) {
            return $this->susun_dari_kunjungan($penjualan->id_kunjungan);
        }
        if ($tagihan->status !== 'BELUM_DIBAYAR') {
            $this->error = 'Tagihan kunjungan sudah lunas; obat tidak dapat ditambahkan.';
            return false;
        }

        $details = $this->db->select('penjualan_obat_detail.*, obat.nama_obat')
            ->join('obat', 'obat.id_obat = penjualan_obat_detail.id_obat')
            ->where('id_penjualan', $id_penjualan)->get('penjualan_obat_detail')->result();
        $tambahan = 0;
        foreach ($details as $detail) {
            $subtotal = (float) $detail->subtotal;
            $this->db->insert('tagihan_detail', array(
                'id_tagihan' => $tagihan->id_tagihan, 'jenis_item' => 'OBAT',
                'id_referensi' => $detail->id_detail, 'nama_item' => $detail->nama_obat,
                'jumlah' => (int) $detail->jumlah, 'harga' => (float) $detail->harga,
                'subtotal' => $subtotal,
            ));
            $tambahan += $subtotal;
        }
        if (! empty($details)) {
            $this->db->where('id_tagihan', $tagihan->id_tagihan)->update('tagihan', array(
                'total' => (float) $tagihan->total + $tambahan,
            ));
        }
        return array('id_tagihan' => $tagihan->id_tagihan, 'total' => (float) $tagihan->total + $tambahan);
    }

    /**
     * Buat tagihan dari daftar item.
     *
     * @return array|bool
     */
    public function buat($id_kunjungan, $items)
    {
        if ($id_kunjungan && ! $this->db->where('id_kunjungan', $id_kunjungan)->get('kunjungan')->row()) {
            $this->error = 'Kunjungan tidak ditemukan.';
            return false;
        }
        if (empty($items)) {
            $this->error = 'Item tagihan kosong.';
            return false;
        }
        $this->db->trans_start();
        $total = 0;
        $rows = array();
        foreach ($items as $item) {
            if (empty($item['jenis_item']) || (int) $item['jumlah'] <= 0 || (float) $item['harga'] < 0) {
                $this->db->trans_complete();
                $this->error = 'Item tagihan tidak valid.';
                return false;
            }
            $subtotal = (float) $item['harga'] * (int) $item['jumlah'];
            $total += $subtotal;
            $rows[] = array(
                'jenis_item' => $item['jenis_item'], 'id_referensi' => $item['id_referensi'],
                'nama_item' => $item['nama_item'], 'jumlah' => (int) $item['jumlah'],
                'harga' => (float) $item['harga'], 'subtotal' => $subtotal,
            );
        }
        $nomor = nomor_baru('TG', 'tagihan', 'nomor_tagihan');
        $id = $this->insert(array(
            'id_kunjungan' => $id_kunjungan, 'nomor_tagihan' => $nomor,
            'tanggal_tagihan' => date('Y-m-d H:i:s'), 'total' => $total, 'status' => 'BELUM_DIBAYAR',
        ));
        if ($id) {
            foreach ($rows as &$r) {
                $r['id_tagihan'] = $id;
            }
            $this->db->insert_batch('tagihan_detail', $rows);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false || ! $id) {
            $this->error = 'Gagal membuat tagihan.';
            return false;
        }
        return array('id_tagihan' => $id, 'nomor_tagihan' => $nomor, 'total' => $total);
    }

    /** Detail tagihan + item + total sudah dibayar. */
    public function detail($id_tagihan)
    {
        $row = $this->find($id_tagihan);
        if (! $row) {
            return false;
        }
        $row->items = $this->db->where('id_tagihan', $id_tagihan)->get('tagihan_detail')->result();
        $bayar = $this->db->select_sum('jumlah_bayar', 'dibayar')
            ->where('id_transaksi IN (SELECT id_transaksi FROM transaksi WHERE id_tagihan = ' . (int) $id_tagihan . ')', null, false)
            ->get('pembayaran')->row();
        $row->sudah_dibayar = $bayar ? (float) $bayar->dibayar : 0;
        $row->sisa = (float) $row->total - $row->sudah_dibayar;
        return $row;
    }

    /** Batalkan tagihan yang belum dibayar. */
    public function batalkan($id_tagihan)
    {
        $row = $this->find($id_tagihan);
        if (! $row) {
            $this->error = 'Tagihan tidak ditemukan.';
            return false;
        }
        if ($row->status !== 'BELUM_DIBAYAR') {
            $this->error = "Hanya tagihan BELUM_DIBAYAR yang dapat dibatalkan (saat ini {$row->status}).";
            return false;
        }
        return $this->update($id_tagihan, array('status' => 'BATAL'));
    }
}
