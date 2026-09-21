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
        array('field' => 'nik', 'label' => 'NIK', 'rules' => 'max_length[30]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama', 'label' => 'Nama', 'rules' => 'required'),
        array('field' => 'no_rm', 'label' => 'No. RM', 'rules' => 'required'),
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
        // PostgreSQL UNIQUE menganggap string kosong sebagai nilai nyata.
        // NIK bersifat opsional, jadi representasikan input kosong sebagai NULL
        // agar lebih dari satu pasien tanpa NIK tetap dapat disimpan.
        $data['nik'] = $this->normalisasi_nik(isset($data['nik']) ? $data['nik'] : null);
        if (! $this->nik_valid($data['nik'])) {
            return false;
        }
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
        $id = $this->insert($data);
        if (! $id && $this->nik_duplicate_error()) {
            $this->error = 'NIK sudah terdaftar.';
        }
        return $id;
    }

    /** Normalisasi NIK opsional sebelum disimpan ke kolom UNIQUE. */
    private function normalisasi_nik($nik)
    {
        $nik = trim((string) $nik);
        return $nik === '' ? null : $nik;
    }

    /**
     * NIK adalah identifier string. Input boleh kosong, tetapi jika diisi
     * harus tepat 16 digit agar nol di depan tetap dipertahankan.
     */
    public function nik_valid($nik)
    {
        $nik = $this->normalisasi_nik($nik);
        if ($nik === null) {
            return true;
        }
        if (! preg_match('/^[0-9]{16}$/D', $nik)) {
            $this->error = 'NIK harus terdiri dari 16 digit angka.';
            return false;
        }
        return true;
    }

    private function nik_duplicate_error()
    {
        return stripos((string) $this->error, 'pasien_nik_key') !== false
            || stripos((string) $this->error, 'duplicate key') !== false
            || stripos((string) $this->error, 'unique constraint') !== false;
    }

    /**
     * Pastikan jalur API maupun form edit juga tidak menyimpan NIK kosong
     * sebagai string kosong.
     */
    public function update($where = null, $data = null)
    {
        if (is_array($data) && array_key_exists('nik', $data)) {
            $data['nik'] = $this->normalisasi_nik($data['nik']);
            if (! $this->nik_valid($data['nik'])) {
                return false;
            }
        }
        $updated = parent::update($where, $data);
        if (! $updated && $this->nik_duplicate_error()) {
            $this->error = 'NIK sudah terdaftar.';
        }
        return $updated;
    }

    /** Cek NIK unik, mengabaikan pasien saat edit. */
    public function nik_tersedia($nik, $abaikan_id = null)
    {
        $nik = $this->normalisasi_nik($nik);
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
            return $this->where("nama LIKE '%" . $this->db->escape_like_str($keyword) . "%'", NULL, FALSE)->find_all() ?: array();
        }
        $rows = $this->db->where('nik', $keyword)
            ->or_where('no_rm', $keyword)
            ->or_where("nama LIKE '%" . $this->db->escape_like_str($keyword) . "%'", NULL, FALSE)
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
        $pasien->pemeriksaan = $this->db->select('pemeriksaan.*, dokter.nama_dokter')
            ->join('kunjungan', 'kunjungan.id_kunjungan = pemeriksaan.id_kunjungan')
            ->join('dokter', 'dokter.id_dokter = pemeriksaan.id_dokter', 'left')
            ->where('kunjungan.id_pasien', $id_pasien)
            ->order_by('pemeriksaan.tanggal_pemeriksaan', 'DESC')
            ->get('pemeriksaan')
            ->result();
        $pasien->resep = $this->db->select('resep.*, dokter.nama_dokter')
            ->join('dokter', 'dokter.id_dokter = resep.id_dokter', 'left')
            ->where('resep.id_pasien', $id_pasien)
            ->order_by('resep.tanggal_resep', 'DESC')
            ->get('resep')
            ->result();
        $pasien->transaksi = $this->db->select('transaksi.*, tagihan.nomor_tagihan')
            ->join('tagihan', 'tagihan.id_tagihan = transaksi.id_tagihan')
            ->join('kunjungan', 'kunjungan.id_kunjungan = tagihan.id_kunjungan')
            ->where('kunjungan.id_pasien', $id_pasien)
            ->order_by('transaksi.tanggal_transaksi', 'DESC')
            ->get('transaksi')
            ->result();
        return $pasien;
    }
}
