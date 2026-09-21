<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Master Obat (§10). */
class Obat_model extends BF_Model
{
    protected $table_name = 'obat';
    protected $key = 'id_obat';
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = 'created_at';
    protected $modified_field = 'updated_at';
    protected $return_insert_id = true;

    protected $validation_rules = array(
        array('field' => 'nama_obat', 'label' => 'Nama Obat', 'rules' => 'max_length[150]'),
        array('field' => 'satuan', 'label' => 'Satuan', 'rules' => 'max_length[30]'),
        array('field' => 'harga', 'label' => 'Harga', 'rules' => 'numeric'),
    );
    protected $insert_validation_rules = array(
        array('field' => 'nama_obat', 'label' => 'Nama Obat', 'rules' => 'required'),
        array('field' => 'satuan', 'label' => 'Satuan', 'rules' => 'required'),
        array('field' => 'harga', 'label' => 'Harga', 'rules' => 'numeric'),
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

    /** Obat + stok saat ini. */
    public function dengan_stok($id_obat = null)
    {
        $this->db->select('obat.*, COALESCE(stok_obat.jumlah_stok, 0) AS stok')
            ->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left');
        if ($id_obat) {
            return $this->db->where('obat.id_obat', $id_obat)->get('obat')->row();
        }
        return $this->db->get('obat')->result();
    }

    /** Auto-generate kode obat berikutnya: OBT-001, OBT-002, ... */
    public function generate_kode()
    {
        $rows = $this->db->select('kode_obat')
            ->like('kode_obat', 'OBT-', 'after')
            ->get('obat')
            ->result();

        $max = 0;
        foreach ($rows as $row) {
            if (preg_match('/OBT-(\d+)$/', $row->kode_obat, $m)) {
                $n = (int) $m[1];
                if ($n > $max) {
                    $max = $n;
                }
            }
        }

        return 'OBT-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
    }
}
