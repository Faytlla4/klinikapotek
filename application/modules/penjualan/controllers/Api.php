<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Penjualan (§14, §15). Guard: kelola_penjualan_obat.
 * POST jual: jenis=RESEP|LANGSUNG, id_resep?, id_pasien?, items[][id_obat,jumlah].
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('penjualan/penjualan_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function jual()
    {
        $this->auth->restrict('kelola_penjualan_obat');
        $hasil = $this->penjualan_model->jual(
            $this->input->post('jenis'),
            $this->input->post('id_resep'),
            $this->input->post('id_pasien'),
            $this->input->post('items')
        );
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->penjualan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'transaksi', 'penjualan_obat', $hasil['id_penjualan'], $hasil['nomor_penjualan']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET /penjualan/api/detail/{id} */
    public function detail($id)
    {
        $this->auth->restrict('kelola_penjualan_obat');
        $row = $this->penjualan_model->detail($id);
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }
}
