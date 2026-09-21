<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Pelayanan (§10). */
class Pelayanan_model extends BF_Model
{
    protected $table_name = 'pelayanan';
    protected $key = 'id_pelayanan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_pelayanan', 'label' => 'Nama Pelayanan', 'rules' => 'max_length[150]'),
        array('field' => 'tarif', 'label' => 'Tarif', 'rules' => 'numeric'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_pelayanan', 'label' => 'Nama Pelayanan', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /** Daftar pelayanan aktif (sumber data, bukan hardcode). */
    public function aktif()
    {
        return $this->where('status', 'AKTIF')->find_all() ?: array();
    }
}
