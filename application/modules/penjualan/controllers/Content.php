<?php defined('BASEPATH') || exit('No direct script access allowed');
class Content extends App_Controller
{
    /** Context aktif (sidebar/redirect). Child per-context meng-override. */
    protected $ctx = 'content';
    public function __construct()
    {
        parent::__construct(); $this->auth->restrict('kelola_penjualan_obat');
        $this->load->model('penjualan/penjualan_model'); $this->load->model('resep/resep_model');
        $this->load->model('master/obat_model'); $this->load->model('pasien/pasien_model');
        Assets::add_module_js('penjualan', 'penjualan.js');
    }
    public function index()
    {
        $dari = $this->input->get('dari');
        $sampai = $this->input->get('sampai');
        $ok_dari = is_string($dari) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari);
        $ok_sampai = is_string($sampai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai);
        $this->db->select('penjualan_obat.*, pasien.nama AS nama_pasien')
            ->join('pasien', 'pasien.id_pasien = penjualan_obat.id_pasien', 'left');
        if ($ok_dari) {
            $this->db->where('penjualan_obat.tanggal_penjualan >=', $dari . ' 00:00:00');
        }
        if ($ok_sampai) {
            $this->db->where('penjualan_obat.tanggal_penjualan <=', $sampai . ' 23:59:59');
        }
        $rows = $this->db->order_by('penjualan_obat.id_penjualan', 'DESC')->limit($ok_dari || $ok_sampai ? 500 : 50)->get('penjualan_obat')->result();
        Template::set('jual_list', $rows);
        Template::set('f_dari', $ok_dari ? $dari : '');
        Template::set('f_sampai', $ok_sampai ? $sampai : '');
        Template::set('toolbar_title', 'Penjualan Obat'); Template::render();
    }
    public function create()
    {
        if ($this->input->post('save')) {
            $post = $this->input->post();
            $items = array();
            if (isset($post['id_obat']) && is_array($post['id_obat'])) {
                foreach ($post['id_obat'] as $i => $id_obat) {
                    if ((int) $id_obat > 0 && (int) ($post['jumlah'][$i] ?? 0) > 0) {
                        $items[] = array('id_obat' => (int) $id_obat, 'jumlah' => (int) $post['jumlah'][$i]);
                    }
                }
            }
            $hasil = $this->penjualan_model->jual(
                $post['jenis_penjualan'] ?? 'LANGSUNG',
                $post['id_resep'] ?: null,
                $post['id_pasien'] ?: null,
                $items
            );
            if ($hasil) {
                $this->load->model('audit/audit_log_model');
                $this->audit_log_model->catat($this->auth->user_id(), 'transaksi', 'penjualan_obat', $hasil['id_penjualan'], $hasil['nomor_penjualan']);
                Template::set_message('Penjualan ' . $hasil['nomor_penjualan'] . ' berhasil.', 'success');
                redirect(SITE_AREA . '/' . $this->ctx . '/penjualan');
            }
            Template::set_message($this->penjualan_model->error ?: 'Penjualan gagal.', 'error');
        }
        Template::set('resep_list', $this->resep_model->menunggu());
        Template::set('obat_list', $this->obat_model->dengan_stok());
        Template::set('pasien_list', $this->db->where('status', 'AKTIF')->order_by('nama', 'ASC')->get('pasien')->result());
        Template::set('toolbar_title', 'Jual Obat'); Template::render();
    }

    /** Struk penjualan + item (cetak nota). */
    public function detail($id)
    {
        $id = (int) $id > 0 ? (int) $id : 0;
        $row = $id ? $this->penjualan_model->detail($id) : false;
        if (! $row) {
            show_404();
        }
        Template::set('jual', $row);
        Template::set('toolbar_title', 'Struk Penjualan'); Template::render();
    }
}
