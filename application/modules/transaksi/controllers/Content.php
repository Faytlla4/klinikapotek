<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_transaksi'); $this->load->model('transaksi/transaksi_model'); Assets::add_module_js('transaksi', 'transaksi.js'); }
    public function index() { Template::set('toolbar_title', 'Transaksi Pembayaran'); Template::render(); }
    public function get_data() {
        $this->db->select('transaksi.id_transaksi AS id, transaksi.*, tagihan.nomor_tagihan')->join('tagihan', 'tagihan.id_tagihan = transaksi.id_tagihan');
        $q = trim($this->input->post('search')['value'] ?? '');
        if ($q !== '') {
            $this->db->group_start()
                ->where("transaksi.nomor_transaksi ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("tagihan.nomor_tagihan ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("transaksi.status ILIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->group_end();
        }
        $dari = $this->input->post('dari');
        $sampai = $this->input->post('sampai');
        if (is_string($dari) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
            $this->db->where('transaksi.tanggal_transaksi >=', $dari . ' 00:00:00');
        }
        if (is_string($sampai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
            $this->db->where('transaksi.tanggal_transaksi <=', $sampai . ' 23:59:59');
        }
        $rows = $this->db->order_by('transaksi.id_transaksi', 'DESC')->get('transaksi')->result();
        echo json_encode(array('draw' => (int)($this->input->post('draw') ?: 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $rows));
    }
    public function detail($id) { $row = $this->transaksi_model->bukti((int) $id); if (!$row) { show_404(); } Template::set('transaksi', $row); Template::set('toolbar_title', 'Bukti Transaksi'); Template::render(); }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_transaksi', $id)->count_all_results('pembayaran');
        if ($n > 0) $guna[] = $n . ' pembayaran';

        if (!empty($guna)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus transaksi karena masih memiliki ' . implode(', ', $guna) . '.'));
            return;
        }

        if ($this->db->where('id_transaksi', $id)->delete('transaksi')) {
            echo json_encode(array('success' => true, 'message' => 'Transaksi berhasil dihapus.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menghapus transaksi.'));
        }
    }
}

