<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Resep dokter dalam context PEMERIKSAAN & REKAM MEDIS. Logic di Content. */
class Pemeriksaan extends Content
{
    protected $ctx = 'pemeriksaan';

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
