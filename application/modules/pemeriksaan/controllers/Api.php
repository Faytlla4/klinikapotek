<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Pemeriksaan/Diagnosis/Tindakan (§13). Guard: kelola_pemeriksaan (DOKTER).
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pemeriksaan/pemeriksaan_model');
        $this->load->model('master/dokter_model');
        $this->load->model('pemeriksaan/diagnosis_model');
        $this->load->model('pemeriksaan/tindakan_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** ID -> int positif; 0 bila tidak valid (hindari SQL error). */
    private function as_id($v)
    {
        $id = (int) $v;
        return $id > 0 ? $id : 0;
    }

    /** POST: id_kunjungan, id_dokter, keluhan?, hasil_pemeriksaan?, catatan_dokter? */
    public function buka()
    {
        $this->auth->restrict('kelola_pemeriksaan');
        $sendiri = $this->dokter_sendiri();
        if ($sendiri === false) {
            $this->json(array('success' => false, 'error' => 'Akun belum dipetakan ke data dokter.'), 403);
            return;
        }
        $post = $this->input->post();
        if ($sendiri) {
            $post['id_dokter'] = $sendiri;
        }
        $post['id_kunjungan'] = $this->as_id($post['id_kunjungan'] ?? null);
        $post['id_dokter'] = $this->as_id($post['id_dokter'] ?? null);
        if (empty($post['id_kunjungan']) || empty($post['id_dokter'])) {
            $this->json(array('success' => false, 'error' => 'Kunjungan dan dokter wajib diisi.'), 422);
            return;
        }
        $id = $this->pemeriksaan_model->buka($post['id_kunjungan'], $post['id_dokter'], $post);
        if (! $id) {
            $this->json(array('success' => false, 'error' => $this->pemeriksaan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'pemeriksaan', $id, '');
        $this->json(array('success' => true, 'data' => $this->pemeriksaan_model->find($id)), 201);
    }

    /** GET /pemeriksaan/api/rekam_medis/{id} */
    public function rekam_medis($id)
    {
        $this->auth->restrict('kelola_pemeriksaan');
        $id = $this->as_id($id);
        $row = $id ? $this->pemeriksaan_model->rekam_medis($id) : false;
        if (! $row || ! $this->boleh_akses($row->id_dokter)) {
            $this->json(array('success' => false, 'error' => 'Tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST: id_pemeriksaan, nama_diagnosis, keterangan? */
    public function diagnosis()
    {
        $this->auth->restrict('kelola_pemeriksaan');
        $post = $this->input->post();
        if (! $this->milik_sendiri($post['id_pemeriksaan'] ?? null)) {
            $this->json(array('success' => false, 'error' => 'Bukan pemeriksaan pasien Anda.'), 403);
            return;
        }
        $id = $this->diagnosis_model->insert($post);
        if (! $id) {
            $this->json(array('success' => false, 'error' => $this->diagnosis_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'diagnosis', $id, '');
        $this->json(array('success' => true, 'data' => $this->diagnosis_model->find($id)), 201);
    }

    /** POST: id_pemeriksaan, nama_tindakan, biaya, keterangan? */
    public function tindakan()
    {
        $this->auth->restrict('kelola_pemeriksaan');
        if (! $this->milik_sendiri($this->input->post('id_pemeriksaan'))) {
            $this->json(array('success' => false, 'error' => 'Bukan pemeriksaan pasien Anda.'), 403);
            return;
        }
        $id = $this->tindakan_model->insert($this->input->post());
        if (! $id) {
            $this->json(array('success' => false, 'error' => $this->tindakan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'tindakan', $id, '');
        $this->json(array('success' => true, 'data' => $this->tindakan_model->find($id)), 201);
    }

    /** POST /pemeriksaan/api/selesai/{id} */
    public function selesai($id)
    {
        $this->auth->restrict('kelola_pemeriksaan');
        if (! $this->milik_sendiri($id)) {
            $this->json(array('success' => false, 'error' => 'Bukan pemeriksaan pasien Anda.'), 403);
            return;
        }
        if (! $this->pemeriksaan_model->selesaikan($id)) {
            $this->json(array('success' => false, 'error' => $this->pemeriksaan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'pemeriksaan', $id, 'Selesai');
        $this->json(array('success' => true));
    }

    /** True bila user adalah dokter murni (bukan pelayanan/admin). */
    private function hanya_dokter()
    {
        return $this->auth->has_permission('kelola_pemeriksaan')
            && ! $this->auth->has_permission('kelola_pendaftaran');
    }

    /** id_dokter milik user login; null = tanpa filter; false = belum dipetakan. */
    private function dokter_sendiri()
    {
        if (! $this->hanya_dokter()) {
            return null;
        }
        $dokter = $this->dokter_model->dari_user($this->auth->user_id());
        return $dokter ? (int) $dokter->id_dokter : false;
    }

    /** Dokter hanya boleh menyentuh pemeriksaan miliknya sendiri. */
    private function boleh_akses($id_dokter)
    {
        $sendiri = $this->dokter_sendiri();
        return $sendiri === null || ($sendiri !== false && (int) $id_dokter === (int) $sendiri);
    }

    /** True bila pemeriksaan ada dan boleh diakses user saat ini. */
    private function milik_sendiri($id_pemeriksaan)
    {
        $id_pemeriksaan = $this->as_id($id_pemeriksaan);
        if (empty($id_pemeriksaan)) {
            return false;
        }
        if (! $this->hanya_dokter()) {
            return true;
        }
        $row = $this->pemeriksaan_model->find($id_pemeriksaan);
        return $row && $this->boleh_akses($row->id_dokter);
    }
}
