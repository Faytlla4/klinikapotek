<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Tindakan Model (§13). Relasi: tindakan -> pemeriksaan. */
class Tindakan_model extends BF_Model
{
    protected $table_name = 'tindakan';
    protected $key = 'id_tindakan';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = false;
    protected $set_modified = false;
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'integer'),
        array('field' => 'nama_tindakan', 'label' => 'Tindakan', 'rules' => 'max_length[200]'),
        array('field' => 'biaya', 'label' => 'Biaya', 'rules' => 'numeric'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'id_pemeriksaan', 'label' => 'Pemeriksaan', 'rules' => 'required'),
        array('field' => 'nama_tindakan', 'label' => 'Tindakan', 'rules' => 'required'),
        array('field' => 'biaya', 'label' => 'Biaya', 'rules' => 'required'),
    );
    protected $skip_validation = false;

    public function __construct()
    {
        parent::__construct();
    }

    /** Total biaya tindakan per pemeriksaan (untuk tagihan). */
    public function total_biaya($id_pemeriksaan)
    {
        $row = $this->db->select_sum('biaya', 'total')
            ->where('id_pemeriksaan', $id_pemeriksaan)
            ->get('tindakan')
            ->row();
        return $row ? (float) $row->total : 0;
    }
}
