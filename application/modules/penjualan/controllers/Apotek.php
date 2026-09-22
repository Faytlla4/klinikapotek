<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Penjualan Obat dalam context APOTEK (+ buat penjualan). Logic di Content. */
class Apotek extends Content
{
    protected $ctx = 'apotek';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }

    public function create()
    {
        Template::set_view('content/create');
        parent::create();
    }

    public function detail($id)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }
}
