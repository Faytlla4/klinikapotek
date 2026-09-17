<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Ruangan (§10). Relasi: ruangan -> poli. */
class Ruangan_model extends BF_Model
{
    protected $table_name = 'ruangan';
    protected $key = 'id_ruangan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_ruangan', 'label' => 'Nama Ruangan', 'rules' => 'max_length[100]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_ruangan', 'label' => 'Nama Ruangan', 'rules' => 'required'),
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

    /** Ruangan aktif per poli. */
    public function per_poli($id_poli)
    {
        return $this->where(array('id_poli' => $id_poli, 'status' => 'AKTIF'))->find_all() ?: array();
    }
}
