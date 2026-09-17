<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Stok Obat dalam context APOTEK (+ halaman Obat Masuk/Keluar). Logic di Content. */
class Apotek extends Content
{
    protected $ctx = 'apotek';

    public function index()
    {
        Template::set_view('content/index');
        parent::index();
    }

    /** Riwayat mutasi masuk/keluar per obat. */
    public function mutasi()
    {
        $this->load->model('master/obat_model');
        $id_obat = (int) $this->input->get('id_obat');
        Template::set('obat_list', $this->obat_model->aktif());
        Template::set('id_obat', $id_obat);
        Template::set('riwayat', $id_obat ? $this->stok_model->riwayat($id_obat) : array());
        Template::set('toolbar_title', 'Obat Masuk/Keluar');
        Template::set_view('apotek/mutasi');
        Template::render();
    }
}
