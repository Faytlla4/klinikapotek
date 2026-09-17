<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Laporan (read-only). Guard: lihat_laporan.
 * GET /laporan/api/{kunjungan|pendapatan|penjualan_obat|resep|mutasi_stok}?dari=YYYY-MM-DD&sampai=YYYY-MM-DD
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laporan/laporan_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function periode()
    {
        $dari = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');
        return array($dari, $sampai);
    }

    public function _remap($method)
    {
        $this->auth->restrict('lihat_laporan');
        if (! in_array($method, array('kunjungan', 'pendapatan', 'penjualan_obat', 'resep', 'mutasi_stok'))) {
            $this->json(array('success' => false, 'error' => 'Laporan tidak dikenal.'), 404);
            return;
        }
        list($dari, $sampai) = $this->periode();
        $this->json(array('success' => true, 'dari' => $dari, 'sampai' => $sampai,
            'data' => $this->laporan_model->{$method}($dari, $sampai)));
    }
}
