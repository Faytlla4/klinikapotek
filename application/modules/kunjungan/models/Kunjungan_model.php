<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Kunjungan Model (§8, §11).
 *
 * Tujuan pelayanan (pelayanan/poli/dokter/ruangan) ditentukan ADMIN/PELAYANAN,
 * bukan pasien. Membuat kunjungan otomatis membuat antrian (Menunggu).
 *
 * Status: TERDAFTAR -> MENUNGGU -> DIPROSES -> SELESAI (+BATAL).
 */
class Kunjungan_model extends BF_Model
{
    protected $table_name = 'kunjungan';
    protected $key = 'id_kunjungan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    /** Alur status yang diizinkan. */
    private $alur = array(
        'TERDAFTAR' => array('MENUNGGU', 'BATAL'),
        'MENUNGGU'  => array('DIPROSES', 'BATAL'),
        'DIPROSES'  => array('SELESAI', 'BATAL'),
        'SELESAI'   => array(),
        'BATAL'     => array(),
    );

    protected $validation_rules = array(
        array('field' => 'id_pasien', 'label' => 'Pasien', 'rules' => 'integer'),
        array('field' => 'id_pelayanan', 'label' => 'Pelayanan', 'rules' => 'integer'),
        array('field' => 'id_poli', 'label' => 'Poli', 'rules' => 'integer'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'integer'),
        array('field' => 'id_ruangan', 'label' => 'Ruangan', 'rules' => 'integer'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_pasien', 'label' => 'Pasien', 'rules' => 'required'),
        array('field' => 'id_pelayanan', 'label' => 'Pelayanan', 'rules' => 'required'),
        array('field' => 'id_poli', 'label' => 'Poli', 'rules' => 'required'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'required'),
        array('field' => 'id_ruangan', 'label' => 'Ruangan', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buat kunjungan + antrian dalam satu transaksi.
     *
     * @param array $data id_pasien, id_pelayanan, id_poli, id_dokter, id_ruangan
     * @return array|bool array(id_kunjungan, id_antrian, nomor_antrian) atau false.
     */
    public function daftar($data, $buat_antrian = true)
    {
        // Validasi relasi ke master (FK logis).
        $master = array(
            'pasien'    => array('input' => 'id_pasien', 'key' => 'id_pasien'),
            'pelayanan' => array('input' => 'id_pelayanan', 'key' => 'id_pelayanan'),
            'poli'      => array('input' => 'id_poli', 'key' => 'id_poli'),
            'dokter'    => array('input' => 'id_dokter', 'key' => 'id_dokter'),
            'ruangan'   => array('input' => 'id_ruangan', 'key' => 'id_ruangan'),
        );
        foreach ($master as $tabel => $relasi) {
            $kolom = $relasi['input'];
            if (empty($data[$kolom]) || ! $this->db->where($relasi['key'], $data[$kolom])->get($tabel)->row()) {
                $this->error = "Data {$tabel} tidak ditemukan.";
                return false;
            }
        }

        $this->db->trans_start();
        $data['tanggal_kunjungan'] = date('Y-m-d H:i:s');
        $data['status'] = 'TERDAFTAR';
        $id_kunjungan = $this->insert($data);

        $antrian = true;
        if ($buat_antrian) {
            $this->load->model('antrian/antrian_model');
            $antrian = $this->antrian_model->buat($id_kunjungan, $data['id_poli']);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false || ! $id_kunjungan || ! $antrian) {
            $this->error = 'Gagal membuat kunjungan/antrian.';
            return false;
        }
        return array(
            'id_kunjungan'  => $id_kunjungan,
            'id_antrian'    => $buat_antrian ? $antrian['id_antrian'] : null,
            'nomor_antrian' => $buat_antrian ? $antrian['nomor_antrian'] : null,
        );
    }

    /** Kunjungan + seluruh relasi (pasien, pelayanan, poli, dokter, ruangan). */
    public function detail($id_kunjungan)
    {
        $row = $this->db->select('kunjungan.*, pasien.no_rm, pasien.nama AS nama_pasien,
                pelayanan.nama_pelayanan, poli.nama_poli, dokter.nama_dokter, ruangan.nama_ruangan')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli')
            ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter')
            ->join('ruangan', 'ruangan.id_ruangan = kunjungan.id_ruangan')
            ->where('kunjungan.id_kunjungan', $id_kunjungan)
            ->get('kunjungan')
            ->row();
        return $row ?: false;
    }

    /**
     * Ubah status kunjungan mengikuti alur.
     *
     * @return bool
     */
    public function ubah_status($id_kunjungan, $status_baru)
    {
        $row = $this->find($id_kunjungan);
        if (! $row) {
            $this->error = 'Kunjungan tidak ditemukan.';
            return false;
        }
        $boleh = isset($this->alur[$row->status]) ? $this->alur[$row->status] : array();
        if (! in_array($status_baru, $boleh)) {
            $this->error = "Status {$row->status} tidak dapat berubah ke {$status_baru}.";
            return false;
        }
        if ($status_baru === 'SELESAI') {
            // Jangan menyelesaikan kunjungan secara terpisah dari pemeriksaan.
            // Pemeriksaan_model::selesaikan() juga menutup antrian dan menyusun
            // tagihan, sehingga data siap muncul di menu Transaksi/Pembayaran.
            $pemeriksaan = $this->db->where('id_kunjungan', $id_kunjungan)
                ->where('status !=', 'SELESAI')
                ->get('pemeriksaan')
                ->row();
            if ($pemeriksaan) {
                $this->load->model('pemeriksaan/pemeriksaan_model');
                if (! $this->pemeriksaan_model->selesaikan($pemeriksaan->id_pemeriksaan)) {
                    $this->error = $this->pemeriksaan_model->error ?: 'Gagal menyelesaikan pemeriksaan.';
                    return false;
                }
                return true;
            }
        }

        if ($status_baru !== 'BATAL') {
            return $this->update($id_kunjungan, array('status' => $status_baru));
        }

        // A cancellation must not leave a live queue entry behind. A visit
        // already being examined/completed cannot be cancelled by this path.
        $this->db->trans_start();
        $antrian = $this->db->where('id_kunjungan', $id_kunjungan)->get('antrian')->row();
        if ($antrian && ! in_array($antrian->status, array('MENUNGGU', 'DIPANGGIL', 'DILEWATI', 'BATAL'))) {
            $this->db->trans_complete();
            $this->error = 'Kunjungan yang sedang atau sudah diperiksa tidak dapat dibatalkan.';
            return false;
        }
        $updated = $this->update($id_kunjungan, array('status' => 'BATAL'));
        if ($updated && $antrian && $antrian->status !== 'BATAL') {
            $this->db->where('id_antrian', $antrian->id_antrian)->update('antrian', array('status' => 'BATAL'));
        }
        $this->db->trans_complete();
        if (! $updated || $this->db->trans_status() === false) {
            $this->error = 'Gagal membatalkan kunjungan.';
            return false;
        }
        return true;
    }

    /**
     * Dapatkan riwayat kunjungan pasien
     * 
     * @param int $id_pasien
     * @param int $limit
     * @return array
     */
    public function get_riwayat_by_pasien($id_pasien, $limit = 5)
    {
        return $this->db->select('kunjungan.*, pelayanan.nama_pelayanan, poli.nama_poli, dokter.nama_dokter, ruangan.nama_ruangan')
            ->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan', 'left')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli', 'left')
            ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter', 'left')
            ->join('ruangan', 'ruangan.id_ruangan = kunjungan.id_ruangan', 'left')
            ->where('kunjungan.id_pasien', $id_pasien)
            ->order_by('kunjungan.tanggal_kunjungan', 'DESC')
            ->limit($limit)
            ->get($this->table_name)
            ->result();
    }
}
