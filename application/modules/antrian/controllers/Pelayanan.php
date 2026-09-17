<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Antrian dalam context PELAYANAN. Logic di Content. */
class Pelayanan extends Content
{
    protected $ctx = 'pelayanan';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }
}
