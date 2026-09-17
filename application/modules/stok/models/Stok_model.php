<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Stok Model (§14).
 *
 * Mesin mutasi stok: setiap perubahan jumlah_stok WAJIB mencatat mutasi_stok.
 * - keluar(): cek stok cukup -> kurangi -> mutasi KELUAR (sumber: RESEP/PENJUALAN).
 * - masuk(): tambah (upsert baris stok) -> mutasi MASUK (sumber: PENGADAAN/RETUR?).
 * Semua dalam transaksi.
 */
class Stok_model extends BF_Model
{
    protected $table_name = 'stok_obat';
    protected $key = 'id_stok';
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

    /** Stok saat ini sebuah obat (0 bila baris belum ada). */
    public function posisi($id_obat)
    {
        $row = $this->db->query('SELECT * FROM stok_obat WHERE id_obat = ? FOR UPDATE', array($id_obat))->row();
        return $row ? (int) $row->jumlah_stok : 0;
    }

    /**
     * Obat keluar (resep/penjualan langsung). Gagal bila stok tak cukup.
     *
     * @param int    $id_obat
     * @param int    $jumlah
     * @param string $sumber      RESEP|PENJUALAN
     * @param int    $id_referensi id_resep / id_penjualan
     * @return bool
     */
    public function keluar($id_obat, $jumlah, $sumber, $id_referensi = null)
    {
        if ($jumlah <= 0) {
            $this->error = 'Jumlah harus positif.';
            return false;
        }
        $this->db->trans_start();
        $row = $this->db->query('SELECT * FROM stok_obat WHERE id_obat = ? FOR UPDATE', array($id_obat))->row();
        $stok = $row ? (int) $row->jumlah_stok : 0;
        if ($stok < $jumlah) {
            $this->db->trans_complete();
            $this->error = "Stok tidak cukup (tersedia {$stok}, diminta {$jumlah}).";
            return false;
        }
        $this->db->where('id_obat', $id_obat)->update('stok_obat', array(
            'jumlah_stok' => $stok - $jumlah,
            'updated_at'  => date('Y-m-d H:i:s'),
        ));
        $this->catat($id_obat, 'KELUAR', $jumlah, $sumber, $id_referensi);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Obat masuk (penerimaan pengadaan). Baris stok dibuat bila belum ada.
     *
     * @return bool
     */
    public function masuk($id_obat, $jumlah, $sumber, $id_referensi = null, $keterangan = '')
    {
        if ($jumlah <= 0) {
            $this->error = 'Jumlah harus positif.';
            return false;
        }
        $this->db->trans_start();
        $row = $this->db->where('id_obat', $id_obat)->get('stok_obat')->row();
        if ($row) {
            $this->db->where('id_obat', $id_obat)->update('stok_obat', array(
                'jumlah_stok' => (int) $row->jumlah_stok + $jumlah,
                'updated_at'  => date('Y-m-d H:i:s'),
            ));
        } else {
            $this->db->insert('stok_obat', array(
                'id_obat'     => $id_obat,
                'jumlah_stok' => $jumlah,
                'updated_at'  => date('Y-m-d H:i:s'),
            ));
        }
        $this->catat($id_obat, 'MASUK', $jumlah, $sumber, $id_referensi, $keterangan);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Tulis baris mutasi_stok (dipanggil di dalam transaksi). */
    private function catat($id_obat, $jenis, $jumlah, $sumber, $id_referensi, $keterangan = '')
    {
        $this->db->insert('mutasi_stok', array(
            'id_obat'      => $id_obat,
            'jenis_mutasi' => $jenis,
            'jumlah'       => $jumlah,
            'tanggal'      => date('Y-m-d H:i:s'),
            'sumber'       => $sumber,
            'id_referensi' => $id_referensi,
            'keterangan'   => $keterangan,
        ));
    }

    /** Riwayat mutasi sebuah obat. */
    public function riwayat($id_obat, $limit = 50)
    {
        return $this->db->where('id_obat', $id_obat)
            ->order_by('tanggal', 'DESC')
            ->limit($limit)
            ->get('mutasi_stok')
            ->result();
    }

    /** Obat dengan stok di bawah minimum (peringatan pengadaan). */
    public function di_bawah_minimum()
    {
        return $this->db->select('obat.*, COALESCE(stok_obat.jumlah_stok, 0) AS stok')
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('COALESCE(stok_obat.jumlah_stok, 0) < obat.stok_minimum')
            ->where('obat.status', 'AKTIF')
            ->get('obat')
            ->result();
    }
}
