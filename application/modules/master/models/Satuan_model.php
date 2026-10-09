<?php defined('BASEPATH') || exit('No direct script access allowed');

class Satuan_model extends BF_Model
{
    protected $table_name = 'master_satuan';
    protected $key = 'id_satuan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array(
            'field' => 'nama_satuan',
            'label' => 'Nama Satuan',
            'rules' => 'required|max_length[100]'
        ),
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'required|in_list[AKTIF,NONAKTIF]'
        ),
    );

    protected $insert_validation_rules = array(
        array(
            'field' => 'nama_satuan',
            'label' => 'Nama Satuan',
            'rules' => 'required|is_unique[master_satuan.nama_satuan]'
        )
    );
    
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function aktif()
    {
        return $this->where('status', 'AKTIF')->order_by('nama_satuan', 'ASC')->find_all() ?: array();
    }
}
