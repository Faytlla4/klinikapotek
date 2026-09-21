<?php defined('BASEPATH') || exit('No direct script access allowed');

class Pelayanan extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permission);
		$this->load->model('master/pelayanan_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");

		Template::set_block('sub_nav', 'pelayanan/_sub_nav');
		Assets::add_module_js('master', 'pelayanan.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Pelayanan');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($id = $this->save_pelayanan('insert')) {
				Template::set_message('Pelayanan berhasil ditambahkan.', 'success');
				redirect(SITE_AREA . '/master/pelayanan');
			}
		}

		Template::set('toolbar_title', 'Tambah Pelayanan');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id)) {
			Template::set_message('ID Pelayanan tidak valid.', 'error');
			redirect(SITE_AREA . '/master/pelayanan');
		}

		if (isset($_POST['save'])) {
			if ($this->save_pelayanan('update', $id)) {
				Template::set_message('Pelayanan berhasil diperbarui.', 'success');
				redirect(SITE_AREA . '/master/pelayanan');
			}
		}

		Template::set('pelayanan', $this->pelayanan_model->find($id));
		Template::set('toolbar_title', 'Edit Pelayanan');
		Template::render();
	}

	private function save_pelayanan($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->pelayanan_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}

		$nama = trim($this->input->post('nama_pelayanan'));
		$exists = $this->db->where('LOWER(nama_pelayanan)', strtolower($nama));
		if ($type == 'update') {
			$exists->where('id_pelayanan !=', $id);
		}
		if ($exists->count_all_results('pelayanan') > 0) {
			Template::set_message('Nama pelayanan "' . $nama . '" sudah ada.', 'error');
			return false;
		}

		$data = array(
			'nama_pelayanan'  => $nama,
			'jenis_pelayanan' => $this->input->post('jenis_pelayanan'),
			'tarif'           => $this->input->post('tarif'),
			'status'          => $this->input->post('status') ? $this->input->post('status') : 'AKTIF',
		);

		if ($type == 'insert') {
			return $this->pelayanan_model->insert($data);
		}

		return $this->pelayanan_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';

		$this->db->from('pelayanan');
		if (!empty($search)) {
			$this->db->where("nama_pelayanan LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("jenis_pelayanan LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$total = $this->db->count_all_results();

		$this->db->from('pelayanan');
		if (!empty($search)) {
			$this->db->where("nama_pelayanan LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
			$this->db->or_where("jenis_pelayanan LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
		}
		$this->db->order_by('id_pelayanan', 'DESC');
		$this->db->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		foreach ($data as $row) {
			$row->id = (int) $row->id_pelayanan;
		}

		echo json_encode(array(
			'draw'            => $draw,
			'recordsTotal'    => $total,
			'recordsFiltered' => $total,
			'data'            => $data ?: array()
		));
	}

	public function delete($id = null)
	{
		$id = (int) $id;
		if ($id <= 0) {
			echo json_encode(array('success' => false, 'message' => 'ID tidak valid.'));
			return;
		}

		$guna = array();
		$n = $this->db->where('id_pelayanan', $id)->count_all_results('kunjungan');
		if ($n > 0) $guna[] = $n . ' kunjungan';

		if (!empty($guna)) {
			echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus pelayanan karena masih memiliki ' . implode(', ', $guna) . '.'));
			return;
		}

		if ($this->pelayanan_model->delete($id)) {
			echo json_encode(array('success' => true, 'message' => 'Pelayanan berhasil dihapus.'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Gagal menghapus pelayanan.'));
		}
	}
}


