<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Transaksi dalam context TRANSAKSI & PEMBAYARAN. Logic di Content. */
class Transaksi extends Content
{
    protected $ctx = 'transaksi';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }

    public function detail($id)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }
}
