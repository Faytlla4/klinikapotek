<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('lihat_laporan');
        $this->load->model('laporan/laporan_model');
    }

    public function index()
    {
        $report = $this->input->get('report') ?: 'kunjungan';
        $dari   = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');

        $valid = array('kunjungan', 'pendapatan', 'penjualan_obat', 'resep', 'mutasi_stok', 'stok');
        if (! in_array($report, $valid)) {
            $report = 'kunjungan';
        }

        $data = $this->laporan_model->{$report}($dari, $sampai);

        Template::set('report', $report);
        Template::set('dari', $dari);
        Template::set('sampai', $sampai);
        Template::set('data_laporan', $data);
        Template::set('toolbar_title', 'Laporan');
        Template::render();
    }

    /** Ambil rentang tanggal Y-m-d dari query string (default: awal bulan s/d hari ini). */
    protected function rentang()
    {
        $dari   = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
            $dari = date('Y-m-01');
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
            $sampai = date('Y-m-d');
        }
        return array($dari, $sampai);
    }
}
