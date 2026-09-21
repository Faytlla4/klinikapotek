<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Laporan Model (read-only agregat, §laporan).
 *
 * Semua method menerima rentang tanggal (Y-m-d) dan mengembalikan array.
 */
class Laporan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /** Ringkasan kunjungan per status dalam periode. */
    public function kunjungan($dari, $sampai)
    {
        return $this->db->select("status, COUNT(*) AS jumlah", false)
            ->where('tanggal_kunjungan >=', $dari . ' 00:00:00')
            ->where('tanggal_kunjungan <=', $sampai . ' 23:59:59')
            ->group_by('status')
            ->get('kunjungan')
            ->result();
    }

    /** Pendapatan: total transaksi Selesai + total pembayaran Lunas per hari. */
    public function pendapatan($dari, $sampai)
    {
        return $this->db->select("DATE(tanggal_transaksi) AS tanggal,
                SUM(CASE WHEN status = 'LUNAS' THEN total ELSE 0 END) AS omzet", false)
            ->where('tanggal_transaksi >=', $dari . ' 00:00:00')
            ->where('tanggal_transaksi <=', $sampai . ' 23:59:59')
            ->group_by('DATE(tanggal_transaksi)')
            ->order_by('tanggal', 'ASC')
            ->get('transaksi')
            ->result();
    }

    /** Penjualan obat per item dalam periode. */
    public function penjualan_obat($dari, $sampai)
    {
        return $this->db->select('obat.kode_obat, obat.nama_obat,
                SUM(penjualan_obat_detail.jumlah) AS qty,
                SUM(penjualan_obat_detail.subtotal) AS omzet', false)
            ->join('penjualan_obat_detail', 'penjualan_obat_detail.id_penjualan = penjualan_obat.id_penjualan')
            ->join('obat', 'obat.id_obat = penjualan_obat_detail.id_obat')
            ->where('penjualan_obat.tanggal_penjualan >=', $dari . ' 00:00:00')
            ->where('penjualan_obat.tanggal_penjualan <=', $sampai . ' 23:59:59')
            ->where('penjualan_obat.status', 'SELESAI')
            ->group_by(array('obat.kode_obat', 'obat.nama_obat'))
            ->order_by('omzet', 'DESC')
            ->get('penjualan_obat')
            ->result();
    }

    /** Resep per status dalam periode. */
    public function resep($dari, $sampai)
    {
        return $this->db->select("status, COUNT(*) AS jumlah", false)
            ->where('tanggal_resep >=', $dari . ' 00:00:00')
            ->where('tanggal_resep <=', $sampai . ' 23:59:59')
            ->group_by('status')
            ->get('resep')
            ->result();
    }

    /** Mutasi stok per jenis dalam periode. */
    public function mutasi_stok($dari, $sampai)
    {
        return $this->db->select('obat.nama_obat, mutasi_stok.jenis_mutasi,
                SUM(mutasi_stok.jumlah) AS qty', false)
            ->join('obat', 'obat.id_obat = mutasi_stok.id_obat')
            ->where('mutasi_stok.tanggal >=', $dari . ' 00:00:00')
            ->where('mutasi_stok.tanggal <=', $sampai . ' 23:59:59')
            ->group_by(array('obat.nama_obat', 'mutasi_stok.jenis_mutasi'))
            ->order_by('obat.nama_obat', 'ASC')
            ->get('mutasi_stok')
            ->result();
    }

    /** Posisi stok terkini per obat (snapshot; abaikan rentang tanggal). */
    public function stok($dari, $sampai)
    {
        return $this->db->select('obat.kode_obat, obat.nama_obat, obat.satuan,
                COALESCE(stok_obat.jumlah_stok, 0) AS stok, obat.stok_minimum', false)
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('obat.status', 'AKTIF')
            ->order_by('obat.nama_obat', 'ASC')
            ->get('obat')
            ->result();
    }

    /** Daftar kunjungan detail dalam periode (untuk cetak). */
    public function cetak_kunjungan($dari, $sampai)
    {
        return $this->db->select('kunjungan.tanggal_kunjungan, pasien.no_rm, pasien.nama AS nama_pasien,
                pelayanan.nama_pelayanan, poli.nama_poli, dokter.nama_dokter, kunjungan.status', false)
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan', 'left')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli', 'left')
            ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter', 'left')
            ->where('kunjungan.tanggal_kunjungan >=', $dari . ' 00:00:00')
            ->where('kunjungan.tanggal_kunjungan <=', $sampai . ' 23:59:59')
            ->order_by('kunjungan.tanggal_kunjungan', 'ASC')
            ->get('kunjungan')
            ->result();
    }

    /** Daftar transaksi detail dalam periode (untuk cetak). */
    public function cetak_transaksi($dari, $sampai)
    {
        return $this->db->select('transaksi.nomor_transaksi, transaksi.tanggal_transaksi,
                transaksi.total, transaksi.status', false)
            ->where('transaksi.tanggal_transaksi >=', $dari . ' 00:00:00')
            ->where('transaksi.tanggal_transaksi <=', $sampai . ' 23:59:59')
            ->order_by('transaksi.tanggal_transaksi', 'ASC')
            ->get('transaksi')
            ->result();
    }

    /** Daftar antrian detail dalam periode (untuk cetak). */
    public function cetak_antrian($dari, $sampai)
    {
        return $this->db->select('antrian.nomor_antrian, antrian.tanggal_antrian,
                poli.nama_poli, pasien.nama AS nama_pasien, antrian.status', false)
            ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli', 'left')
            ->where('antrian.tanggal_antrian >=', $dari)
            ->where('antrian.tanggal_antrian <=', $sampai)
            ->order_by('antrian.tanggal_antrian', 'ASC')
            ->order_by('antrian.nomor_antrian', 'ASC')
            ->get('antrian')
            ->result();
    }

    /** Daftar pendaftaran pasien baru dalam periode (untuk cetak). */
    public function cetak_pendaftaran($dari, $sampai)
    {
        return $this->db->select('no_rm, nama, nik, created_at', false)
            ->where('created_at >=', $dari . ' 00:00:00')
            ->where('created_at <=', $sampai . ' 23:59:59')
            ->order_by('created_at', 'ASC')
            ->get('pasien')
            ->result();
    }
}
