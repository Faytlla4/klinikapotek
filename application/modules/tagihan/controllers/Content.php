<?php defined('BASEPATH') || exit('No direct script access allowed');
// Daftar tagihan (DataTables + cari nomor/RM/nama) + detail rincian.
// Tagihan tidak dibuat manual di sini; ia akibat penjualan/kunjungan.
class Content extends App_Controller
{
    public function __construct() { parent::__construct(); $this->auth->restrict('kelola_tagihan'); $this->load->model('tagihan/tagihan_model'); Assets::add_module_js('tagihan', 'tagihan.js'); }
    public function index() { Template::set('toolbar_title', 'Tagihan'); Template::render(); }
    public function get_data()
    {
        $request = $this->input->post();
        $draw    = (int) ($request['draw'] ?? 1);
        $start   = (int) ($request['start'] ?? 0);
        $length  = (int) ($request['length'] ?? 10);
        if ($length <= 0) {
            $length = 10;
        }

        $search_val = '';
        if (isset($request['search']) && is_array($request['search']) && !empty($request['search']['value'])) {
            $search_val = trim($request['search']['value']);
        } elseif (is_string($this->input->post('search'))) {
            $search_val = trim($this->input->post('search'));
        }

        $dari   = $request['dari'] ?? $this->input->post('dari');
        $sampai = $request['sampai'] ?? $this->input->post('sampai');

        $recordsTotal = $this->db->count_all_results('tagihan');

        $build_query = function () use ($search_val, $dari, $sampai) {
            $this->db->select('tagihan.id_tagihan AS id, tagihan.*, pasien.no_rm, COALESCE(pasien.nama, \'Umum\') AS nama_pasien', FALSE)
                ->from('tagihan')
                ->join('kunjungan', 'kunjungan.id_kunjungan = tagihan.id_kunjungan', 'left')
                ->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien', 'left');

            if ($search_val !== '') {
                $escaped = $this->db->escape_like_str($search_val);
                $this->db->group_start()
                    ->where("tagihan.nomor_tagihan ILIKE '%" . $escaped . "%'", NULL, FALSE)
                    ->or_where("pasien.no_rm ILIKE '%" . $escaped . "%'", NULL, FALSE)
                    ->or_where("pasien.nama ILIKE '%" . $escaped . "%'", NULL, FALSE)
                    ->or_where("tagihan.status ILIKE '%" . $escaped . "%'", NULL, FALSE)
                    ->group_end();
            }

            if (is_string($dari) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
                $this->db->where('tagihan.tanggal_tagihan >=', $dari . ' 00:00:00');
            }
            if (is_string($sampai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
                $this->db->where('tagihan.tanggal_tagihan <=', $sampai . ' 23:59:59');
            }
        };

        $build_query();
        $recordsFiltered = $this->db->count_all_results();

        $build_query();
        $this->db->order_by('tagihan.id_tagihan', 'DESC')
            ->limit($length, $start);
        $rows = $this->db->get()->result();

        foreach ($rows as $row) {
            $row->id = (int) $row->id_tagihan;
            $row->total = (float) $row->total;
        }

        $response = array(
            'draw'            => $draw,
            'recordsTotal'    => (int) $recordsTotal,
            'recordsFiltered' => (int) $recordsFiltered,
            'data'            => $rows ?: array(),
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function detail($id) { $row = $this->tagihan_model->detail((int) $id); if (!$row) { show_404(); } Template::set('tagihan', $row); Template::set('toolbar_title', 'Detail Tagihan'); Template::render(); }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'ID tidak valid.')));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_tagihan', $id)->count_all_results('transaksi');
        if ($n > 0) $guna[] = $n . ' transaksi';
        $n = $this->db->where('id_tagihan', $id)->count_all_results('pesanan_online');
        if ($n > 0) $guna[] = $n . ' pesanan online';

        if (!empty($guna)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus tagihan karena masih memiliki ' . implode(', ', $guna) . '.')));
            return;
        }

        $this->db->where('id_tagihan', $id)->delete('tagihan_detail');
        if ($this->db->where('id_tagihan', $id)->delete('tagihan')) {
            $res = array('success' => true, 'message' => 'Tagihan berhasil dihapus.');
        } else {
            $res = array('success' => false, 'message' => 'Gagal menghapus tagihan.');
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($res));
    }
}

