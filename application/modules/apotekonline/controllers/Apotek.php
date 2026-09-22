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
        $this->load->model('apotekonline/retur_model');
        $this->load->model('audit/audit_log_model');
    }

    public function index()
    {
        $status = $this->input->get('status');
        // Sync status_bayar dari tagihan agar list selalu terkini.
        $this->pesanan_model->sync_semua_bayar();
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
        // Sync status_bayar dari tagihan sebelum tampil.
        $this->pesanan_model->selaraskan_bayar($row);
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
        $psn = $this->db->select('nama, no_hp')->where('id_pasien', $row->id_pasien)->get('pasien')->row();
        $row->nama_pasien = $psn ? $psn->nama : '-';
        $row->no_hp_pasien = ($psn && $psn->no_hp) ? $psn->no_hp : '-';
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
        $this->pesanan_model->selaraskan_bayar($row);
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
        $psn = $this->db->select('nama, no_hp')->where('id_pasien', $row->id_pasien)->get('pasien')->row();
        $row->nama_pasien = $psn ? $psn->nama : '-';
        $row->no_hp_pasien = ($psn && $psn->no_hp) ? $psn->no_hp : '-';
        Template::set('pesanan', $row);
        Template::set('toolbar_title', 'Detail Pesanan Online');
        Template::set_view('online/kelola_detail');
        Template::render();
    }

    /** Daftar pengajuan retur (filter status via ?status=). */
    public function retur()
    {
        $status = $this->input->get('status');
        if ($status !== null && ! in_array($status, array('DIMINTA', 'SELESAI', 'DITOLAK'))) {
            $status = null;
        }
        Template::set(array(
            'retur_list' => $this->retur_model->daftar($status),
            'f_status' => $status,
        ));
        Template::set('toolbar_title', 'Retur Online');
        Template::set_view('online/retur');
        Template::render();
    }

    /** Putus retur: setujui (disposisi+refund) atau tolak (catatan). */
    public function retur_detail($id)
    {
        $id = (int) $id > 0 ? (int) $id : 0;
        $aksi = $this->input->post('aksi');
        if ($aksi === 'setujui') {
            $ok = $this->retur_model->setujui(
                $id,
                (array) $this->input->post('disposisi'),
                $this->input->post('metode_refund'),
                $this->input->post('nominal_refund'),
                $this->input->post('catatan'),
                $this->auth->user_id()
            );
            if ($ok) {
                $this->audit_log_model->catat($this->auth->user_id(), 'update', 'retur_online', $id, 'Retur disetujui');
                Template::set_message('Retur disetujui, stok dan refund tercatat.', 'success');
            } else {
                Template::set_message($this->retur_model->error, 'error');
            }
            redirect(SITE_AREA . '/apotek/retur/' . $id);
        } elseif ($aksi === 'tolak') {
            $ok = $this->retur_model->tolak($id, $this->input->post('catatan'), $this->auth->user_id());
            if ($ok) {
                $this->audit_log_model->catat($this->auth->user_id(), 'update', 'retur_online', $id, 'Retur ditolak');
                Template::set_message('Retur ditolak.', 'success');
            } else {
                Template::set_message($this->retur_model->error, 'error');
            }
            redirect(SITE_AREA . '/apotek/retur/' . $id);
        }
        $row = $id ? $this->retur_model->detail($id) : false;
        if (! $row) {
            show_404();
        }
        Template::set('retur', $row);
        Template::set('toolbar_title', 'Putusan Retur Online');
        Template::set_view('online/retur_detail');
        Template::render();
    }
}
