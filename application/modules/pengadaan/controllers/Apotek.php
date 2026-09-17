<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Pengadaan dalam context APOTEK. Logic di Content. */
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
}
