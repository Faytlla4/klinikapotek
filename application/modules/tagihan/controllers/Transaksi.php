<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Tagihan dalam context TRANSAKSI & PEMBAYARAN (+ halaman Pembayaran). Logic di Content. */
class Transaksi extends Content
{
    protected $ctx = 'transaksi';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }

    public function detail($id)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }

    /** Daftar tagihan belum lunas + form bayar (POST ke halaman ini). */
    public function pembayaran()
    {
        if ($this->input->post('bayar')) {
            $this->auth->restrict('kelola_pembayaran');
            $this->load->model('transaksi/transaksi_model');
            $hasil = $this->transaksi_model->bayar($this->input->post('id_tagihan'), $this->input->post('jumlah_bayar'));
            if ($hasil) {
                $this->load->model('audit/audit_log_model');
                $this->audit_log_model->catat($this->auth->user_id(), 'transaksi', 'pembayaran', $hasil['id_transaksi'], 'Status: ' . $hasil['status']);
                $pesan = 'Pembayaran berhasil dicatat (' . $hasil['status'] . ').';
                if ($hasil['kembalian'] > 0) {
                    $pesan .= ' Kembalian: <strong>Rp ' . number_format($hasil['kembalian'], 0, ',', '.') . '</strong>.';
                }
                Template::set_message($pesan, 'success');
                redirect(SITE_AREA . '/transaksi/tagihan/pembayaran');
            }
            Template::set_message($this->transaksi_model->error ?: 'Pembayaran gagal.', 'error');
        }
        $rows = $this->tagihan_model->belum_lunas();
        Template::set('tagihan_list', $rows);
        Template::set('toolbar_title', 'Pembayaran');
        Template::set_view('transaksi/pembayaran');
        Template::render();
    }
}
