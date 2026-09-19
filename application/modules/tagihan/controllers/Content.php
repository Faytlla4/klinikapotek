<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_tagihan'); $this->load->model('tagihan/tagihan_model'); Assets::add_module_js('tagihan', 'tagihan.js'); }
    public function index() { Template::set('toolbar_title', 'Tagihan'); Template::render(); }
    public function get_data() { $rows = $this->db->select('tagihan.id_tagihan AS id, tagihan.*, pasien.no_rm, pasien.nama AS nama_pasien')->join('kunjungan', 'kunjungan.id_kunjungan = tagihan.id_kunjungan', 'left')->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien', 'left')->order_by('tagihan.id_tagihan', 'DESC')->get('tagihan')->result(); echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows)); }
    public function detail($id) { $row = $this->tagihan_model->detail((int) $id); if (!$row) { show_404(); } Template::set('tagihan', $row); Template::set('toolbar_title', 'Detail Tagihan'); Template::render(); }
}

