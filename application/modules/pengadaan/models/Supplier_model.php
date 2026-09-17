<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Supplier Model (§16). */
class Supplier_model extends BF_Model
{
    protected $table_name = 'supplier';
    protected $key = 'id_supplier';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'kode_supplier', 'label' => 'Kode', 'rules' => 'max_length[50]'),
        array('field' => 'nama_supplier', 'label' => 'Nama Supplier', 'rules' => 'max_length[150]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'kode_supplier', 'label' => 'Kode', 'rules' => 'required|is_unique[supplier.kode_supplier]'),
        array('field' => 'nama_supplier', 'label' => 'Nama Supplier', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function aktif()
    {
        return $this->where('status', 'AKTIF')->find_all() ?: array();
    }
}
