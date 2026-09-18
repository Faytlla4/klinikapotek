<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pemeriksaan Model (§13). Relasi: pemeriksaan -> kunjungan, dokter.
 * Status: DIPROSES -> SELESAI.
 */
class Pemeriksaan_model extends BF_Model
{
    protected $table_name = 'pemeriksaan';
    protected $key = 'id_pemeriksaan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'id_kunjungan', 'label' => 'Kunjungan', 'rules' => 'integer'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'integer'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_kunjungan', 'label' => 'Kunjungan', 'rules' => 'required'),
        array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buka pemeriksaan dari kunjungan (kunjungan harus Diperiksa/Sedang diperiksa).
     *
     * @return int|bool id_pemeriksaan atau false.
     */
    public function buka($id_kunjungan, $id_dokter, $data = array())
    {
        $kunjungan = $this->db->where('id_kunjungan', $id_kunjungan)->get('kunjungan')->row();
        if (! $kunjungan) {
            $this->error = 'Kunjungan tidak ditemukan.';
            return false;
        }
        if ((int) $kunjungan->id_dokter !== (int) $id_dokter) {
            $this->error = 'Dokter tidak sesuai dengan kunjungan.';
            return false;
        }
        if (! in_array($kunjungan->status, array('TERDAFTAR', 'MENUNGGU', 'DIPROSES'))) {
            $this->error = 'Kunjungan belum siap diperiksa (status: ' . $kunjungan->status . ').';
            return false;
        }
        
        // Pastikan status kunjungan di-update menjadi DIPROSES ketika pemeriksaan dimulai
        if ($kunjungan->status !== 'DIPROSES') {
            $this->db->where('id_kunjungan', $id_kunjungan)->update('kunjungan', array('status' => 'DIPROSES'));
            $this->db->where('id_kunjungan', $id_kunjungan)
                     ->where('status !=', 'SELESAI')
                     ->where('status !=', 'BATAL')
                     ->update('antrian', array('status' => 'SEDANG_DIPERIKSA', 'waktu_mulai' => date('Y-m-d H:i:s')));
        }
        if ($this->db->where('id_kunjungan', $id_kunjungan)->get('pemeriksaan')->row()) {
            $this->error = 'Kunjungan sudah memiliki pemeriksaan.';
            return false;
        }
        $data = array_intersect_key($data, array_flip(array(
            'id_kunjungan', 'id_dokter', 'keluhan', 'hasil_pemeriksaan', 'catatan_dokter',
            'tanggal_pemeriksaan', 'status',
        )));
        $data['id_kunjungan'] = $id_kunjungan;
        $data['id_dokter'] = $id_dokter;
        $data['tanggal_pemeriksaan'] = date('Y-m-d H:i:s');
        $data['status'] = 'DIPROSES';
        return $this->insert($data);
    }

    /** Pemeriksaan + diagnosis + tindakan + resep (rekam medis satu kunjungan). */
    public function rekam_medis($id_pemeriksaan)
    {
        $row = $this->find($id_pemeriksaan);
        if (! $row) {
            return false;
        }
        $row->diagnosis = $this->db->where('id_pemeriksaan', $id_pemeriksaan)->get('diagnosis')->result();
        $row->tindakan = $this->db->where('id_pemeriksaan', $id_pemeriksaan)->get('tindakan')->result();
        $row->resep = $this->db->where('id_pemeriksaan', $id_pemeriksaan)->get('resep')->result();
        return $row;
    }

    /** Selesaikan pemeriksaan (kunjungan ikut Selesai bila semua pemeriksaan selesai). */
    public function selesaikan($id_pemeriksaan)
    {
        $row = $this->find($id_pemeriksaan);
        if (! $row) {
            $this->error = 'Pemeriksaan tidak ditemukan.';
            return false;
        }
        if ($row->status === 'SELESAI') {
            return true;
        }
        $this->db->trans_start();
        $this->update($id_pemeriksaan, array('status' => 'SELESAI'));
        $terbuka = $this->db->where(array('id_kunjungan' => $row->id_kunjungan))
            ->where('status !=', 'SELESAI')
            ->count_all_results('pemeriksaan');
        if ($terbuka == 0) {
            $this->db->where('id_kunjungan', $row->id_kunjungan)->update('kunjungan', array('status' => 'SELESAI'));
            $this->db->query(
                "UPDATE antrian SET status = 'SELESAI', waktu_selesai = NOW()
                 WHERE id_kunjungan = ? AND status != 'SELESAI'",
                array($row->id_kunjungan)
            );
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Daftar pemeriksaan dengan seluruh konteks pasien dan kunjungan. */
    public function daftar($id_dokter = null)
    {
        $this->db->select('pemeriksaan.*, kunjungan.id_pasien, pasien.no_rm,
                pasien.nama AS nama_pasien, dokter.nama_dokter, poli.nama_poli')
            ->join('kunjungan', 'kunjungan.id_kunjungan = pemeriksaan.id_kunjungan')
            ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
            ->join('dokter', 'dokter.id_dokter = pemeriksaan.id_dokter')
            ->join('poli', 'poli.id_poli = kunjungan.id_poli')
            ->order_by('pemeriksaan.tanggal_pemeriksaan', 'DESC');
        if ($id_dokter) {
            $this->db->where('pemeriksaan.id_dokter', $id_dokter);
        }
        return $this->db->get('pemeriksaan')->result();
    }
}
