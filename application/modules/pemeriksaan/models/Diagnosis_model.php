<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Diagnosis Model (§13). Relasi: diagnosis -> pemeriksaan. */
class Diagnosis_model extends BF_Model
{
    protected $table_name = 'diagnosis';
    protected $key = 'id_diagnosis';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'integer'),
        array('field' => 'nama_diagnosis', 'label' => 'Diagnosis', 'rules' => 'max_length[200]'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'required'),
        array('field' => 'nama_diagnosis', 'label' => 'Diagnosis', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }
}
