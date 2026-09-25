<?php defined('BASEPATH') || exit('No direct script access allowed');

class Transaksi extends App_Controller
{
    protected $ctx = 'transaksi';

    public function __construct()
    {
        parent::__construct();

        $this->load->model('tagihan/tagihan_model');

        Assets::add_module_js(
            'tagihan',
            'tagihan.js'
        );
    }

    private function require_tagihan()
    {
        $this->auth->restrict('kelola_tagihan');
    }

    private function require_pembayaran()
    {
        $this->auth->restrict('kelola_pembayaran');
    }

    public function index()
    {
        $this->require_tagihan();

        Template::set(
            'toolbar_title',
            'Daftar Tagihan'
        );

        Template::set_view(
            'content/index'
        );

        Template::render();
    }

    public function detail($id)
    {
        $this->require_tagihan();

        $id = (int) $id;

        if ($id <= 0) {
            show_404();
        }

        $tagihan = $this->tagihan_model->detail($id);

        if (! $tagihan) {
            show_404();
        }

        Template::set(
            'tagihan',
            $tagihan
        );

        Template::set(
            'toolbar_title',
            'Detail Tagihan'
        );

        Template::set_view(
            'content/detail'
        );

        Template::render();
    }

    public function pembayaran()
    {
        $this->require_pembayaran();

        $this->load->model(
            'transaksi/transaksi_model'
        );

        if ($this->input->post('bayar')) {
            $id_tagihan = (int) $this->input->post(
                'id_tagihan'
            );

            $jumlah_bayar = $this->input->post(
                'jumlah_bayar'
            );

            $hasil = $this->transaksi_model->bayar(
                $id_tagihan,
                $jumlah_bayar
            );

            if ($hasil) {
                $this->load->model(
                    'audit/audit_log_model'
                );

                $this->audit_log_model->catat(
                    $this->auth->user_id(),
                    'transaksi',
                    'pembayaran',
                    $hasil['id_transaksi'],
                    'Status: ' . $hasil['status']
                );

                $pesan = 'Pembayaran berhasil dicatat ('
                    . $hasil['status']
                    . ').';

                if ($hasil['kembalian'] > 0) {
                    $pesan .= ' Kembalian: <strong>Rp '
                        . number_format(
                            $hasil['kembalian'],
                            0,
                            ',',
                            '.'
                        )
                        . '</strong>.';
                }

                Template::set_message(
                    $pesan,
                    'success'
                );

                redirect(
                    SITE_AREA
                    . '/transaksi/tagihan/pembayaran'
                );

                return;
            }

            Template::set_message(
                $this->transaksi_model->error
                    ?: 'Pembayaran gagal.',
                'error'
            );
        }

        $tagihan_list = $this->tagihan_model->belum_lunas();

        Template::set(
            'tagihan_list',
            $tagihan_list
        );

        Template::set(
            'toolbar_title',
            'Pembayaran'
        );

        Template::set_view(
            'transaksi/pembayaran'
        );

        Template::render();
    }
}