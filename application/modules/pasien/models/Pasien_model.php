<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pasien Model (Â§9).
 *
 * - no_rm UNIQUE, satu pasien satu Nomor Rekam Medis.
 * - Pencarian: NIK, nama, no_rm.
 */
class Pasien_model extends BF_Model
{
    protected $table_name = 'pasien';
    protected $key = 'id_pasien';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    // ponytail: required/is_unique hanya saat insert; update parsial tak kena required.
    protected $validation_rules = array(
        array('field' => 'nama', 'label' => 'Nama', 'rules' => 'max_length[150]'),
        array('field' => 'no_rm', 'label' => 'No. RM', 'rules' => 'max_length[30]'),
        array('field' => 'nik', 'label' => 'NIK', 'rules' => 'max_length[20]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama', 'label' => 'Nama', 'rules' => 'required'),
        array('field' => 'no_rm', 'label' => 'No. RM', 'rules' => 'required|is_unique[pasien.no_rm]'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate Nomor Rekam Medis baru yang unik.
     *
     * @return string
     */
    public function no_rm_baru()
    {
        $i = 0;
        do {
            if (++$i > 100) {
                // Pengaman: jangan pernah loop selamanya.
                return false;
            }
            $no_rm = nomor_baru('RM', 'pasien', 'no_rm');
        } while ($this->find_by('no_rm', $no_rm));
        return $no_rm;
    }

    /**
     * Buat pasien baru (no_rm otomatis bila kosong).
     *
     * @param array $data
     * @return int|bool id_pasien baru atau false.
     */
    public function daftar($data)
    {
        if (! empty($data['nik']) && $this->find_by('nik', $data['nik'])) {
            $this->error = 'NIK sudah terdaftar.';
            return false;
        }
        if (empty($data['no_rm'])) {
            $data['no_rm'] = $this->no_rm_baru();
            if ($data['no_rm'] === false) {
                $this->error = 'Gagal membuat Nomor RM.';
                return false;
            }
        }
        if (empty($data['status'])) {
            $data['status'] = 'AKTIF';
        }
        return $this->insert($data);
    }

    /** Cek NIK unik, mengabaikan pasien saat edit. */
    public function nik_tersedia($nik, $abaikan_id = null)
    {
        if (empty($nik)) {
            return true;
        }
        $this->db->where('nik', $nik);
        if ($abaikan_id) {
            $this->db->where('id_pasien !=', $abaikan_id);
        }
        return ! $this->db->get($this->table_name)->row();
    }

    /**
     * Cari pasien berdasarkan NIK / nama / no_rm.
     *
     * @param string $keyword
     * @param string $by nik|nama|no_rm (null = semua kolom)
     * @return array
     */
    public function cari($keyword, $by = null)
    {
        $keyword = trim($keyword);
        if ($by === 'nik') {
            return $this->where('nik', $keyword)->find_all() ?: array();
        }
        if ($by === 'no_rm') {
            return $this->where('no_rm', $keyword)->find_all() ?: array();
        }
        if ($by === 'nama') {
            return $this->where("nama ILIKE '%" . $this->db->escape_like_str($keyword) . "%'", NULL, FALSE)->find_all() ?: array();
        }
        $rows = $this->db->where('nik', $keyword)
            ->or_where('no_rm', $keyword)
            ->or_where("nama ILIKE '%" . $this->db->escape_like_str($keyword) . "%'", NULL, FALSE)
            ->get($this->table_name)
            ->result();
        return $rows ?: array();
    }

    /**
     * Detail pasien + riwayat kunjungan.
     *
     * @param int $id_pasien
     * @return object|bool
     */
    public function detail($id_pasien)
    {
        $pasien = $this->find($id_pasien);
        if (! $pasien) {
            return false;
        }
        $pasien->kunjungan = $this->db->select('kunjungan.*, pelayanan.nama_pelayanan, poli.nama_poli')
            ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan', 'left')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli', 'left')
            ->where('kunjungan.id_pasien', $id_pasien)
            ->order_by('kunjungan.tanggal_kunjungan', 'DESC')
            ->get('kunjungan')
            ->result();
        return $pasien;
    }
}

