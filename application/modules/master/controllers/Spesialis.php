<?php defined('BASEPATH') || exit('No direct script access allowed');

class Spesialis extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permission);
		$this->load->model('master/spesialis_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");

		Template::set_block('sub_nav', 'spesialis/_sub_nav');
		Assets::add_module_js('master', 'spesialis.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Spesialis');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($id = $this->save_spesialis('insert')) {
				Template::set_message('Spesialis berhasil ditambahkan.', 'success');
				redirect(SITE_AREA . '/master/spesialis');
			}
		}

		Template::set('toolbar_title', 'Tambah Spesialis');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Spesialis tidak valid.', 'error');
			redirect(SITE_AREA . '/master/spesialis');
		}

		if (isset($_POST['save'])) {
			if ($this->save_spesialis('update', $id)) {
				Template::set_message('Spesialis berhasil diperbarui.', 'success');
				redirect(SITE_AREA . '/master/spesialis');
			}
		}

		Template::set('spesialis', $this->spesialis_model->find($id));
		Template::set('toolbar_title', 'Edit Spesialis');
		Template::render();
	}

	private function save_spesialis($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->spesialis_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'nama_spesialis' => $this->input->post('nama_spesialis'),
			'status'         => $this->input->post('status') ? $this->input->post('status') : 'AKTIF',
		);

		if ($type == 'insert') {
			return $this->spesialis_model->insert($data);
		}

		return $this->spesialis_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';

		$this->db->from('spesialis');
		if (!empty($search)) {
			$this->db->where("nama_spesialis ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$total = $this->db->count_all_results();

		$this->db->from('spesialis');
		if (!empty($search)) {
			$this->db->where("nama_spesialis ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$this->db->order_by('id_spesialis', 'DESC');
		$this->db->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();

		echo json_encode(array(
			'draw'            => $draw,
			'recordsTotal'    => $total,
			'recordsFiltered' => $total,
			'data'            => $data ?: array()
		));
	}
}


