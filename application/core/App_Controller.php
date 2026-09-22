<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Admin Controller
 *
 * This class provides a base class for all admin-facing controllers.
 * It automatically loads the form, form_validation and pagination
 * helpers/libraries, sets defaults for pagination and sets our
 * Admin Theme.
 *
 * @package    Bonfire
 * @subpackage MY_Controller
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class App_Controller extends Authenticated_Controller
{
	/** Dokter aktif dari sesi; false bila tidak ada/tidak lagi aktif. */
	protected function dokter_aktif()
	{
		$id = (int) $this->session->userdata('id_dokter_aktif');
		if ($id <= 0) {
			return false;
		}
		$row = $this->db->where('id_dokter', $id)->where('status', 'AKTIF')->get('dokter')->row();
		if (! $row) {
			$this->session->unset_userdata('id_dokter_aktif');
			return false;
		}
		return $row;
	}

	//--------------------------------------------------------------------

	/**
	 * Class constructor - setup paging and keyboard shortcuts as well as
	 * load various libraries
	 *
	 */
	public function __construct()
	{
		$this->autoload['libraries'][] = 'ui/contextslte';

		parent::__construct();

		// ponytail: seed permission konteks baru (mis. LAPORAN CETAK) di sini,
		// bukan di controller modul — submenu kosong tidak bisa diklik untuk
		// mencapai halaman seed-nya (AdminLTE cegah navigasi parent).
		$this->pastikan_permission_cetak();
		$this->pastikan_permission_backup();

		// Profiler Bar?
		if (ENVIRONMENT == 'development') {
			if ($this->settings_lib->item('site.show_profiler')
				&& $this->auth->has_permission('Bonfire.Profiler.View')
			) {
				// Profiler bar?
				$this->showProfiler(false);
			}
		}

		// Basic setup
		Template::set_theme($this->config->item('template.adminlte_theme'), $this->config->item('template.default_theme'));
	}

	/**
	 * Buat sendiri permission konteks LAPORAN CETAK bila belum ada, plus
	 * perbaiki assignment role yang tertinggal. Idempoten; 1 query COUNT
	 * murah per request bila sudah lengkap.
	 */
	protected function pastikan_permission_cetak()
	{
		if ($this->db->dbdriver !== 'postgre') {
			return;
		}
		$cetak = array('Laporan.Cetak.View', 'Site.Cetak.View');
		$ada = $this->db->select('COUNT(*) AS n', false)
			->where_in('nama_permission', $cetak)
			->get('permissions')->row();
		$segar = false;
		if (empty($ada) || (int) $ada->n !== 2) {
			$this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
				('Laporan.Cetak.View', 'LAPORAN'),
				('Site.Cetak.View', 'MANAJEMEN_SISTEM')
				ON CONFLICT (nama_permission) DO NOTHING");
			$segar = true;
		}
		// Role saya boleh lihat laporan tapi belum kebagian menu cetak? Perbaiki.
		$role_id = (int) $this->auth->role_id();
		if ($role_id > 0) {
			$boleh = $this->db->select('COUNT(*) AS n', false)
				->from('role_permissions rp')
				->join('permissions p', 'p.id_permission = rp.id_permission')
				->where('rp.id_role', $role_id)
				->where('p.nama_permission', 'lihat_laporan')
				->get()->row();
			if (! empty($boleh) && (int) $boleh->n > 0) {
				$punya = $this->db->select('COUNT(*) AS n', false)
					->from('role_permissions rp')
					->join('permissions p', 'p.id_permission = rp.id_permission')
					->where('rp.id_role', $role_id)
					->where_in('p.nama_permission', $cetak)
					->get()->row();
				if (empty($punya) || (int) $punya->n !== 2) {
					$this->db->query('INSERT INTO role_permissions (id_role, id_permission)
						SELECT ' . $role_id . ', p.id_permission
						FROM permissions p
						WHERE p.nama_permission IN (' . "'Laporan.Cetak.View', 'Site.Cetak.View'" . ')
						ON CONFLICT (id_role, id_permission) DO NOTHING');
					$segar = true;
				}
			}
		}
		// Muat ulang sekali agar cache permission Auth ikut segar.
		if ($segar) {
			$qs = ! empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
			redirect($this->uri->uri_string() . $qs);
		}
	}

	/**
	 * Buat sendiri permission BACKUP DATABASE bila belum ada + perbaiki
	 * assignment role yang tertinggal. Idempoten.
	 */
	protected function pastikan_permission_backup()
	{
		if ($this->db->dbdriver !== 'postgre') {
			return;
		}
		$perm = 'Backup.Settings.View';
		$ada = $this->db->select('COUNT(*) AS n', false)
			->where('nama_permission', $perm)
			->get('permissions')->row();
		$segar = false;
		if (empty($ada) || (int) $ada->n !== 1) {
			$this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
				('Backup.Settings.View', 'MANAJEMEN_SISTEM')
				ON CONFLICT (nama_permission) DO NOTHING");
			$segar = true;
		}
		$role_id = (int) $this->auth->role_id();
		if ($role_id > 0) {
			$boleh = $this->db->select('COUNT(*) AS n', false)
				->from('role_permissions rp')
				->join('permissions p', 'p.id_permission = rp.id_permission')
				->where('rp.id_role', $role_id)
				->where('p.nama_permission', 'Site.Settings.View')
				->get()->row();
			if (! empty($boleh) && (int) $boleh->n > 0) {
				$punya = $this->db->select('COUNT(*) AS n', false)
					->from('role_permissions rp')
					->join('permissions p', 'p.id_permission = rp.id_permission')
					->where('rp.id_role', $role_id)
					->where('p.nama_permission', $perm)
					->get()->row();
				if (empty($punya) || (int) $punya->n !== 1) {
					$this->db->query('INSERT INTO role_permissions (id_role, id_permission)
						SELECT ' . $role_id . ', p.id_permission
						FROM permissions p
						WHERE p.nama_permission = \'' . $perm . '\'
						ON CONFLICT (id_role, id_permission) DO NOTHING');
					$segar = true;
				}
			}
		}
		if ($segar) {
			$qs = ! empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
			redirect($this->uri->uri_string() . $qs);
		}
	}
}
/* End of file Admin_Controller.php */
