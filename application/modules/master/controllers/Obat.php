<?php defined('BASEPATH') || exit('No direct script access allowed');

class Obat extends App_Controller
{
	protected $permission = 'kelola_master_data';

	public function __construct()
	{
		parent::__construct();
		if (! $this->auth->has_permission($this->permission)
			&& ! $this->auth->has_permission('kelola_stok_obat')) {
			Template::set_message('Anda tidak memiliki akses ke master obat.', 'attention');
			Template::redirect('dashboard');
		}
		$this->load->model('master/obat_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
		Template::set_block('sub_nav', 'obat/_sub_nav');
		Assets::add_module_js('master', 'obat.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Kelola Master Obat');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save']) && $this->save_obat('insert')) {
			Template::set_message('Obat berhasil ditambahkan.', 'success');
			redirect(SITE_AREA . '/master/obat');
		}
		Template::set('toolbar_title', 'Tambah Obat');
		Template::render();
	}

	public function edit($id = null)
	{
		if (empty($id)) {
			Template::set_message('ID Obat tidak valid.', 'error');
			redirect(SITE_AREA . '/master/obat');
		}
		if (isset($_POST['save']) && $this->save_obat('update', $id)) {
			Template::set_message('Obat berhasil diperbarui.', 'success');
			redirect(SITE_AREA . '/master/obat');
		}
		Template::set('obat', $this->obat_model->find($id));
		Template::set('toolbar_title', 'Edit Obat');
		Template::render();
	}

	private function save_obat($type = 'insert', $id = 0)
	{
		$this->form_validation->set_rules($this->obat_model->get_validation_rules($type));
		if ($this->form_validation->run() === false) {
			return false;
		}
		$data = array(
			'kode_obat'    => $this->input->post('kode_obat'),
			'nama_obat'    => $this->input->post('nama_obat'),
			'jenis_obat'   => $this->input->post('jenis_obat'),
			'satuan'       => $this->input->post('satuan'),
			'harga'        => $this->input->post('harga'),
			'stok_minimum' => $this->input->post('stok_minimum') ?: 0,
			'wajib_resep'  => $this->input->post('wajib_resep') === 'true' ? 'true' : 'false',
			'status'       => $this->input->post('status') ?: 'AKTIF',
		);
		if ($type === 'insert') {
			return $this->obat_model->insert($data);
		}
		unset($data['kode_obat']);
		return $this->obat_model->update($id, $data);
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = $request['search']['value'] ?? '';
		$build = function () use ($search) {
			$this->db->select('obat.*, COALESCE(stok_obat.jumlah_stok, 0) AS stok')
				->from('obat')
				->join('stok_obat', 'stok_obat.id_obat = obat.id_obat', 'left');
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('obat.kode_obat', $search);
				$this->db->or_like('obat.nama_obat', $search);
				$this->db->or_like('obat.jenis_obat', $search);
				$this->db->group_end();
			}
		};
		$build();
		$total = $this->db->count_all_results();
		$build();
		$this->db->order_by('obat.id_obat', 'DESC')
			->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		echo json_encode(array('draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data ?: array()));
	}
}
