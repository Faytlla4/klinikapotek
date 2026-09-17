<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Audit Log dalam context MANAJEMEN SISTEM (read-only, reuse Audit_log_model). */
class Settings extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('lihat_audit_log');
        $this->load->model('audit/audit_log_model');
    }

    public function index()
    {
        $modul = $this->input->get('modul');
        $aksi = $this->input->get('aksi');
        if ($modul) {
            $this->db->where('audit_logs.modul', $modul);
        }
        if ($aksi) {
            $this->db->where('audit_logs.aksi', $aksi);
        }
        $rows = $this->db->select('audit_logs.*, users.username')
            ->join('users', 'users.id_user = audit_logs.id_user', 'left')
            ->order_by('id_log', 'DESC')
            ->limit(100)
            ->get('audit_logs')
            ->result();
        Template::set('log_list', $rows);
        Template::set('f_modul', $modul);
        Template::set('f_aksi', $aksi);
        Template::set('toolbar_title', 'Audit Log');
        Template::render();
    }
}
