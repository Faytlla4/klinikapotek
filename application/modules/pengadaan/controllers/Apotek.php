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

    public function detail($id = null)
    {
        Template::set_view('content/detail');
        parent::detail($id);
    }

    public function terima($id = null)
    {
        parent::terima($id);
    }

    public function terima_draft($id = null)
    {
        parent::terima_draft($id);
    }

    public function permintaan()
    {
        Template::set_view('content/permintaan');
        parent::permintaan();
    }

    public function permintaan_buat()
    {
        Template::set_view('content/permintaan_buat');
        parent::permintaan_buat();
    }

    public function permintaan_detail($id = null)
    {
        Template::set_view('content/permintaan_detail');
        parent::permintaan_detail($id);
    }

    public function penerimaan_detail($id = null)
    {
        Template::set_view('content/penerimaan_detail');
        parent::penerimaan_detail($id);
    }

    public function retur()
    {
        Template::set_view('content/retur');
        parent::retur();
    }

    public function retur_buat($id_penerimaan = null)
    {
        Template::set_view('content/retur_buat');
        parent::retur_buat($id_penerimaan);
    }

    public function retur_detail($id = null)
    {
        Template::set_view('content/retur_detail');
        parent::retur_detail($id);
    }

    public function delete($id = null)
    {
        parent::delete($id);
    }
}
