<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Kunjungan dalam context PELAYANAN. Logic di Content. */
class Pelayanan extends Content
{
    protected $ctx = 'pelayanan';

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

    public function detail($id = null)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }
}
