<?php defined('BASEPATH') || exit('No direct script access allowed');

class Poli extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permission);
		$this->load->model('master/poli_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");

		Template::set_block('sub_nav', 'poli/_sub_nav');
		Assets::add_module_js('master', 'poli.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Poli');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($id = $this->save_poli('insert')) {
				Template::set_message('Poli berhasil ditambahkan.', 'success');
				redirect(SITE_AREA . '/master/poli');
			}
		}

		Template::set('toolbar_title', 'Tambah Poli');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Poli tidak valid.', 'error');
			redirect(SITE_AREA . '/master/poli');
		}

		if (isset($_POST['save'])) {
			if ($this->save_poli('update', $id)) {
				Template::set_message('Poli berhasil diperbarui.', 'success');
				redirect(SITE_AREA . '/master/poli');
			}
		}

		Template::set('poli', $this->poli_model->find($id));
		Template::set('toolbar_title', 'Edit Poli');
		Template::render();
	}

	private function save_poli($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->poli_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'nama_poli' => $this->input->post('nama_poli'),
			'status'    => $this->input->post('status') ? $this->input->post('status') : 'AKTIF',
		);

		if ($type == 'insert') {
			return $this->poli_model->insert($data);
		}

		return $this->poli_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';

		$this->db->from('poli');
		if (!empty($search)) {
			$this->db->where("nama_poli ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$total = $this->db->count_all_results();

		$this->db->from('poli');
		if (!empty($search)) {
			$this->db->where("nama_poli ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$this->db->order_by('id_poli', 'DESC');
		$this->db->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		foreach ($data as $row) {
			$row->id = (int) $row->id_poli;
		}

		echo json_encode(array(
			'draw'            => $draw,
			'recordsTotal'    => $total,
			'recordsFiltered' => $total,
			'data'            => $data ?: array()
		));
	}
}


