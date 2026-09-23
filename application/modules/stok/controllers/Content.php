<?php defined('BASEPATH') || exit('No direct script access allowed');
// Tabel stok obat (DataTables via get_data). Stoknya sendiri hanya berubah lewat
// penerimaan/penjualan/retur — halaman ini murni baca, tidak ada tombol ubah stok.
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_stok_obat'); $this->load->model('stok/stok_model'); $this->load->model('master/obat_model'); Assets::add_module_js('stok', 'stok.js'); }
    public function index() { Template::set('toolbar_title', 'Stok Obat'); Template::render(); }
    public function get_data() { $rows = $this->obat_model->dengan_stok(); echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows)); }
}
