<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Spesialis (§10). */
class Spesialis_model extends BF_Model
{
    protected $table_name = 'spesialis';
    protected $key = 'id_spesialis';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_spesialis', 'label' => 'Nama Spesialis', 'rules' => 'max_length[100]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_spesialis', 'label' => 'Nama Spesialis', 'rules' => 'required'),
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
