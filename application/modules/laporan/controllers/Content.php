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

    /**
     * Buat sendiri permission konteks LAPORAN CETAK bila belum ada, plus
     * perbaiki assignment role yang tertinggal (mesin baru cukup pull +
     * buka halaman laporan; tanpa SQL manual). Idempoten.
     */
    protected function pastikan_permission_cetak()
    {
        if ($this->db->dbdriver !== 'postgre') {
            return;
        }
        $cetak = array('Laporan.Cetak.View', 'Site.Cetak.View');
        $ada = $this->db->select('COUNT(*) AS n', false)
            ->where_in('nama_permission', $cetak)
            ->get('permissions')->row();
        $segar = false;
        if (empty($ada) || (int) $ada->n !== 2) {
            $this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
                ('Laporan.Cetak.View', 'LAPORAN'),
                ('Site.Cetak.View', 'MANAJEMEN_SISTEM')
                ON CONFLICT (nama_permission) DO NOTHING");
            $segar = true;
        }
        // Role saya boleh lihat laporan tapi belum kebagian menu cetak? Perbaiki.
        $role_id = (int) $this->auth->role_id();
        if ($role_id > 0) {
            $boleh = $this->db->select('COUNT(*) AS n', false)
                ->from('role_permissions rp')
                ->join('permissions p', 'p.id_permission = rp.id_permission')
                ->where('rp.id_role', $role_id)
                ->where('p.nama_permission', 'lihat_laporan')
                ->get()->row();
            if (! empty($boleh) && (int) $boleh->n > 0) {
                $punya = $this->db->select('COUNT(*) AS n', false)
                    ->from('role_permissions rp')
                    ->join('permissions p', 'p.id_permission = rp.id_permission')
                    ->where('rp.id_role', $role_id)
                    ->where_in('p.nama_permission', $cetak)
                    ->get()->row();
                if (empty($punya) || (int) $punya->n !== 2) {
                    $this->db->query('INSERT INTO role_permissions (id_role, id_permission)
                        SELECT ' . $role_id . ', p.id_permission
                        FROM permissions p
                        WHERE p.nama_permission IN (' . "'Laporan.Cetak.View', 'Site.Cetak.View'" . ')
                        ON CONFLICT (id_role, id_permission) DO NOTHING');
                    $segar = true;
                }
            }
        }
        // Muat ulang sekali agar cache permission Auth ikut segar.
        if ($segar) {
            $qs = ! empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
            redirect($this->uri->uri_string() . $qs);
        }
    }
}
