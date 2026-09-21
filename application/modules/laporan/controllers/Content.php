<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pastikan_permission_cetak();
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

    /**
     * Buat sendiri permission konteks LAPORAN CETAK bila belum ada (mesin baru
     * cukup pull + buka halaman laporan; tanpa SQL manual). Idempoten.
     */
    protected function pastikan_permission_cetak()
    {
        if ($this->db->dbdriver !== 'postgre') {
            return;
        }
        $ada = $this->db->select('COUNT(*) AS n', false)
            ->where_in('nama_permission', array('Laporan.Cetak.View', 'Site.Cetak.View'))
            ->get('permissions')->row();
        if (! empty($ada) && (int) $ada->n === 2) {
            return;
        }
        $this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
            ('Laporan.Cetak.View', 'LAPORAN'),
            ('Site.Cetak.View', 'MANAJEMEN_SISTEM')
            ON CONFLICT (nama_permission) DO NOTHING");
        $this->db->query("INSERT INTO role_permissions (id_role, id_permission)
            SELECT rp.id_role, p.id_permission
            FROM role_permissions rp
            JOIN permissions pl ON pl.id_permission = rp.id_permission AND pl.nama_permission = 'Laporan.Laporan.View'
            JOIN permissions p ON p.nama_permission IN ('Laporan.Cetak.View', 'Site.Cetak.View')
            ON CONFLICT (id_role, id_permission) DO NOTHING");
        // Muat ulang sekali agar cache permission Auth ikut segar.
        $qs = ! empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        redirect($this->uri->uri_string() . $qs);
    }
}
