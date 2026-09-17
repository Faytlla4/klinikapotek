<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Apotek Online sisi APOTEKER (context apotek). Daftar + validasi +
 * proses + status, di atas Pesanan_model (stok via jual() existing).
 */
class Apotek extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('kelola_pesanan_online');
        $this->load->model('apotekonline/pesanan_model');
        $this->load->model('audit/audit_log_model');
    }

    public function index()
    {
        $status = $this->input->get('status');
        $this->db->select('pesanan_online.*, pasien.nama AS nama_pasien')
            ->join('pasien', 'pasien.id_pasien = pesanan_online.id_pasien')
            ->order_by('pesanan_online.id_pesanan', 'DESC');
        if ($status) {
            $this->db->where('pesanan_online.status', $status);
        }
        Template::set(array(
            'pesanan_list' => $this->db->get('pesanan_online')->result(),
            'f_status' => $status,
        ));
        Template::set('toolbar_title', 'Pesanan Online');
        Template::set_view('online/kelola');
        Template::render();
    }

    public function detail($id)
    {
        $id = (int) $id > 0 ? (int) $id : 0;
        $row = $id ? $this->pesanan_model->find($id) : false;
        if (! $row) {
            show_404();
        }
        $row->items = $this->db->select('pesanan_online_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = pesanan_online_detail.id_obat')
            ->where('id_pesanan', $id)
            ->get('pesanan_online_detail')->result();
        // Info stok + tagihan terkini untuk keputusan apoteker.
        foreach ($row->items as $it) {
            $st = $this->db->where('id_obat', $it->id_obat)->get('stok_obat')->row();
            $it->stok = $st ? (int) $st->jumlah_stok : 0;
        }
        if ($row->id_tagihan) {
            $row->tagihan = $this->db->where('id_tagihan', $row->id_tagihan)->get('tagihan')->row();
        }
        $aksi = $this->input->post('aksi');
        if ($aksi === 'validasi') {
            $ok = $this->pesanan_model->ubah_status($id, 'DIVERIFIKASI');
        } elseif ($aksi === 'proses') {
            $hasil = $this->pesanan_model->proses($id);
            $ok = (bool) $hasil;
            if (! $ok) {
                Template::set_message($this->pesanan_model->error, 'error');
            }
        } elseif (in_array($aksi, array('siap', 'selesai', 'batal'))) {
            $map = array('siap' => 'SIAP', 'selesai' => 'SELESAI', 'batal' => 'BATAL');
            $ok = $this->pesanan_model->ubah_status($id, $map[$aksi]);
        } else {
            $ok = null;
        }
        if ($ok === true) {
            $this->audit_log_model->catat($this->auth->user_id(), 'update', 'pesanan_online', $id, 'Aksi: ' . $aksi);
            Template::set_message('Pesanan diperbarui.', 'success');
            redirect(SITE_AREA . '/apotek/pesanan-online/detail/' . $id);
        } elseif ($ok === false) {
            Template::set_message($this->pesanan_model->error ?: 'Gagal.', 'error');
        }
        // Muat ulang setelah aksi.
        $row = $this->pesanan_model->find($id);
        $row->items = $this->db->select('pesanan_online_detail.*, obat.nama_obat, obat.satuan')
            ->join('obat', 'obat.id_obat = pesanan_online_detail.id_obat')
            ->where('id_pesanan', $id)
            ->get('pesanan_online_detail')->result();
        foreach ($row->items as $it) {
            $st = $this->db->where('id_obat', $it->id_obat)->get('stok_obat')->row();
            $it->stok = $st ? (int) $st->jumlah_stok : 0;
        }
        if ($row->id_tagihan) {
            $row->tagihan = $this->db->where('id_tagihan', $row->id_tagihan)->get('tagihan')->row();
        }
        Template::set('pesanan', $row);
        Template::set('toolbar_title', 'Detail Pesanan Online');
        Template::set_view('online/kelola_detail');
        Template::render();
    }
}
