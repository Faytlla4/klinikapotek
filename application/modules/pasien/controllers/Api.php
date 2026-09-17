<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Pasien (§9): daftar, cari (NIK/nama/no_rm), detail, ubah.
 * Guard: kelola_pasien. Tanpa view (JSON).
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pasien/pasien_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** GET ?keyword=&by=nik|nama|no_rm */
    public function cari()
    {
        $this->auth->restrict('kelola_pasien');
        $this->json(array('success' => true, 'data' => $this->pasien_model->cari(
            $this->input->get('keyword'), $this->input->get('by')
        )));
    }

    /** GET /pasien/api/detail/{id} */
    public function detail($id)
    {
        $this->auth->restrict('kelola_pasien');
        $row = $this->pasien_model->detail($id);
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Pasien tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST: nama, nik?, tanggal_lahir?, jenis_kelamin?, alamat?, no_hp? (no_rm otomatis) */
    public function daftar()
    {
        $this->auth->restrict('kelola_pasien');
        $id = $this->pasien_model->daftar($this->input->post());
        if (! $id) {
            $this->load->library('form_validation');
            $this->json(array('success' => false, 'error' => $this->pasien_model->error ?: strip_tags($this->form_validation->error_string()) ?: 'Gagal menyimpan.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'pasien', $id, 'Pendaftaran pasien');
        $this->json(array('success' => true, 'data' => $this->pasien_model->find($id)), 201);
    }

    /** POST /pasien/api/ubah/{id} */
    public function ubah($id)
    {
        $this->auth->restrict('kelola_pasien');
        $data = $this->input->post();
        unset($data['no_rm'], $data['id_pasien']); // no_rm tidak boleh berubah
        if (! $this->pasien_model->update($id, $data)) {
            $this->json(array('success' => false, 'error' => $this->pasien_model->error ?: 'Gagal mengubah.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'pasien', $id, 'Ubah data pasien');
        $this->json(array('success' => true, 'data' => $this->pasien_model->find($id)));
    }
}
