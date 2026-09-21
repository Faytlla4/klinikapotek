<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
	protected $permission = 'kelola_pasien';

	/** Context aktif (sidebar/redirect). Child per-context meng-override. */
	protected $ctx = 'content';

	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict($this->permission);
		$this->load->model('pasien/pasien_model');
		$this->load->model('audit/audit_log_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('pasien', 'pasien.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Data Pasien');
		Template::render();
	}

	public function create()
	{
		if ($this->input->method() === 'post' && $this->save_pasien('insert')) {
			Template::set_message('Pasien berhasil ditambahkan.', 'success');
			redirect(SITE_AREA . '/' . $this->ctx . '/pasien');
		}
		Template::set('toolbar_title', 'Tambah Pasien');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		if (empty($id) || ! $this->pasien_model->find($id)) {
			Template::set_message('ID Pasien tidak valid.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/pasien');
		}
		if ($this->input->method() === 'post' && $this->save_pasien('update', $id)) {
			Template::set_message('Data pasien berhasil diperbarui.', 'success');
			redirect(SITE_AREA . '/' . $this->ctx . '/pasien');
		}
		Template::set('pasien', $this->pasien_model->find($id));
		Template::set('toolbar_title', 'Edit Pasien');
		Template::render();
	}

	public function detail($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		$pasien = $this->pasien_model->detail($id);
		if (! $pasien) {
			Template::set_message('Pasien tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/pasien');
		}
		Template::set('pasien', $pasien);
		Template::set('toolbar_title', 'Detail Pasien');
		Template::render();
	}

	private function save_pasien($type = 'insert', $id = 0)
	{
		$rules = $this->pasien_model->get_validation_rules($type);
		if ($type === 'insert') {
			// no_rm dibuat setelah validasi form; user tidak boleh mengisinya.
			$rules = array_filter($rules, function ($rule) {
				return $rule['field'] !== 'no_rm';
			});
		}
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() === false) {
			return false;
		}
		if (! $this->pasien_model->nik_tersedia($this->input->post('nik'), $type === 'update' ? $id : null)) {
			Template::set_message('NIK sudah terdaftar.', 'error');
			return false;
		}

		$data = array(
			'nama'          => $this->input->post('nama'),
			'nik'           => $this->input->post('nik'),
			'tanggal_lahir' => $this->input->post('tanggal_lahir') ?: null,
			'jenis_kelamin' => $this->input->post('jenis_kelamin') ?: null,
			'alamat'        => $this->input->post('alamat'),
			'no_hp'         => $this->input->post('no_hp'),
			'status'        => $this->input->post('status') ?: 'AKTIF',
		);
		if ($type === 'insert') {
			$id = $this->pasien_model->daftar($data);
			$aksi = 'create';
		} else {
			$id = $this->pasien_model->update($id, $data) ? $id : false;
			$aksi = 'update';
		}
		if (! $id) {
			return false;
		}
		$this->audit_log_model->catat($this->auth->user_id(), $aksi, 'pasien', $id, '');
		return $id;
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = trim($request['search']['value'] ?? '');
		$build = function () use ($search) {
			$this->db->from('pasien');
			if ($search !== '') {
				$this->db->group_start();
				$this->db->where("no_rm LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->or_where("nik LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->or_where("nama LIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->group_end();
			}
		};
		$build();
		$total = $this->db->count_all_results();
		$build();
		$this->db->order_by('id_pasien', 'DESC')->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		// bfDataTable builds row links from `id`; expose the module's primary
		// key under that conventional name without changing the database data.
		foreach ($data as $row) {
			$row->id = (int) $row->id_pasien;
		}
		$payload = array('draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data ?: array());
		// Data lama yang tidak valid UTF-8 must not turn the entire AJAX
		// response into an empty string (which DataTables reports as invalid JSON).
		$json = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
		if ($json === false) {
			log_message('error', 'Gagal membuat JSON tabel pasien: ' . json_last_error_msg());
			$json = json_encode(array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array()));
		}
		$this->output->set_content_type('application/json')->set_output($json);
	}
}


