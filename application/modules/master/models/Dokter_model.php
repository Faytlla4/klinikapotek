<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Dokter (§10). Relasi: dokter -> spesialis. */
class Dokter_model extends BF_Model
{
    protected $table_name = 'dokter';
    protected $key = 'id_dokter';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_dokter', 'label' => 'Nama Dokter', 'rules' => 'max_length[150]'),
        array('field' => 'tarif', 'label' => 'Tarif', 'rules' => 'numeric'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_dokter', 'label' => 'Nama Dokter', 'rules' => 'required'),
        array('field' => 'tarif', 'label' => 'Tarif', 'rules' => 'required'),
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

    /** Baris dokter milik user login (relasi dokter.id_user). False bila belum dipetakan. */
    public function dari_user($id_user)
    {
        if (empty($id_user)) {
            return false;
        }
        return $this->db->where('dokter.id_user', $id_user)->get('dokter')->row() ?: false;
    }

    /** Dokter + nama spesialis. */
    public function dengan_spesialis($id_dokter = null)    {
        $this->db->select('dokter.*, spesialis.nama_spesialis')
            ->join('spesialis', 'spesialis.id_spesialis = dokter.id_spesialis', 'left');
        if ($id_dokter) {
            return $this->db->where('dokter.id_dokter', $id_dokter)->get('dokter')->row();
        }
        return $this->db->get('dokter')->result();
    }
}
