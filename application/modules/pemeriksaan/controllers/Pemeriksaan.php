<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Pemeriksaan dalam context PEMERIKSAAN & REKAM MEDIS. Logic di Content. */
class Pemeriksaan extends Content
{
    protected $ctx = 'pemeriksaan';

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

    public function edit($id = null)
    {
        Template::set_view('content/edit');
        parent::edit($id);
    }

    public function detail($id = null)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }
}
