<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (! $this->auth->has_permission('kelola_antrian') && ! $this->auth->has_permission('kelola_antrian_dokter')) {
			$this->auth->restrict('kelola_antrian');
		}
		$this->load->model('antrian/antrian_model');
		$this->load->model('master/dokter_model');
		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('antrian', 'antrian.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Data Antrian');
		Template::render();
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw = (int) ($request['draw'] ?? 1);
		$search = trim($request['search']['value'] ?? '');
		$id_dokter = $this->input->get('id_dokter');
		if ($this->auth->has_permission('kelola_antrian_dokter') && ! $this->auth->has_permission('kelola_antrian')) {
			// Dokter hanya melihat antriannya sendiri; parameter URL diabaikan (anti-IDOR).
			$dokter = $this->dokter_model->dari_user($this->auth->user_id());
			$rows = $dokter ? $this->antrian_model->untuk_dokter($dokter->id_dokter) : array();
		} else {
			$rows = $id_dokter ? $this->antrian_model->untuk_dokter($id_dokter) : $this->antrian_model->hari_ini();
		}
		if ($search !== '') {
			$rows = array_values(array_filter($rows, function ($row) use ($search) {
				return stripos($row->nomor_antrian, $search) !== false
					|| stripos($row->nama_pasien, $search) !== false
					|| stripos($row->nama_poli, $search) !== false;
			}));
		}
		$start = (int) ($request['start'] ?? 0);
		$length = (int) ($request['length'] ?? 10);
		$data = $length > 0 ? array_slice($rows, $start, $length) : $rows;
		echo json_encode(array('draw' => $draw, 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => $data));
	}
}
