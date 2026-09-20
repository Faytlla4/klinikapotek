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

    /** ID -> int positif; 0 bila tidak valid (hindari SQL error). */
    private function as_id($v)
    {
        $id = (int) $v;
        return $id > 0 ? $id : 0;
    }

    public function jual()
    {
        $this->auth->restrict('kelola_penjualan_obat');
        $items = $this->input->post('items');
        if (is_array($items)) {
            // id_* dinormalisasi di sini; jumlah DIBIARKAN mentah agar
            // validasi bilangan bulat di model dapat menolak desimal.
            foreach ($items as &$it) {
                if (isset($it['id_obat'])) {
                    $it['id_obat'] = $this->as_id($it['id_obat']);
                }
            }
            unset($it);
        }
        $hasil = $this->penjualan_model->jual(
            $this->input->post('jenis'),
            $this->as_id($this->input->post('id_resep')),
            $this->as_id($this->input->post('id_pasien')),
            $items
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
        $id = $this->as_id($id);
        $row = $id ? $this->penjualan_model->detail($id) : false;
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST /penjualan/api/retur/{id}: items[][id_obat,jumlah], keterangan? */
    public function retur($id)
    {
        $this->auth->restrict('kelola_penjualan_obat');
        $items = $this->input->post('items');
        $hasil = $this->penjualan_model->retur($this->as_id($id), $items, trim($this->input->post('keterangan') ?: ''));
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->penjualan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'stok', 'retur_penjualan', $hasil['id_retur'], 'Retur penjualan ' . $id);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }
}
