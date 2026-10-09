<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Halaman Master Supplier dalam konteks MASTER. */
class Master extends App_Controller
{
    protected $ctx = 'master';

    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('Master.Supplier.Manage');

        $this->load->model('pengadaan/supplier_model');
        $this->load->model('audit/audit_log_model');
    }

    public function supplier()
    {
        if ($this->input->post('save')) {
            $data = array_intersect_key(
                $this->input->post(),
                array_flip(array(
                    'kode_supplier',
                    'nama_supplier',
                    'alamat',
                    'no_hp',
                    'status'
                ))
            );

            $id = $this->supplier_model->insert($data);

            if ($id) {
                $this->audit_log_model->catat(
                    $this->auth->user_id(),
                    'create',
                    'supplier',
                    $id,
                    ''
                );

                Template::set_message('Supplier berhasil disimpan.', 'success');
                redirect(SITE_AREA . '/master/pengadaan/supplier');
                return;
            }

            Template::set_message(
                $this->supplier_model->error ?: 'Gagal menyimpan supplier.',
                'error'
            );
        }

        Template::set('kode_supplier_baru', $this->supplier_model->generate_kode());
        Template::set(
            'supplier_list',
            $this->db->order_by('nama_supplier', 'ASC')->get('supplier')->result()
        );
        Template::set('toolbar_title', 'Master Supplier');
        Template::set_view('master/supplier');
        Template::render();
    }
}