<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Antrian (§12). Guard: kelola_antrian (pelayanan) / kelola_antrian_dokter.
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('antrian/antrian_model');
        $this->load->model('master/dokter_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** GET ?id_poli= (antrian hari ini, urut nomor) */
    public function hari_ini()
    {
        if (! $this->auth->has_permission('kelola_antrian') && ! $this->auth->has_permission('kelola_antrian_dokter')) {
            $this->auth->restrict('kelola_antrian');
        }
        $this->json(array('success' => true, 'data' => $this->antrian_model->hari_ini($this->input->get('id_poli'))));
    }

    /** POST: id_kunjungan. Membuat antrian dari kunjungan TERDAFTAR. */
    public function buat()
    {
        $this->auth->restrict('kelola_antrian');
        $hasil = $this->antrian_model->buat_dari_kunjungan($this->input->post('id_kunjungan'));
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->antrian_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'antrian', $hasil['id_antrian'], $hasil['nomor_antrian']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET ?id_pasien=. Hanya ADMIN/PELAYANAN; ownership pasien belum tersedia di schema users. */
    public function pasien()
    {
        $this->auth->restrict('kelola_pasien');
        $this->json(array('success' => true, 'data' => $this->antrian_model->untuk_pasien($this->input->get('id_pasien'))));
    }

    /** GET antrian dokter yang login (parameter id_dokter diabaikan, anti-IDOR). */
    public function dokter()
    {
        $this->auth->restrict('kelola_antrian_dokter');
        if (! $this->auth->has_permission('kelola_antrian')) {
            $dokter = $this->dokter_model->dari_user($this->auth->user_id());
            if (! $dokter) {
                $this->json(array('success' => false, 'error' => 'Akun belum dipetakan ke data dokter.'), 403);
                return;
            }
            $this->json(array('success' => true, 'data' => $this->antrian_model->untuk_dokter($dokter->id_dokter)));
            return;
        }
        $this->json(array('success' => true, 'data' => $this->antrian_model->untuk_dokter($this->input->get('id_dokter'))));
    }

    /** POST /antrian/api/status/{id}: DIPANGGIL|DILEWATI|SEDANG_DIPERIKSA|SELESAI|BATAL */
    public function status($id)
    {
        if (! $this->auth->has_permission('kelola_antrian') && ! $this->auth->has_permission('kelola_antrian_dokter')) {
            $this->auth->restrict('kelola_antrian');
        }
        $status = $this->input->post('status');
        if (! $this->auth->has_permission('kelola_antrian')) {
            // Dokter hanya boleh mengubah antrian pasiennya sendiri.
            $dokter = $this->dokter_model->dari_user($this->auth->user_id());
            $milik = $dokter ? $this->db->select('kunjungan.id_dokter')
                ->join('kunjungan', 'kunjungan.id_kunjungan = antrian.id_kunjungan')
                ->where('antrian.id_antrian', $id)->get('antrian')->row() : null;
            if (! $milik || (int) $milik->id_dokter !== (int) $dokter->id_dokter) {
                $this->json(array('success' => false, 'error' => 'Bukan antrian pasien Anda.'), 403);
                return;
            }
        }
        if (in_array($status, array('DIPANGGIL', 'SEDANG_DIPERIKSA'))) {
            // Dokter memanggil dari antriannya; pelayanan mengelola umum.
            if (! $this->auth->has_permission('kelola_antrian_dokter')
                && ! $this->auth->has_permission('kelola_antrian')) {
                $this->json(array('success' => false, 'error' => 'Tidak berizin.'), 403);
                return;
            }
        }
        if (! $this->antrian_model->ubah_status($id, $status)) {
            $this->json(array('success' => false, 'error' => $this->antrian_model->error), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'antrian', $id, 'Status -> ' . $status);
        $this->json(array('success' => true));
    }
}
