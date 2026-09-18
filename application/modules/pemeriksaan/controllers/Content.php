<?php defined('BASEPATH') || exit('No direct script access allowed');

class Content extends App_Controller
{
	protected $permission = 'kelola_pemeriksaan';

	/** Context aktif (sidebar/redirect). Child per-context meng-override. */
	protected $ctx = 'content';

	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict($this->permission);
		$this->load->model('pemeriksaan/pemeriksaan_model');
		$this->load->model('master/dokter_model');
		$this->load->model('pemeriksaan/diagnosis_model');
		$this->load->model('pemeriksaan/tindakan_model');
		$this->load->model('kunjungan/kunjungan_model');
		$this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('pemeriksaan', 'pemeriksaan.js');
	}

	public function index()
	{
		Template::set('toolbar_title', 'Pemeriksaan dan Rekam Medis');
		Template::render();
	}

	public function create()
	{
		$dokter_sendiri = $this->dokter_sendiri();
		if ($dokter_sendiri === false && $this->hanya_dokter()) {
			Template::set_message('Akun belum dipetakan ke data dokter.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/pemeriksaan');
		}
		if (isset($_POST['save']) && $this->save_pemeriksaan($dokter_sendiri)) {
			Template::set_message('Pemeriksaan berhasil disimpan.', 'success');
			redirect(SITE_AREA . '/' . $this->ctx . '/pemeriksaan');
		}
		$this->set_kunjungan($dokter_sendiri);
		Template::set('toolbar_title', 'Tambah Pemeriksaan');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		$row = $this->pemeriksaan_model->find($id);
		if (! $row || ! $this->boleh_akses($row->id_dokter)) {
			Template::set_message('Pemeriksaan tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/pemeriksaan');
		}
		if (isset($_POST['save'])) {
			$data = array(
				'keluhan' => $this->input->post('keluhan'),
				'hasil_pemeriksaan' => $this->input->post('hasil_pemeriksaan'),
				'catatan_dokter' => $this->input->post('catatan_dokter'),
			);
			if ($this->pemeriksaan_model->update($id, $data)) {
				Template::set_message('Pemeriksaan diperbarui.', 'success');
				redirect(SITE_AREA . '/' . $this->ctx . '/pemeriksaan');
			}
		}
		Template::set('pemeriksaan', $row);
		Template::set('toolbar_title', 'Edit Pemeriksaan');
		Template::render();
	}

	public function detail($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
		$row = $this->pemeriksaan_model->rekam_medis($id);
		if (! $row || ! $this->boleh_akses($row->id_dokter)) {
			Template::set_message('Pemeriksaan tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/' . $this->ctx . '/pemeriksaan');
		}
		
		$this->load->model('master/obat_model');
		$this->load->model('resep/resep_model');
		
		// Jika sudah ada resep, ambil detail obatnya
		if (!empty($row->resep)) {
			foreach ($row->resep as $rs) {
				$rs->detail = $this->db->select('resep_detail.*, obat.nama_obat, obat.satuan')
									   ->join('obat', 'obat.id_obat = resep_detail.id_obat')
									   ->where('id_resep', $rs->id_resep)
									   ->get('resep_detail')->result();
			}
		} else {
			// Jika belum ada resep, siapkan list obat aktif untuk form
			Template::set('obat_list', $this->obat_model->aktif());
		}
		
		// Ambil data kunjungan untuk mendapatkan id_pasien
		$kunjungan = $this->db->where('id_kunjungan', $row->id_kunjungan)->get('kunjungan')->row();
		Template::set('kunjungan', $kunjungan);
		
		Template::set('pemeriksaan', $row);
		Template::set('toolbar_title', 'Detail Rekam Medis & Resep');
		Template::render();
	}

	private function set_kunjungan($dokter_sendiri = null)
	{
		$this->db->select('kunjungan.*, pasien.no_rm, pasien.nama AS nama_pasien, dokter.nama_dokter')
			->join('pasien', 'pasien.id_pasien = kunjungan.id_pasien')
			->join('dokter', 'dokter.id_dokter = kunjungan.id_dokter')
			->where_in('kunjungan.status', array('TERDAFTAR', 'MENUNGGU', 'DIPROSES'))
			->where('kunjungan.id_kunjungan NOT IN (SELECT id_kunjungan FROM pemeriksaan)', null, false);
		if ($dokter_sendiri) {
			$this->db->where('kunjungan.id_dokter', $dokter_sendiri);
		}
		Template::set('kunjungan_list', $this->db->get('kunjungan')->result());
		if ($dokter_sendiri) {
			Template::set('dokter_list', $this->db->where('id_dokter', $dokter_sendiri)->get('dokter')->result());
			Template::set('dokter_terkunci', $dokter_sendiri);
		} else {
			Template::set('dokter_list', $this->db->get('dokter')->result());
		}
	}

	private function save_pemeriksaan($dokter_sendiri = null)
	{
		if ($dokter_sendiri) {
			$_POST['id_dokter'] = $dokter_sendiri;
		}
		$this->form_validation->set_rules(array(
			array('field' => 'id_kunjungan', 'label' => 'Kunjungan', 'rules' => 'required|integer'),
			array('field' => 'id_dokter', 'label' => 'Dokter', 'rules' => 'required|integer'),
		));
		if ($this->form_validation->run() === false) {
			return false;
		}
		$post = $this->input->post();
		$id = $this->pemeriksaan_model->buka($post['id_kunjungan'], $post['id_dokter'], $post);
		if (! $id) {
			Template::set_message($this->pemeriksaan_model->error ?: 'Gagal menyimpan pemeriksaan.', 'error');
			return false;
		}
		$this->load->model('audit/audit_log_model');
		$this->audit_log_model->catat($this->auth->user_id(), 'create', 'pemeriksaan', $id, '');
		return true;
	}

	public function get_data()
	{
		$sendiri = $this->dokter_sendiri();
		$rows = $sendiri === false ? array() : $this->pemeriksaan_model->daftar($sendiri);
		$request = $this->input->post();
		$search = trim($request['search']['value'] ?? '');
		if ($search !== '') {
			$rows = array_values(array_filter($rows, function ($row) use ($search) {
				return stripos($row->nama_pasien, $search) !== false || stripos($row->no_rm, $search) !== false || stripos($row->nama_dokter, $search) !== false;
			}));
		}
		// Tambahkan property id untuk bfDataTable
		foreach ($rows as $r) {
			$r->id = $r->id_pemeriksaan;
		}
		$start = (int) ($request['start'] ?? 0);
		$length = (int) ($request['length'] ?? 10);
		echo json_encode(array('draw' => (int) ($request['draw'] ?? 1), 'recordsTotal' => count($rows), 'recordsFiltered' => count($rows), 'data' => array_slice($rows, $start, $length)));
	}

	/** True bila user adalah dokter murni (bukan pelayanan/admin). */
	private function hanya_dokter()
	{
		return $this->auth->has_permission('kelola_pemeriksaan')
			&& ! $this->auth->has_permission('kelola_pendaftaran');
	}

	/**
	 * id_dokter milik user login bila hanya_dokter (filter data sendiri).
	 * Null = tanpa filter (pelayanan/admin). False = dokter belum dipetakan.
	 *
	 * @return int|null|bool
	 */
	private function dokter_sendiri()
	{
		if (! $this->hanya_dokter()) {
			return null;
		}
		$dokter = $this->dokter_model->dari_user($this->auth->user_id());
		return $dokter ? (int) $dokter->id_dokter : false;
	}

	/** Dokter hanya boleh membuka pemeriksaan miliknya sendiri. */
	private function boleh_akses($id_dokter)
	{
		$sendiri = $this->dokter_sendiri();
		return $sendiri === null || ($sendiri !== false && (int) $id_dokter === (int) $sendiri);
	}
}

