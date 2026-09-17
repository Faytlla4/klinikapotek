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
}
