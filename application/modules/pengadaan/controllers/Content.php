<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
    /** Context aktif (sidebar/redirect). Child per-context meng-override. */
    protected $ctx = 'content';
    public function __construct()
    {
        parent::__construct(); $this->auth->restrict('kelola_pengadaan');
        $this->load->model('pengadaan/pengadaan_model'); $this->load->model('pengadaan/supplier_model');
        $this->load->model('master/obat_model'); Assets::add_module_js('pengadaan', 'pengadaan.js');
    }
    public function index() { Template::set('toolbar_title', 'Pengadaan Obat'); Template::render(); }
    public function create()
    {
        if (isset($_POST['save'])) {
            $items = array(array('id_obat' => $this->input->post('id_obat'), 'jumlah_pesan' => $this->input->post('jumlah_pesan'), 'harga' => $this->input->post('harga')));
            $result = $this->pengadaan_model->pesan($this->input->post('id_supplier'), $items);
            if ($result) { Template::set_message('Pengadaan berhasil dibuat.', 'success'); redirect(SITE_AREA . '/' . $this->ctx . '/pengadaan'); }
            Template::set_message($this->pengadaan_model->error ?: 'Pengadaan gagal.', 'error');
        }
        Template::set('supplier_list', $this->supplier_model->aktif()); Template::set('obat_list', $this->obat_model->aktif());
        Template::set('toolbar_title', 'Tambah Pengadaan'); Template::render();
    }
    public function get_data()
    {
        $rows = $this->db->select('pengadaan_obat.*, supplier.nama_supplier')
            ->join('supplier', 'supplier.id_supplier = pengadaan_obat.id_supplier')
            ->order_by('id_pengadaan', 'DESC')
            ->get('pengadaan_obat')
            ->result();
        foreach ($rows as $row) {
            $row->id = (int) $row->id_pengadaan;
        }
        echo json_encode(array(
            'draw'            => (int) ($this->input->post('draw') ?: 1),
            'recordsTotal'    => count($rows),
            'recordsFiltered' => count($rows),
            'data'            => $rows
        ));
    }

    public function delete($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
            return;
        }

        $guna = array();
        $n = $this->db->where('id_pengadaan', $id)->count_all_results('penerimaan_obat');
        if ($n > 0) $guna[] = $n . ' penerimaan';

        if (!empty($guna)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus pengadaan karena masih memiliki ' . implode(', ', $guna) . '.'));
            return;
        }

        $this->db->where('id_pengadaan', $id)->delete('pengadaan_obat_detail');
        if ($this->db->where('id_pengadaan', $id)->delete('pengadaan_obat')) {
            echo json_encode(array('success' => true, 'message' => 'Pengadaan berhasil dihapus.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menghapus pengadaan.'));
        }
    }
}
