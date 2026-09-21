<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Laporan dalam context LAPORAN. Logic di Content. */
class Laporan extends Content
{
    protected $ctx = 'laporan';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }
}
