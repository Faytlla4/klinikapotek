<?php defined('BASEPATH') || exit('No direct script access allowed');

class Transaksi extends App_Controller
{
    protected $ctx = 'transaksi';

    public function __construct()
    {
        parent::__construct();

        $this->load->model('tagihan/tagihan_model');

        Assets::add_module_js(
            'tagihan',
            'tagihan.js'
        );
    }

    private function require_tagihan()
    {
        $this->auth->restrict('kelola_tagihan');
    }

    private function require_pembayaran()
    {
        $this->auth->restrict('kelola_pembayaran');
    }

    public function index()
    {
        $this->require_tagihan();

        Template::set(
            'toolbar_title',
            'Daftar Tagihan'
        );

        Template::set_view(
            'content/index'
        );

        Template::render();
    }

    public function get_data()
    {
        $this->require_tagihan();

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

    public function delete($id = null)
    {
        $this->require_tagihan();

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

    public function detail($id)
    {
        $this->require_tagihan();

        $id = (int) $id;

        if ($id <= 0) {
            show_404();
        }

        $tagihan = $this->tagihan_model->detail($id);

        if (! $tagihan) {
            show_404();
        }

        Template::set(
            'tagihan',
            $tagihan
        );

        Template::set(
            'toolbar_title',
            'Detail Tagihan'
        );

        Template::set_view(
            'content/detail'
        );

        Template::render();
    }

    public function pembayaran()
    {
        $this->require_pembayaran();

        $this->load->model(
            'transaksi/transaksi_model'
        );

        if ($this->input->post('bayar')) {
            $id_tagihan = (int) $this->input->post(
                'id_tagihan'
            );

            $jumlah_bayar = $this->input->post(
                'jumlah_bayar'
            );

            $hasil = $this->transaksi_model->bayar(
                $id_tagihan,
                $jumlah_bayar
            );

            if ($hasil) {
                $this->load->model(
                    'audit/audit_log_model'
                );

                $this->audit_log_model->catat(
                    $this->auth->user_id(),
                    'transaksi',
                    'pembayaran',
                    $hasil['id_transaksi'],
                    'Status: ' . $hasil['status']
                );

                $pesan = 'Pembayaran berhasil dicatat ('
                    . $hasil['status']
                    . ').';

                if ($hasil['kembalian'] > 0) {
                    $pesan .= ' Kembalian: <strong>Rp '
                        . number_format(
                            $hasil['kembalian'],
                            0,
                            ',',
                            '.'
                        )
                        . '</strong>.';
                }

                Template::set_message(
                    $pesan,
                    'success'
                );

                redirect(
                    SITE_AREA
                    . '/transaksi/tagihan/pembayaran'
                );

                return;
            }

            Template::set_message(
                $this->transaksi_model->error
                    ?: 'Pembayaran gagal.',
                'error'
            );
        }

        $tagihan_list = $this->tagihan_model->belum_lunas();

        Template::set(
            'tagihan_list',
            $tagihan_list
        );

        Template::set(
            'toolbar_title',
            'Pembayaran'
        );

        Template::set_view(
            'transaksi/pembayaran'
        );

        Template::render();
    }
}