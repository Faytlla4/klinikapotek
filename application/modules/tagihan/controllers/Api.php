<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Tagihan & Transaksi (§17).
 * Guard: kelola_tagihan / kelola_transaksi / kelola_pembayaran.
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tagihan/tagihan_model');
        $this->load->model('transaksi/transaksi_model');
        $this->load->model('audit/audit_log_model');
    }

    private function json($data, $code = 200)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /** ID URI/POST -> int positif; 0 bila tidak valid (hindari SQL error). */
    private function as_id($v)
    {
        $id = (int) $v;
        return $id > 0 ? $id : 0;
    }

    /** POST /tagihan/api/susun/{id_kunjungan} (otomatis dari tarif+tindakan+obat) */
    public function susun($id_kunjungan)
    {
        $this->auth->restrict('kelola_tagihan');
        $id_kunjungan = $this->as_id($id_kunjungan);
        if (! $id_kunjungan) {
            $this->json(array('success' => false, 'error' => 'Kunjungan tidak valid.'), 422);
            return;
        }
        $hasil = $this->tagihan_model->susun_dari_kunjungan($id_kunjungan);
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->tagihan_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'tagihan', $hasil['id_tagihan'], $hasil['nomor_tagihan']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET /tagihan/api/detail/{id} */
    public function detail($id)
    {
        $this->auth->restrict('kelola_tagihan');
        $id = $this->as_id($id);
        $row = $id ? $this->tagihan_model->detail($id) : false;
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Tagihan tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }

    /** POST /tagihan/api/batalkan/{id} */
    public function batalkan($id)
    {
        $this->auth->restrict('kelola_tagihan');
        $id = $this->as_id($id);
        if (! $id || ! $this->tagihan_model->batalkan($id)) {
            $this->json(array('success' => false, 'error' => $this->tagihan_model->error), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'tagihan', $id, 'Dibatalkan');
        $this->json(array('success' => true));
    }

    /** POST: id_tagihan, jumlah_bayar */
    public function bayar()
    {
        $this->auth->restrict('kelola_pembayaran');
        $hasil = $this->transaksi_model->bayar($this->as_id($this->input->post('id_tagihan')), $this->input->post('jumlah_bayar'));
        if (! $hasil) {
            $this->json(array('success' => false, 'error' => $this->transaksi_model->error ?: 'Gagal.'), 422);
            return;
        }
        $this->audit_log_model->catat($this->auth->user_id(), 'transaksi', 'pembayaran', $hasil['id_transaksi'], 'Status: ' . $hasil['status']);
        $this->json(array('success' => true, 'data' => $hasil), 201);
    }

    /** GET /tagihan/api/bukti/{id_transaksi} (cetak transaksi) */
    public function bukti($id_transaksi)
    {
        $this->auth->restrict('kelola_transaksi');
        $id_transaksi = $this->as_id($id_transaksi);
        $row = $id_transaksi ? $this->transaksi_model->bukti($id_transaksi) : false;
        if (! $row) {
            $this->json(array('success' => false, 'error' => 'Transaksi tidak ditemukan.'), 404);
            return;
        }
        $this->json(array('success' => true, 'data' => $row));
    }
}
