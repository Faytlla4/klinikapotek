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
}
/* End of file Admin_Controller.php */
