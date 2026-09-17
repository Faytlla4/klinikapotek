<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_penjualan_obat'); $this->load->model('penjualan/penjualan_model'); }
    public function index() { Template::set('toolbar_title', 'Penjualan Obat'); Template::render(); }
}
