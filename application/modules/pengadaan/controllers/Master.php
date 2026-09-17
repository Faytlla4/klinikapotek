<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__ . '/Content.php';

/** Supplier dalam context MASTER DATA. Form simpan di sini; API di Api::supplier_simpan. */
class Master extends Content
{
    protected $ctx = 'master';

    public function supplier()
    {
        if ($this->input->post('save')) {
            $data = array_intersect_key($this->input->post(), array_flip(array(
                'kode_supplier', 'nama_supplier', 'alamat', 'no_hp', 'status',
            )));
            $id = $this->supplier_model->insert($data);
            if ($id) {
                $this->load->model('audit/audit_log_model');
                $this->audit_log_model->catat($this->auth->user_id(), 'create', 'supplier', $id, '');
                Template::set_message('Supplier berhasil disimpan.', 'success');
                redirect(SITE_AREA . '/master/pengadaan/supplier');
            }
            Template::set_message($this->supplier_model->error ?: 'Gagal menyimpan supplier.', 'error');
        }
        Template::set('supplier_list', $this->db->order_by('nama_supplier', 'ASC')->get('supplier')->result());
        Template::set('toolbar_title', 'Master Supplier');
        Template::set_view('master/supplier');
        Template::render();
    }
}
