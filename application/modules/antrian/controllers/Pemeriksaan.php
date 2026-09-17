<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Antrian Dokter dalam context PEMERIKSAAN & REKAM MEDIS. Logic di Content. */
class Pemeriksaan extends Content
{
    protected $ctx = 'pemeriksaan';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }
}
