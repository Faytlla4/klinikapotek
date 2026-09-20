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
		Template::set('kode_obat_baru', $this->obat_model->generate_kode());
		Template::set('toolbar_title', 'Tambah Obat');
		Template::render();
	}

	public function edit($id = null)
	{
        $id = (int) $id > 0 ? (int) $id : 0;
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
			'nama_obat'    => $this->input->post('nama_obat'),
			'jenis_obat'   => $this->input->post('jenis_obat'),
			'satuan'       => $this->input->post('satuan'),
			'harga'        => $this->input->post('harga'),
			'stok_minimum' => $this->input->post('stok_minimum') ?: 0,
			'wajib_resep'  => $this->input->post('wajib_resep') === 'true' ? 'true' : 'false',
			'status'       => $this->input->post('status') ?: 'AKTIF',
		);
		if ($type === 'insert') {
			$data['kode_obat'] = $this->obat_model->generate_kode();
			return $this->obat_model->insert($data);
		}
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
				$this->db->where("obat.kode_obat ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->or_where("obat.nama_obat ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->or_where("obat.jenis_obat ILIKE '%" . $this->db->escape_like_str($search) . "%'", NULL, FALSE);
				$this->db->group_end();
			}
		};
		$build();
		$total = $this->db->count_all_results();
		$build();
		$this->db->order_by('obat.id_obat', 'DESC')
			->limit((int) ($request['length'] ?? 10), (int) ($request['start'] ?? 0));
		$data = $this->db->get()->result();
		foreach ($data as $row) {
			$row->id = (int) $row->id_obat;
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

		$guna = array();
		$n = $this->db->where('id_obat', $id)->count_all_results('stok_obat');
		if ($n > 0) $guna[] = $n . ' data stok';
		$n = $this->db->where('id_obat', $id)->count_all_results('mutasi_stok');
		if ($n > 0) $guna[] = $n . ' mutasi stok';
		$n = $this->db->where('id_obat', $id)->count_all_results('resep_detail');
		if ($n > 0) $guna[] = $n . ' resep';
		$n = $this->db->where('id_obat', $id)->count_all_results('penjualan_obat_detail');
		if ($n > 0) $guna[] = $n . ' penjualan';
		$n = $this->db->where('id_obat', $id)->count_all_results('penerimaan_obat_detail');
		if ($n > 0) $guna[] = $n . ' penerimaan';
		$n = $this->db->where('id_obat', $id)->count_all_results('pengadaan_obat_detail');
		if ($n > 0) $guna[] = $n . ' pengadaan';
		$n = $this->db->where('id_obat', $id)->count_all_results('retur_pengadaan_detail');
		if ($n > 0) $guna[] = $n . ' retur';
		$n = $this->db->where('id_obat', $id)->count_all_results('pesanan_online_detail');
		if ($n > 0) $guna[] = $n . ' pesanan online';

		if (!empty($guna)) {
			echo json_encode(array('success' => false, 'message' => 'Tidak bisa menghapus obat karena masih memiliki ' . implode(', ', $guna) . '.'));
			return;
		}

		if ($this->obat_model->delete($id)) {
			echo json_encode(array('success' => true, 'message' => 'Obat berhasil dihapus.'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Gagal menghapus obat.'));
		}
	}
}


