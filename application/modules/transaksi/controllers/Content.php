<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_transaksi'); $this->load->model('transaksi/transaksi_model'); Assets::add_module_js('transaksi', 'transaksi.js'); }
    public function index() { Template::set('toolbar_title', 'Transaksi Pembayaran'); Template::render(); }
    public function get_data() { $rows = $this->db->select('transaksi.*, tagihan.nomor_tagihan')->join('tagihan', 'tagihan.id_tagihan = transaksi.id_tagihan')->order_by('id_transaksi', 'DESC')->get('transaksi')->result(); echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows)); }
    public function detail($id) { $row = $this->transaksi_model->bukti((int) $id); if (!$row) { show_404(); } Template::set('transaksi', $row); Template::set('toolbar_title', 'Bukti Transaksi'); Template::render(); }
}

