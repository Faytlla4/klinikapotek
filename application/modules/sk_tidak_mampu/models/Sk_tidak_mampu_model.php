<?php defined('BASEPATH') || exit('No direct script access allowed');

class Sk_tidak_mampu_model extends DT_Model
{
	public $table_name = 'sk_tidak_mampu';
	protected $key = 'id';
	protected $date_format = 'datetime';

	protected $log_user = true;
	protected $set_created = true;
	protected $set_modified = true;
	protected $soft_deletes = true;

	protected $created_field = 'created_on';
	protected $created_by_field = 'created_by';
	protected $modified_field = 'modified_on';
	protected $modified_by_field = 'modified_by';
	protected $deleted_field = 'deleted';
	protected $deleted_by_field = 'deleted_by';

	// Customize the operations of the model without recreating the insert,
	// update, etc. methods by adding the method names to act as callbacks here.
	protected $before_insert = array();
	protected $after_insert = array();
	protected $before_update = array();
	protected $after_update = array();
	protected $before_find = array();
	protected $after_find = array();
	protected $before_delete = array();
	protected $after_delete = array();

	// For performance reasons, you may require your model to NOT return the id
	// of the last inserted row as it is a bit of a slow method. This is
	// primarily helpful when running big loops over data.
	protected $return_insert_id = false;

	// The default type for returned row data.
	protected $return_type = 'object';

	// Items that are always removed from data prior to inserts or updates.
	protected $protected_attributes = array('id');

	// You may need to move certain rules (like required) into the
	// $insert_validation_rules array and out of the standard validation array.
	// That way it is only required during inserts, not updates which may only
	// be updating a portion of the data.
	protected $validation_rules = array(
		array(
			'field' => 'nama',
			'label' => 'lang:sk_tidak_mampu_field_nama',
			'rules' => 'required|max_length[30]',
		),
		array(
			'field' => 'alamat',
			'label' => 'lang:sk_tidak_mampu_field_alamat',
			'rules' => 'required|max_length[255]',
		),
		array(
			'field' => 'jenis_surat',
			'label' => 'lang:sk_tidak_mampu_field_jenis_surat',
			'rules' => 'required|max_length[30]',
		),
		array(
			'field' => 'no_telepon',
			'label' => 'lang:sk_tidak_mampu_field_no_telepon',
			'rules' => 'required|max_length[16]',
		),
		array(
			'field' => 'tanggal',
			'label' => 'lang:sk_tidak_mampu_field_tanggal',
			'rules' => 'required',
		),
	);
	protected $insert_validation_rules = array();
	protected $skip_validation = false;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function user_lookup()
	{
		$request = $this->input->post();
		$output['draw'] = (int) $request['draw'];

		if (!empty($request['search']['value']) && strlen($request['search']['value']) > 0) {
			$this->where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}

		$output['recordsTotal'] = $this->db->count_all_results($this->table_name);

		if (!empty($request['search']['value']) && strlen($request['search']['value']) > 0) {
			$this->where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}

		if (!empty($request['sort'])) {
			$this->order_by($request['sort']);
		}

		$this->limit($request['length'], $request['start']);
		$data = $this->db->get($this->table_name)->result();
		$output['data'] = $data;
		$output['recordsFiltered'] = $data ? count($data) : 0;

		return $output;
	}
}