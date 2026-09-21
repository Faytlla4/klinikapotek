<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Pengadaan & Supplier (§16). Guard: kelola_pengadaan.
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pengadaan/supplier_model');
        $this->load->model('pengadaan/pengadaan_model');
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

    /** Item list: id_obat -> int. Qty DIBIARKAN mentah agar validasi
     * bilangan bulat di model dapat menolak desimal. */
    private function as_items($items, $qty_key)
    {
        if (! is_array($items)) {
            return $items;
        }
        foreach ($items as &$it) {
            if (isset($it['id_obat'])) {
                $it['id_obat'] = $this->as_id($it['id_obat']);
            }
        }
        unset($it);
        return $items;
    }

    /** GET supplier aktif */
    public function supplier()
    {
        $this->auth->restrict('kelola_pengadaan');
        $this->json(array('success' => true, 'data' => $this->supplier_model->aktif()));
    }

    /** POST: nama_supplier, alamat?, no_hp? */
    public function supplier_simpan()
    {
        $this->auth->restrict('kelola_pengadaan');
        $data = array_intersect_key($this->input->post(), array_flip(array(
            'kode_supplier', 'nama_supplier', 'alamat', 'no_hp', 'status',
        )));
        if (empty($data['kode_supplier'])) {
            $data['kode_supplier'] = $this->supplier_model->generate_kode();
        }
        $id = $this->supplier_model->insert($data);
        if (! $id) {
            $this->json(array('success' => false, 'error' => $this->supplier_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'supplier', $id, '');
        $this->json(array('success' => true, 'data' => $this->supplier_model->find($id)), 201);
    }

    /** POST: id_supplier, items[][id_obat,jumlah_pesan,harga?] */
    public function pesan()
    {
        $this->auth->restrict('kelola_pengadaan');
        $hasil = $this->pengadaan_model->pesan($this->as_id($this->input->post('id_supplier')), $this->as_items($this->input->post('items'), 'jumlah_pesan'));
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->pengadaan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'pengadaan_obat', $hasil['id_pengadaan'], $hasil['nomor_pengadaan']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** POST: id_pengadaan, items[][id_obat,jumlah_terima,kondisi?] */
    public function terima()
    {
        $this->auth->restrict('kelola_pengadaan');
        $hasil = $this->pengadaan_model->terima($this->as_id($this->input->post('id_pengadaan')), $this->as_items($this->input->post('items'), 'jumlah_terima'));
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->pengadaan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'stok', 'penerimaan_obat', $hasil['id_penerimaan'], $hasil['nomor_penerimaan']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** POST /pengadaan/api/batalkan/{id} */
    public function batalkan($id)
    {
        $this->auth->restrict('kelola_pengadaan');
        $id = $this->as_id($id);
        if (! $id || ! $this->pengadaan_model->batalkan($id)) {
            $this->json(array('success' => false, 'error' => $this->pengadaan_model->error), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'pengadaan_obat', $id, 'Dibatalkan');
        $this->json(array('success' => true));
    }
}
