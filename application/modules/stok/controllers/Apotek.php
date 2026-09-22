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

    /** Riwayat mutasi masuk/keluar per obat (saring jenis + tanggal). */
    public function mutasi()
    {
        $this->load->model('master/obat_model');
        $id_obat = (int) $this->input->get('id_obat');
        $jenis = strtoupper(trim($this->input->get('jenis') ?: ''));
        $jenis = ($jenis === 'MASUK' || $jenis === 'KELUAR') ? $jenis : '';
        $dari = $this->input->get('dari') ?: '';
        $sampai = $this->input->get('sampai') ?: '';
        $dari = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari) ? $dari : '';
        $sampai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai) ? $sampai : '';
        Template::set('obat_list', $this->obat_model->aktif());
        Template::set('id_obat', $id_obat);
        Template::set('f_jenis', $jenis);
        Template::set('f_dari', $dari);
        Template::set('f_sampai', $sampai);
        Template::set('riwayat', $id_obat ? $this->stok_model->riwayat($id_obat, 200, $dari ?: null, $sampai ?: null, $jenis ?: null) : array());
        Template::set('toolbar_title', 'Obat Masuk/Keluar');
        Template::set_view('apotek/mutasi');
        Template::render();
    }
}
