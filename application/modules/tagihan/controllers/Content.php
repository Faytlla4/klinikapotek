<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_tagihan'); $this->load->model('tagihan/tagihan_model'); Assets::add_module_js('tagihan', 'tagihan.js'); }
    public function index() { Template::set('toolbar_title', 'Tagihan'); Template::render(); }
    public function get_data() {
        $rows = $this->db->select('tagihan.id_tagihan AS id, tagihan.*, pasien.no_rm, pasien.nama AS nama_pasien')->join('kunjungan', 'kunjungan.id_kunjungan = tagihan.id_kunjungan', 'left')->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien', 'left');
        $q = trim($this->input->post('search')['value'] ?? '');
        if ($q !== '') {
            $this->db->group_start()
                ->where("tagihan.nomor_tagihan ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("pasien.no_rm ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("pasien.nama ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("tagihan.status ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->group_end();
        }
        $dari = $this->input->post('dari');
        $sampai = $this->input->post('sampai');
        if (is_string($dari) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
            $this->db->where('tagihan.tanggal_tagihan >=', $dari . ' 00:00:00');
        }
        if (is_string($sampai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
            $this->db->where('tagihan.tanggal_tagihan <=', $sampai . ' 23:59:59');
        }
        $rows = $this->db->order_by('tagihan.id_tagihan', 'DESC')->get('tagihan')->result();
        echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows));
    }
    public function detail($id) { $row = $this->tagihan_model->detail((int) $id); if (!$row) { show_404(); } Template::set('tagihan', $row); Template::set('toolbar_title', 'Detail Tagihan'); Template::render(); }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_tagihan', $id)->count_all_results('transaksi');
        if ($n > 0) $guna[] = $n . ' transaksi';
        $n = $this->db->where('id_tagihan', $id)->count_all_results('pesanan_online');
        if ($n > 0) $guna[] = $n . ' pesanan online';

        if (!empty($guna)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus tagihan karena masih memiliki ' . implode(', ', $guna) . '.'));
            return;
        }

        $this->db->where('id_tagihan', $id)->delete('tagihan_detail');
        if ($this->db->where('id_tagihan', $id)->delete('tagihan')) {
            echo json_encode(array('success' => true, 'message' => 'Tagihan berhasil dihapus.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menghapus tagihan.'));
        }
    }
}

