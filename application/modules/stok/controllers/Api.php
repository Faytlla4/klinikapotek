<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Stok (§14). Guard: kelola_stok_obat.
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stok/stok_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** GET /stok/api/posisi/{id_obat} */
    public function posisi($id_obat)
    {
        $this->auth->restrict('kelola_stok_obat');
        $this->json(array('success' => true, 'data' => array(
            'id_obat' => (int) $id_obat, 'stok' => $this->stok_model->posisi($id_obat),
        )));
    }

    /** GET /stok/api/riwayat/{id_obat} */
    public function riwayat($id_obat)
    {
        $this->auth->restrict('kelola_stok_obat');
        $this->json(array('success' => true, 'data' => $this->stok_model->riwayat($id_obat)));
    }

    /** GET obat di bawah stok minimum */
    public function menipis()
    {
        $this->auth->restrict('kelola_stok_obat');
        $this->json(array('success' => true, 'data' => $this->stok_model->di_bawah_minimum()));
    }
}
