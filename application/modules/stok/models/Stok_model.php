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
        $row = $this->db->query('SELECT * FROM stok_obat WHERE id_obat = ? FOR UPDATE', array($id_obat))->row();
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

    /** Riwayat mutasi sebuah obat (opsional saring jenis + rentang tanggal). */
    public function riwayat($id_obat, $limit = 50, $dari = null, $sampai = null, $jenis = null)
    {
        $this->db->where('id_obat', $id_obat);
        if ($jenis === 'MASUK' || $jenis === 'KELUAR') {
            $this->db->where('jenis_mutasi', $jenis);
        }
        if (is_string($dari) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
            $this->db->where('tanggal >=', $dari . ' 00:00:00');
        }
        if (is_string($sampai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
            $this->db->where('tanggal <=', $sampai . ' 23:59:59');
        }
        return $this->db->order_by('tanggal', 'DESC')
            ->limit((int) $limit)
            ->get('mutasi_stok')
            ->result();
    }

    /** Obat dengan stok di bawah minimum (peringatan pengadaan). */
    public function di_bawah_minimum()
    {
        return $this->db->select('obat.*, COALESCE(stok_obat.jumlah_stok, 0) AS stok')
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('COALESCE(stok_obat.jumlah_stok, 0) > 0', null, false)
            ->where('COALESCE(stok_obat.jumlah_stok, 0) <= obat.stok_minimum', null, false)
            ->where('obat.status', 'AKTIF')
            ->get('obat')
            ->result();
    }

    /** Semua obat aktif dengan klasifikasi stok yang dipakai dashboard apotek. */
    public function peringatan_dashboard()
    {
        $rows = $this->db->select("obat.id_obat, obat.kode_obat, obat.nama_obat,
                obat.satuan, obat.stok_minimum,
                COALESCE(stok_obat.jumlah_stok, 0) AS stok,
                CASE
                    WHEN COALESCE(stok_obat.jumlah_stok, 0) = 0 THEN 'HABIS'
                    WHEN COALESCE(stok_obat.jumlah_stok, 0) <= obat.stok_minimum THEN 'MENIPIS'
                    ELSE 'AMAN'
                END AS status_stok", false)
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('obat.status', 'AKTIF')
            ->where('(COALESCE(stok_obat.jumlah_stok, 0) <= obat.stok_minimum)', null, false)
            ->order_by('stok', 'ASC')
            ->order_by('obat.nama_obat', 'ASC')
            ->get('obat')
            ->result();

        return $rows ?: array();
    }

    /** Ringkasan stok nyata untuk kartu dashboard. */
    public function ringkasan_dashboard()
    {
        $row = $this->db->select("COUNT(*) FILTER (WHERE COALESCE(stok_obat.jumlah_stok, 0) > 0
                    AND COALESCE(stok_obat.jumlah_stok, 0) <= obat.stok_minimum) AS menipis,
                COUNT(*) FILTER (WHERE COALESCE(stok_obat.jumlah_stok, 0) = 0) AS habis", false)
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left')
            ->where('obat.status', 'AKTIF')
            ->get('obat')
            ->row();

        return array(
            'menipis' => $row ? (int) $row->menipis : 0,
            'habis' => $row ? (int) $row->habis : 0,
        );
    }

    /** Obat masuk yang memiliki tanggal kedaluwarsa, dikelompokkan per penerimaan/batch. */
    public function peringatan_kedaluwarsa($hari = 30)
    {
        // Database lama mungkin belum menjalankan migration 008. Jangan
        // membuat seluruh dashboard gagal hanya karena fitur opsional ini
        // belum tersedia.
        if (! $this->kolom_kedaluwarsa_tersedia()) {
            return array();
        }

        $hari = max(1, (int) $hari);
        $today = date('Y-m-d');
        $batas = date('Y-m-d', strtotime($today . ' +' . $hari . ' days'));
        $escaped_today = $this->db->escape($today);
        $escaped_batas = $this->db->escape($batas);
        $rows = $this->db->select("obat.nama_obat, obat.kode_obat, obat.satuan,
                penerimaan_obat_detail.id_detail,
                penerimaan_obat_detail.id_penerimaan,
                penerimaan_obat_detail.id_obat,
                penerimaan_obat_detail.nomor_batch,
                penerimaan_obat_detail.tanggal_kadaluarsa,
                penerimaan_obat_detail.jumlah_terima AS stok,
                (penerimaan_obat_detail.tanggal_kadaluarsa::date - {$escaped_today}::date) AS sisa_hari,
                CASE
                    WHEN penerimaan_obat_detail.tanggal_kadaluarsa < {$escaped_today}::date THEN 'SUDAH KEDALUWARSA'
                    WHEN penerimaan_obat_detail.tanggal_kadaluarsa <= {$escaped_batas}::date THEN 'SEGERA KEDALUWARSA'
                    ELSE 'MASIH AMAN'
                END AS status_expired", false)
            ->join('obat', 'obat.id_obat = penerimaan_obat_detail.id_obat')
            ->join('penerimaan_obat', 'penerimaan_obat.id_penerimaan = penerimaan_obat_detail.id_penerimaan')
            ->where('obat.status', 'AKTIF')
            ->where('penerimaan_obat_detail.tanggal_kadaluarsa IS NOT NULL', null, false)
            ->where('penerimaan_obat_detail.tanggal_kadaluarsa <=', $batas)
            ->where('penerimaan_obat_detail.jumlah_terima >', 0)
            ->order_by('penerimaan_obat_detail.tanggal_kadaluarsa', 'ASC')
            ->order_by('obat.nama_obat', 'ASC')
            ->get('penerimaan_obat_detail')
            ->result();

        return $rows ?: array();
    }

    /**
     * Proses tindakan apoteker terhadap obat kedaluwarsa (RETUR, PEMUSNAHAN, TUNDA).
     *
     * @param int $id_detail ID detail penerimaan obat
     * @param string $jenis_tindakan RETUR | PEMUSNAHAN | TUNDA
     * @param int $jumlah Jumlah yang ditindak
     * @param string $keterangan Keterangan tambahan
     * @param int $user_id ID user yang melakukan tindakan
     * @return bool
     */
    public function proses_tindakan_expired($id_detail, $jenis_tindakan, $jumlah = 0, $keterangan = '', $user_id = 0)
    {
        $id_detail = (int) $id_detail;
        $jenis_tindakan = strtoupper(trim($jenis_tindakan));
        $jumlah = (int) $jumlah;

        if ($id_detail <= 0) {
            $this->error = 'ID penerimaan detail tidak valid.';
            return false;
        }

        if (! in_array($jenis_tindakan, array('RETUR', 'PEMUSNAHAN', 'TUNDA'), true)) {
            $this->error = 'Jenis tindakan tidak valid. Pilih RETUR, PEMUSNAHAN, atau TUNDA.';
            return false;
        }

        $detail = $this->db->select('penerimaan_obat_detail.*, obat.nama_obat')
            ->join('obat', 'obat.id_obat = penerimaan_obat_detail.id_obat')
            ->where('id_detail', $id_detail)
            ->get('penerimaan_obat_detail')
            ->row();

        if (! $detail) {
            $this->error = 'Data detail penerimaan obat tidak ditemukan.';
            return false;
        }

        if ($jenis_tindakan === 'TUNDA') {
            $this->load->model('audit/audit_log_model');
            $this->audit_log_model->catat($user_id, 'update', 'stok_obat', $detail->id_obat, "Tunda tindakan obat expired batch " . ($detail->nomor_batch ?: '-'));
            return true;
        }

        if ($jumlah <= 0) {
            $this->error = 'Jumlah obat yang ditindak harus lebih besar dari 0.';
            return false;
        }

        if ($jumlah > (int) $detail->jumlah_terima) {
            $this->error = "Jumlah ditindak ({$jumlah}) melebihi stok batch ({$detail->jumlah_terima}).";
            return false;
        }

        $this->db->trans_start();

        $tipe_mutasi = ($jenis_tindakan === 'RETUR') ? 'RETUR' : 'PEMUSNAHAN';
        $catatan = "Tindakan {$jenis_tindakan} obat expired (Batch: " . ($detail->nomor_batch ?: '-') . "). " . $keterangan;
        
        if (! $this->keluar($detail->id_obat, $jumlah, $tipe_mutasi, $id_detail, $catatan)) {
            $this->db->trans_rollback();
            return false;
        }

        $sisa_batch = max(0, (int) $detail->jumlah_terima - $jumlah);
        $this->db->where('id_detail', $id_detail)->update('penerimaan_obat_detail', array('jumlah_terima' => $sisa_batch));

        $this->load->model('audit/audit_log_model');
        $this->audit_log_model->catat($user_id, 'update', 'stok_obat', $detail->id_obat, "Tindakan {$jenis_tindakan} sebanyak {$jumlah} item obat {$detail->nama_obat}");

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Apakah database sudah memiliki sumber data batch/kedaluwarsa. */
    public function kolom_kedaluwarsa_tersedia()
    {
        return $this->db->field_exists('nomor_batch', 'penerimaan_obat_detail')
            && $this->db->field_exists('tanggal_kadaluarsa', 'penerimaan_obat_detail');
    }
}
