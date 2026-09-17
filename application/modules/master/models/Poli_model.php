<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Poli (§10). */
class Poli_model extends BF_Model
{
    protected $table_name = 'poli';
    protected $key = 'id_poli';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_poli', 'label' => 'Nama Poli', 'rules' => 'max_length[100]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_poli', 'label' => 'Nama Poli', 'rules' => 'required'),
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
