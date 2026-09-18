<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Kunjungan (§8, §11). Guard: kelola_pendaftaran (hanya ADMIN/PELAYANAN
 * yang menentukan tujuan pelayanan — pasien tidak memilih poli/dokter).
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kunjungan/kunjungan_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** POST: id_pasien, id_pelayanan, id_poli, id_dokter, id_ruangan */
    public function daftar()
    {
        $this->auth->restrict('kelola_pendaftaran');
        $hasil = $this->kunjungan_model->daftar($this->input->post());
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->kunjungan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'kunjungan', $hasil['id_kunjungan'], 'No. antrian ' . $hasil['nomor_antrian']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET /kunjungan/api/detail/{id} */
    public function detail($id)
    {
        $this->auth->restrict('kelola_pendaftaran');
        $row = $this->kunjungan_model->detail($id);
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Kunjungan tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST /kunjungan/api/status/{id}: status=BATAL.
     * Perubahan status pelayanan normal dikelola oleh alur antrian, supaya
     * kunjungan dan antrian tidak dapat berada pada state yang berbeda.
     */
    public function status($id)
    {
        $this->auth->restrict('kelola_pendaftaran');
        if ($this->input->post('status') !== 'BATAL') {
            $this->json(array('success' => false, 'error' => 'Status kunjungan selain BATAL harus diubah melalui antrian.'), 422);
            return;
        }
        if (! $this->kunjungan_model->ubah_status($id, 'BATAL')) {
            $this->json(array('success' => false, 'error' => $this->kunjungan_model->error), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'kunjungan', $id, 'Status -> ' . $this->input->post('status'));
        $this->json(array('success' => true));
    }
}
