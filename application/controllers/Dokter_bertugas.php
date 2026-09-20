<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Memilih konteks dokter untuk satu akun ber-role DOKTER. */
class Dokter_bertugas extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (! $this->is_role_dokter()) {
            redirect('dashboard');
        }
        $this->load->model('master/dokter_model');
    }

    public function index()
    {
        if ($this->input->post('pilih')) {
            $id = (int) $this->input->post('id_dokter');
            $dokter = $this->db->where('id_dokter', $id)->where('status', 'AKTIF')->get('dokter')->row();
            if ($dokter) {
                $this->session->set_userdata('id_dokter_aktif', (int) $dokter->id_dokter);
                Template::set_message('Selamat datang, ' . html_escape($dokter->nama_dokter) . '.', 'success');
                redirect('dashboard/dokter');
            }
            Template::set_message('Dokter yang dipilih tidak valid atau tidak aktif.', 'error');
        }
        Template::set('dokter_list', $this->dokter_model->aktif());
        Template::set('toolbar_title', 'Pilih Dokter Bertugas');
        Template::set_view('dokter_bertugas/index');
        Template::render();
    }

    public function ganti()
    {
        $this->session->unset_userdata('id_dokter_aktif');
        redirect('dokter-bertugas');
    }

    private function is_role_dokter()
    {
        $role = $this->db->select('nama_role')->where('id_role', $this->auth->role_id())->get('roles')->row();
        return $role && $role->nama_role === 'DOKTER';
    }
}
