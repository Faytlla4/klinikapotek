<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Antrian Model (§12).
 *
 * Relasi: antrian -> kunjungan. Nomor antrian unik per poli per hari.
 *
 * Status: MENUNGGU -> DIPANGGIL -> SEDANG_DIPERIKSA -> SELESAI
 *         (+ DILEWATI dari DIPANGGIL, BATAL dari MENUNGGU/DIPANGGIL).
 */
class Antrian_model extends BF_Model
{
    protected $table_name = 'antrian';
    protected $key = 'id_antrian';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    private $alur = array(
        'MENUNGGU'        => array('DIPANGGIL', 'BATAL'),
        'DIPANGGIL'        => array('SEDANG_DIPERIKSA', 'DILEWATI', 'BATAL'),
        'DILEWATI'         => array('DIPANGGIL', 'BATAL'),
        'SEDANG_DIPERIKSA' => array('SELESAI'),
        'SELESAI'          => array(),
        'BATAL'            => array(),
    );

    protected $validation_rules = array(
        array('field' => 'id_kunjungan', 'label' => 'Kunjungan', 'rules' => 'integer'),
        array('field' => 'nomor_antrian', 'label' => 'Nomor Antrian', 'rules' => 'max_length[20]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_kunjungan', 'label' => 'Kunjungan', 'rules' => 'required'),
        array('field' => 'nomor_antrian', 'label' => 'Nomor Antrian', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buat nomor antrian baru untuk sebuah kunjungan (status Menunggu).
     *
     * @param int $id_kunjungan
     * @param int $id_poli
     * @return array|bool array(id_antrian, nomor_antrian) atau false.
     */
    public function buat($id_kunjungan, $id_poli)
    {
        $kunjungan = $this->db->where('id_kunjungan', $id_kunjungan)->get('kunjungan')->row();
        if (! $kunjungan || (int) $kunjungan->id_poli !== (int) $id_poli) {
            $this->error = 'Kunjungan atau poli tidak valid.';
            return false;
        }
        if ($this->db->where('id_kunjungan', $id_kunjungan)->get('antrian')->row()) {
            $this->error = 'Kunjungan sudah memiliki antrian.';
            return false;
        }
        $today = date('Y-m-d');
        $count = $this->db->select('antrian.id_antrian')
            ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
            ->where(array('antrian.tanggal_antrian' => $today, 'kunjungan.id_poli' => $id_poli))
            ->count_all_results('antrian');
        $nomor = sprintf('P%02d-%03d', (int) $id_poli, $count + 1);
        $id = $this->insert(array(
            'id_kunjungan'    => $id_kunjungan,
            'nomor_antrian'   => $nomor,
            'tanggal_antrian' => $today,
            'status'          => 'MENUNGGU',
        ));
        return $id ? array('id_antrian' => $id, 'nomor_antrian' => $nomor) : false;
    }

    /** Buat antrian untuk kunjungan TERDAFTAR pada Tahap D. */
    public function buat_dari_kunjungan($id_kunjungan)
    {
        $kunjungan = $this->db->where('id_kunjungan', $id_kunjungan)->get('kunjungan')->row();
        if (! $kunjungan || $kunjungan->status !== 'TERDAFTAR') {
            $this->error = 'Kunjungan harus berstatus TERDAFTAR.';
            return false;
        }
        return $this->buat($id_kunjungan, $kunjungan->id_poli);
    }

    /** Daftar antrian yang terhubung ke satu pasien. */
    public function untuk_pasien($id_pasien)
    {
        return $this->db->select('antrian.*, kunjungan.id_pasien, kunjungan.id_dokter,
                kunjungan.id_poli, poli.nama_poli, dokter.nama_dokter')
            ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli')
            ->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter')
            ->where('kunjungan.id_pasien', $id_pasien)
            ->order_by('antrian.tanggal_antrian', 'DESC')
            ->order_by('antrian.id_antrian', 'DESC')
            ->get('antrian')
            ->result();
    }

    /** Daftar antrian untuk dokter tertentu pada hari ini. */
    public function untuk_dokter($id_dokter)
    {
        return $this->db->select('antrian.*, kunjungan.id_pasien, kunjungan.id_dokter,
                pasien.no_rm, pasien.nama AS nama_pasien, poli.nama_poli')
            ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli')
            ->where(array('antrian.tanggal_antrian' => date('Y-m-d'), 'kunjungan.id_dokter' => $id_dokter))
            ->order_by('antrian.id_antrian', 'ASC')
            ->get('antrian')
            ->result();
    }

    /** Antrian hari ini + info kunjungan/pasien/poli (untuk DOKTER & PELAYANAN). */
    public function hari_ini($id_poli = null)
    {
        $this->db->select('antrian.*, kunjungan.id_pasien, kunjungan.id_dokter,
                pasien.no_rm, pasien.nama AS nama_pasien, poli.nama_poli')
            ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli')
            ->where('antrian.tanggal_antrian', date('Y-m-d'))
            ->order_by('antrian.id_antrian', 'ASC');
        if ($id_poli) {
            $this->db->where('kunjungan.id_poli', $id_poli);
        }
        return $this->db->get('antrian')->result();
    }

    /**
     * Ubah status antrian mengikuti alur; catat waktu panggil/mulai/selesai.
     *
     * @return bool
     */
    public function ubah_status($id_antrian, $status_baru)
    {
        $row = $this->find($id_antrian);
        if (! $row) {
            $this->error = 'Antrian tidak ditemukan.';
            return false;
        }
        $boleh = isset($this->alur[$row->status]) ? $this->alur[$row->status] : array();
        if (! in_array($status_baru, $boleh)) {
            $this->error = "Status {$row->status} tidak dapat berubah ke {$status_baru}.";
            return false;
        }
        $data = array('status' => $status_baru);
        if ($status_baru === 'DIPANGGIL') {
            $data['waktu_dipanggil'] = date('Y-m-d H:i:s');
        } elseif ($status_baru === 'SEDANG_DIPERIKSA') {
            $data['waktu_mulai'] = date('Y-m-d H:i:s');
        } elseif ($status_baru === 'SELESAI') {
            $data['waktu_selesai'] = date('Y-m-d H:i:s');
        }
        $this->db->trans_start();
        $updated = $this->update($id_antrian, $data);
        if ($updated && $status_baru === 'SEDANG_DIPERIKSA') {
            $this->db->where('id_kunjungan', $row->id_kunjungan)
                ->update('kunjungan', array('status' => 'DIPROSES'));
        } elseif ($updated && $status_baru === 'SELESAI') {
            $this->db->where('id_kunjungan', $row->id_kunjungan)
                ->update('kunjungan', array('status' => 'SELESAI'));
        }
        $this->db->trans_complete();
        return $updated && $this->db->trans_status();
    }
}
