<?php defined('BASEPATH') || exit('No direct script access allowed');

class Dokter extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permission);
		$this->load->model('master/dokter_model');
		$this->load->model('master/spesialis_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");

		Template::set_block('sub_nav', 'dokter/_sub_nav');
		Assets::add_module_js('master', 'dokter.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Dokter');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($id = $this->save_dokter('insert')) {
				Template::set_message('Dokter berhasil ditambahkan.', 'success');
				redirect(SITE_AREA . '/master/dokter');
			}
		}

		Template::set('spesialis_list', $this->spesialis_model->aktif());
		Template::set('toolbar_title', 'Tambah Dokter');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Dokter tidak valid.', 'error');
			redirect(SITE_AREA . '/master/dokter');
		}

		if (isset($_POST['save'])) {
			if ($this->save_dokter('update', $id)) {
				Template::set_message('Dokter berhasil diperbarui.', 'success');
				redirect(SITE_AREA . '/master/dokter');
			}
		}

		Template::set('dokter', $this->dokter_model->find($id));
		Template::set('spesialis_list', $this->spesialis_model->aktif());
		Template::set('toolbar_title', 'Edit Dokter');
		Template::render();
	}

	private function save_dokter($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->dokter_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'nama_dokter'  => $this->input->post('nama_dokter'),
			'id_spesialis' => $this->input->post('id_spesialis') ? $this->input->post('id_spesialis') : null,
			'no_sip'       => $this->input->post('no_sip'),
			'no_hp'        => $this->input->post('no_hp'),
			'tarif'        => $this->input->post('tarif'),
			'status'       => $this->input->post('status') ? $this->input->post('status') : 'AKTIF',
		);

		if ($type == 'insert') {
			return $this->dokter_model->insert($data);
		}

		return $this->dokter_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';

		$this->db->select('dokter.*, spesialis.nama_spesialis')
			->from('dokter')
			->join('spesialis', 'spesialis.id_spesialis = dokter.id_spesialis', 'left');

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->where("dokter.nama_dokter ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("dokter.no_sip ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("spesialis.nama_spesialis ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->group_end();
		}
		$total = $this->db->count_all_results();

		$this->db->select('dokter.*, spesialis.nama_spesialis')
			->from('dokter')
			->join('spesialis', 'spesialis.id_spesialis = dokter.id_spesialis', 'left');

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->where("dokter.nama_dokter ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("dokter.no_sip ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("spesialis.nama_spesialis ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->group_end();
		}
		$this->db->order_by('dokter.id_dokter', 'DESC');
		$this->db->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		foreach ($data as $row) {
			$row->id = (int) $row->id_dokter;
		}

		echo json_encode(array(
			'draw'            => $draw,
			'recordsTotal'    => $total,
			'recordsFiltered' => $total,
			'data'            => $data ?: array()
		));
	}
}


