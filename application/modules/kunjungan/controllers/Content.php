<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
	protected $permission = 'kelola_pendaftaran';

	/** Context aktif (sidebar/redirect). Child per-context meng-override. */
	protected $ctx = 'content';

	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict($this->permission);
		$this->load->model('kunjungan/kunjungan_model');
		$this->load->model('pasien/pasien_model');
		$this->load->model('master/pelayanan_model');
		$this->load->model('master/poli_model');
		$this->load->model('master/dokter_model');
		$this->load->model('master/ruangan_model');
		$this->load->model('audit/audit_log_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('kunjungan', 'kunjungan.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Data Kunjungan');
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save']) && $this->save_kunjungan()) {
			Template::set_message('Kunjungan berhasil dibuat.', 'success');
			redirect(SITE_AREA . '/' . $this->ctx . '/kunjungan');
		}
		$this->set_master_data();
		Template::set('toolbar_title', 'Tambah Kunjungan');
		Template::render();
	}

	public function detail($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		$kunjungan = $this->kunjungan_model->detail($id);
		if (! $kunjungan) {
			Template::set_message('Kunjungan tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/kunjungan');
		}
		Template::set('kunjungan', $kunjungan);
		Template::set('toolbar_title', 'Detail Kunjungan');
		Template::render();
	}

	/**
	 * POST: ubah status kunjungan (TERDAFTAR→MENUNGGU→DIPROSES→SELESAI / BATAL)
	 */
	public function ubah_status($id = null)
	{
		$id = (int) $id > 0 ? (int) $id : 0;
		$status_baru = $this->input->post('status');
		$status_valid = array('MENUNGGU', 'DIPROSES', 'SELESAI', 'BATAL');

		if (! in_array($status_baru, $status_valid)) {
			Template::set_message('Status tidak valid.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/kunjungan/detail/' . $id);
		}

		$kunjungan = $this->kunjungan_model->detail($id);
		if (! $kunjungan) {
			Template::set_message('Kunjungan tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/kunjungan');
		}

		if (! $this->kunjungan_model->ubah_status($id, $status_baru)) {
			Template::set_message($this->kunjungan_model->error ?: 'Gagal mengubah status.', 'error');
		} else {
			$this->audit_log_model->catat($this->auth->user_id(), 'update', 'kunjungan', $id, 'Status -> ' . $status_baru);
			Template::set_message('Status kunjungan berhasil diubah menjadi <strong>' . $status_baru . '</strong>.', 'success');
		}
		redirect(SITE_AREA . '/' . $this->ctx . '/kunjungan/detail/' . $id);
	}

	private function set_master_data()
	{
		Template::set('pasien_list', $this->pasien_model->find_all() ?: array());
		Template::set('pelayanan_list', $this->pelayanan_model->aktif());
		Template::set('poli_list', $this->poli_model->aktif());
		Template::set('dokter_list', $this->dokter_model->aktif());
		Template::set('ruangan_list', $this->ruangan_model->aktif());
	}

	private function save_kunjungan()
	{
		$rules = array(
			array('field' => 'id_pasien',   'label' => 'Pasien',    'rules' => 'required|integer'),
			array('field' => 'id_pelayanan','label' => 'Pelayanan', 'rules' => 'required|integer'),
			array('field' => 'id_poli',     'label' => 'Poli',      'rules' => 'required|integer'),
			array('field' => 'id_dokter',   'label' => 'Dokter',    'rules' => 'required|integer'),
			array('field' => 'id_ruangan',  'label' => 'Ruangan',   'rules' => 'required|integer'),
		);
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'id_pasien'    => $this->input->post('id_pasien'),
			'id_pelayanan' => $this->input->post('id_pelayanan'),
			'id_poli'      => $this->input->post('id_poli'),
			'id_dokter'    => $this->input->post('id_dokter'),
			'id_ruangan'   => $this->input->post('id_ruangan'),
		);
		// Semua jalur pendaftaran membuat kunjungan dan antrian melalui
		// service yang sama agar tidak ada perbedaan dengan endpoint API.
		$result = $this->kunjungan_model->daftar($data);
		if (! $result) {
			Template::set_message($this->kunjungan_model->error ?: 'Gagal membuat kunjungan.', 'error');
			return false;
		}
		$this->audit_log_model->catat($this->auth->user_id(), 'create', 'kunjungan', $result['id_kunjungan'], 'No. antrian ' . $result['nomor_antrian']);
		return true;
	}

	public function get_data()
	{
		$request = $this->input->post();
		$draw    = (int) ($request['draw'] ?? 1);
		$search  = trim($request['search']['value'] ?? '');
		$build   = function () use ($search) {
			$this->db->select('kunjungan.*, pasien.no_rm, pasien.nama AS nama_pasien,
				pelayanan.nama_pelayanan, poli.nama_poli, dokter.nama_dokter, ruangan.nama_ruangan')
				->from('kunjungan')
				->join('pasien',    'pasien.id_pasien = kunjungan.id_pasien')
				->join('pelayanan', 'pelayanan.id_pelayanan = kunjungan.id_pelayanan')
				->join('poli',      'poli.id_poli = kunjungan.id_poli')
				->join('dokter',    'dokter.id_dokter = kunjungan.id_dokter')
				->join('ruangan',   'ruangan.id_ruangan = kunjungan.id_ruangan');
			if ($search !== '') {
				$this->db->group_start()
					->where("pasien.no_rm ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE)
					->or_where("pasien.nama ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE)
					->or_where("pelayanan.nama_pelayanan ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE)
					->or_where("poli.nama_poli ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE)
					->group_end();
			}
		};
		$build();
		$total = $this->db->count_all_results();
		$build();
		$this->db->order_by('kunjungan.id_kunjungan', 'DESC')
			->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		foreach ($data as $row) {
			$row->id = (int) $row->id_kunjungan;
		}
		echo json_encode(array('draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data ?: array()));
	}

	/**
	 * Mengembalikan 5 kunjungan terakhir milik pasien (JSON).
	 * Dipanggil via AJAX dari form tambah kunjungan.
	 *
	 * @param int $id_pasien
	 */
	public function get_riwayat($id_pasien)
	{
		$id_pasien = (int) $id_pasien;
		if ($id_pasien <= 0) {
			echo json_encode(array('status' => false, 'message' => 'ID pasien tidak valid.'));
			return;
		}
		$riwayat = $this->kunjungan_model->get_riwayat_by_pasien($id_pasien, 5);
		echo json_encode(array('status' => true, 'data' => $riwayat));
	}
}
