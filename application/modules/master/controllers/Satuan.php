<?php defined('BASEPATH') || exit('No direct script access allowed');

class Satuan extends App_Controller
{
	protected $permission = 'Master.Satuan.Manage';

	public function __construct()
	{
		parent::__construct();
		if (! $this->auth->has_permission($this->permission)) {
			Template::set_message('Anda tidak memiliki akses ke master data.', 'attention');
			Template::redirect('dashboard');
		}
		$this->load->model('master/satuan_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
		Template::set_block('sub_nav', 'satuan/_sub_nav');
		Assets::add_module_js('master', 'satuan.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Satuan Obat');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save']) && $this->save_satuan('insert')) {
			Template::set_message('Satuan obat berhasil ditambahkan.', 'success');
			redirect(SITE_AREA . '/master/satuan');
		}
		Template::set('toolbar_title', 'Tambah Satuan Obat');
		Template::render();
	}

	public function edit($id = null)
	{
		$id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Satuan tidak valid.', 'error');
			redirect(SITE_AREA . '/master/satuan');
		}
		if (isset($_POST['save']) && $this->save_satuan('update', $id)) {
			Template::set_message('Satuan obat berhasil diperbarui.', 'success');
			redirect(SITE_AREA . '/master/satuan');
		}
		Template::set('satuan', $this->satuan_model->find($id));
		Template::set('toolbar_title', 'Edit Satuan Obat');
		Template::render();
	}

	private function save_satuan($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->satuan_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}
		$data = array(
			'nama_satuan' => $this->input->post('nama_satuan'),
			'status'      => $this->input->post('status') ?: 'AKTIF',
		);
		if ($type === 'insert') {
			return $this->satuan_model->insert($data);
		}
		return $this->satuan_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';
		
		$build = function () use ($search) {
			$this->db->from('master_satuan');
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->where("nama_satuan LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->group_end();
			}
		};
		
		$build();
		$total = $this->db->count_all_results();
		
		$build();
		$this->db->order_by('id_satuan', 'DESC')
			->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		
		foreach ($data as $row) {
			$row->id = (int) $row->id_satuan;
		}
		
		echo json_encode(array('draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data ?: array()));
	}

	public function delete($id = null)
	{
		$id = (int) $id;
		if ($id <= 0) {
			echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
			return;
		}

		$n = $this->db->where('id_satuan', $id)->count_all_results('obat');
		if ($n > 0) {
			echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus satuan karena sudah dipakai oleh ' . $n . ' obat. Silakan nonaktifkan saja statusnya.'));
			return;
		}

		if ($this->satuan_model->delete($id)) {
			echo json_encode(array('success' => true, 'message' => 'Satuan obat berhasil dihapus.'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Gagal menghapus satuan obat.'));
		}
	}
}
