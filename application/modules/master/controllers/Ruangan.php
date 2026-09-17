<?php defined('BASEPATH') || exit('No direct script access allowed');

class Ruangan extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permission);
		$this->load->model('master/ruangan_model');
		$this->load->model('master/poli_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");

		Template::set_block('sub_nav', 'ruangan/_sub_nav');
		Assets::add_module_js('master', 'ruangan.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Ruangan');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($id = $this->save_ruangan('insert')) {
				Template::set_message('Ruangan berhasil ditambahkan.', 'success');
				redirect(SITE_AREA . '/master/ruangan');
			}
		}

		Template::set('poli_list', $this->poli_model->aktif());
		Template::set('toolbar_title', 'Tambah Ruangan');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Ruangan tidak valid.', 'error');
			redirect(SITE_AREA . '/master/ruangan');
		}

		if (isset($_POST['save'])) {
			if ($this->save_ruangan('update', $id)) {
				Template::set_message('Ruangan berhasil diperbarui.', 'success');
				redirect(SITE_AREA . '/master/ruangan');
			}
		}

		Template::set('ruangan', $this->ruangan_model->find($id));
		Template::set('poli_list', $this->poli_model->aktif());
		Template::set('toolbar_title', 'Edit Ruangan');
		Template::render();
	}

	private function save_ruangan($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->ruangan_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'nama_ruangan' => $this->input->post('nama_ruangan'),
			'id_poli'      => $this->input->post('id_poli') ? $this->input->post('id_poli') : null,
			'status'       => $this->input->post('status') ? $this->input->post('status') : 'AKTIF',
		);

		if ($type == 'insert') {
			return $this->ruangan_model->insert($data);
		}

		return $this->ruangan_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';

		$this->db->select('ruangan.*, poli.nama_poli')
			->from('ruangan')
			->join('poli', 'poli.id_poli = ruangan.id_poli', 'left');

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->where("ruangan.nama_ruangan ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("poli.nama_poli ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->group_end();
		}
		$total = $this->db->count_all_results();

		$this->db->select('ruangan.*, poli.nama_poli')
			->from('ruangan')
			->join('poli', 'poli.id_poli = ruangan.id_poli', 'left');

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->where("ruangan.nama_ruangan ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("poli.nama_poli ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->group_end();
		}
		$this->db->order_by('ruangan.id_ruangan', 'DESC');
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


