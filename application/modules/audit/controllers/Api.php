<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * API Audit Log (read-only). Guard: lihat_audit_log.
 * GET /audit/api/list?modul=&aksi=&limit=
 */
class Api extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit/audit_log_model');
    }

    public function list()
    {
        $this->auth->restrict('lihat_audit_log');
        if ($this->input->get('modul')) {
            $this->db->where('modul', $this->input->get('modul'));
        }
        if ($this->input->get('aksi')) {
            $this->db->where('aksi', $this->input->get('aksi'));
        }
        $rows = $this->db->select('audit_logs.*, users.username')
            ->join('users', 'users.id_user = audit_logs.id_user', 'left')
            ->order_by('id_log', 'DESC')
            ->limit((int) ($this->input->get('limit') ?: 100))
            ->get('audit_logs')
            ->result();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array('success' => true, 'data' => $rows)));
    }
}
