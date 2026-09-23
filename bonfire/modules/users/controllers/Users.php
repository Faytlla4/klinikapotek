<?php defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers to jumpstart the development of
 * CodeIgniter applications.
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2014, Bonfire Dev Team
 * @license   http://opensource.org/licenses/MIT
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * Users Controller.
 *
 * Provides front-end functions for users, including access to login and logout.
 *
 * @package Bonfire\Modules\Users\Controllers\Users
 * @author     Bonfire Dev Team
 * @link    http://cibonfire.com/docs/developer
 */
class Users extends Front_Controller
{
	/** @var array Site's settings to be passed to the view. */
	private $siteSettings;

	/**
	 * Setup the required libraries etc.
	 *
	 * @retun void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->load->model('users/user_model');

		$this->load->library('users/auth');

		$this->lang->load('users');

		// ponytail: tanpa tabel settings (skema apotek), pakai default
		$settings = $this->settings_lib->find_all();
		$this->siteSettings = is_array($settings) ? $settings : array();

		if (! isset($this->siteSettings['auth.password_show_labels'])) {
			$this->siteSettings['auth.password_show_labels'] = 0;
		}

		if ($this->siteSettings['auth.password_show_labels'] == 1) {
			Assets::add_module_js('users', 'password_strength.js');
			Assets::add_module_js('users', 'jquery.strength.js');
		}
	}

	// -------------------------------------------------------------------------
	// Authentication (Login/Logout)
	// -------------------------------------------------------------------------

	/**
	 * Present the login view and allow the user to login.
	 *
	 * @return void
	 */
	public function login()
	{
		// Force no-cache supaya browser tak serve redirect lama setelah logout
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
		header_remove('ETag');
		header_remove('Last-Modified');

		/*
		 * Jika session masih dianggap login, jangan kembali ke root (/).
		 * Arahkan ke halaman aplikasi yang sesuai.
		 *
		 * Untuk DOKTER, user harus memilih konteks dokter terlebih dahulu.
		 * Role lainnya masuk ke dashboard.
		 */
		if ($this->auth->is_logged_in() !== false) {
			$role = $this->db
				->select('nama_role')
				->where('id_role', $this->auth->role_id())
				->get('roles')
				->row();

			if ($role && $role->nama_role === 'DOKTER') {
				$this->session->unset_userdata('id_dokter_aktif');
				Template::redirect('dokter-bertugas');
			}

			Template::redirect('dashboard');
		}

		// Try to login.
		if (isset($_POST['log-me-in'])
			&& true === $this->auth->login(
				$this->input->post('login'),
				$this->input->post('password'),
				$this->input->post('remember_me') == '1'
			)
		) {
			$this->load->model('audit/audit_log_model');

			$this->audit_log_model->catat(
				$this->auth->user_id(),
				'login',
				'users',
				$this->auth->user_id(),
				'Login: ' . $this->input->ip_address()
			);

			// Now redirect. (If this ever changes to render something, note that
			// auth->login() currently doesn't attempt to fix `$this->current_user`
			// for the current page load).

			// If the site is configured to use role-based login destinations and
			// the login destination has been set...
			if ($this->settings_lib->item('auth.do_login_redirect')
				&& !empty($this->auth->login_destination)
			) {
				Template::redirect($this->auth->login_destination);
			}

			// Bersihkan requested_page agar tidak redirect balik ke /login
			$this->session->unset_userdata('requested_page');

			// Satu akun DOKTER memilih konteks dokter setelah autentikasi.
			$role = $this->db
				->select('nama_role')
				->where('id_role', $this->auth->role_id())
				->get('roles')
				->row();

			if ($role && $role->nama_role === 'DOKTER') {
				$this->session->unset_userdata('id_dokter_aktif');
				Template::redirect('dokter-bertugas');
			}

			// Jika tidak ada tujuan lain, masuk ke router dashboard per-role.
			Template::redirect('dashboard');
		}

		// Prompt the user to login.
		Template::set('page_title', 'Login');
		Template::render();
	}

	/**
	 * Log out, destroy the session, and cleanup, then redirect to the home page.
	 *
	 * @return void
	 */
	public function logout()
	{
		// Force no-cache supaya redirect setelah logout tak di-cache browser
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');

		// Ambil dari sesi auth (current_user tidak selalu terisi di sini;
		// skema custom memakai id_user, bukan id).
		$logout_id = (int) $this->auth->user_id();

		if ($logout_id > 0) {
			// Login session is valid. Log the Activity.
			$this->load->model('audit/audit_log_model');

			$this->audit_log_model->catat(
				$logout_id,
				'logout',
				'users',
				$logout_id,
				'Logout: ' . $this->input->ip_address()
			);
		}

		// Always clear browser data (don't silently ignore user requests).
		$this->session->unset_userdata('id_dokter_aktif');
		$this->auth->logout();

		Template::redirect('/');
	}

	// -------------------------------------------------------------------------
	// User Management (Register/Update Profile)
	// -------------------------------------------------------------------------

	/**
	 * Allow a user to edit their own profile information.
	 *
	 * @return void
	 */
	public function profile()
	{
		// Make sure the user is logged in.
		$this->auth->restrict();
		$this->set_current_user();

		// ponytail: profile dibuka dari menu user di layout admin — render di
		// tema adminlte (tema depan tak ada CSS-nya) agar ber-style.
		Template::set_theme(
			$this->config->item('template.adminlte_theme'),
			$this->config->item('template.default_theme')
		);

		$this->load->library('ui/contextslte');

		$this->load->helper('date');

		$this->load->config('address');
		$this->load->helper('address');

		$this->load->config('user_meta');
		$meta_fields = config_item('user_meta_fields');

		Template::set('meta_fields', $meta_fields);

		if (isset($_POST['save'])) {
			$user_id = $this->current_user->id;

			if ($this->saveUser('update', $user_id, $meta_fields)) {
				$user = $this->user_model->find($user_id);
				$log_name = !empty($user->nama) ? $user->nama : $user->username;

				log_activity(
					$this->current_user->id,
					lang('us_log_edit_profile') . ": {$log_name}",
					'users'
				);

				Template::set_message(
					lang('us_profile_updated_success'),
					'success'
				);

				// Redirect to make sure any language changes are picked up.
				Template::redirect('/users/profile');
			}

			Template::set_message(
				lang('us_profile_updated_error'),
				'error'
			);
		}

		// Get the current user information.
		$user = $this->user_model->find_user_and_meta(
			$this->current_user->id
		);

		if ($this->siteSettings['auth.password_show_labels'] == 1) {
			Assets::add_js(
				$this->load->view(
					'users_js',
					array('settings' => $this->siteSettings),
					true
				),
				'inline'
			);
		}

		Template::set('user', $user);
		Template::set('page_title', lang('us_profile'));
		Template::render();
	}

	/**
	 * Register a new user.
	 *
	 * @return void
	 */
	public function register()
	{

// If the user is already logged in, go to the application dashboard.
// Jangan redirect ke '/' karena '/' adalah landing page publik.
if ($this->auth->is_logged_in() !== false) {
	Template::redirect('dashboard');
}

		$this->form_validation->set_rules(
			'username',
			lang('bf_username'),
			'required|trim|alpha_numeric|min_length[4]|max_length[30]|is_unique[users.username]'
		);

		$this->form_validation->set_rules(
			'password',
			lang('bf_password'),
			'required|trim|min_length[8]|max_length[120]'
		);

		$this->form_validation->set_rules(
			'pass_confirm',
			lang('bf_password_confirm'),
			'required|trim|matches[password]'
		);

		if ($this->form_validation->run()) {
			$user_data = array(
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
			);

			$user_id = $this->user_model->insert($user_data);

			if ($user_id) {
				Template::set_message(
					lang('us_registration_success'),
					'success'
				);

				Template::redirect(LOGIN_URL);
			}

			Template::set_message(
				lang('us_registration_error'),
				'error'
			);
		}

		Template::set('page_title', lang('us_register'));
		Template::render();
	}

	/**
	 * Display the list of users.
	 *
	 * @return void
	 */
	public function index()
	{
		$this->auth->restrict('Users.Users.View');

		$users = $this->user_model->find_all();

		Template::set('users', $users);
		Template::set('page_title', lang('us_users'));
		Template::render();
	}

	/**
	 * View a user's profile.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function view($id = null)
	{
		$this->auth->restrict('Users.Users.View');

		if (empty($id)) {
			Template::set_message(
				lang('us_invalid_user_id'),
				'error'
			);

			Template::redirect('users');
		}

		$user = $this->user_model->find_user_and_meta($id);

		if (empty($user)) {
			Template::set_message(
				lang('us_invalid_user_id'),
				'error'
			);

			Template::redirect('users');
		}

		Template::set('user', $user);
		Template::set('page_title', lang('us_view_user'));
		Template::render();
	}

	/**
	 * Create a new user.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->auth->restrict('Users.Users.Create');

		if ($this->input->post('save')) {
			if ($this->saveUser('insert')) {
				Template::set_message(
					lang('us_create_success'),
					'success'
				);

				Template::redirect('users');
			}

			Template::set_message(
				lang('us_create_error'),
				'error'
			);
		}

		Template::set('page_title', lang('us_create_user'));
		Template::render();
	}

	/**
	 * Edit an existing user.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function edit($id = null)
	{
		$this->auth->restrict('Users.Users.Edit');

		if (empty($id)) {
			Template::set_message(
				lang('us_invalid_user_id'),
				'error'
			);

			Template::redirect('users');
		}

		if ($this->input->post('save')) {
			if ($this->saveUser('update', $id)) {
				Template::set_message(
					lang('us_edit_success'),
					'success'
				);

				Template::redirect('users');
			}

			Template::set_message(
				lang('us_edit_error'),
				'error'
			);
		}

		$user = $this->user_model->find($id);

		if (empty($user)) {
			Template::set_message(
				lang('us_invalid_user_id'),
				'error'
			);

			Template::redirect('users');
		}

		Template::set('user', $user);
		Template::set('page_title', lang('us_edit_user'));
		Template::render();
	}

	/**
	 * Delete a user.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function delete($id = null)
	{
		$this->auth->restrict('Users.Users.Delete');

		if (empty($id)) {
			Template::set_message(
				lang('us_invalid_user_id'),
				'error'
			);

			Template::redirect('users');
		}

		if ($this->user_model->delete($id)) {
			Template::set_message(
				lang('us_delete_success'),
				'success'
			);
		} else {
			Template::set_message(
				lang('us_delete_error'),
				'error'
			);
		}

		Template::redirect('users');
	}

	/**
	 * Save a user.
	 *
	 * @param string $type
	 * @param int    $id
	 * @param array  $meta_fields
	 *
	 * @return bool
	 */
	private function saveUser($type = 'insert', $id = null, $meta_fields = null)
	{
		$this->form_validation->set_rules(
			'username',
			lang('bf_username'),
			'required|trim|alpha_numeric|min_length[4]|max_length[30]'
		);

		$this->form_validation->set_rules(
			'password',
			lang('bf_password'),
			'trim|min_length[8]|max_length[120]'
		);

		$this->form_validation->set_rules(
			'pass_confirm',
			lang('bf_password_confirm'),
			'trim|matches[password]'
		);

		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = array(
			'username' => $this->input->post('username'),
		);

		$password = $this->input->post('password');

		if (!empty($password)) {
			$data['password'] = $password;
		}

		if ($type == 'insert') {
			$user_id = $this->user_model->insert($data);

			if (!$user_id) {
				return false;
			}
		} else {
			if (!$this->user_model->update($id, $data)) {
				return false;
			}

			$user_id = $id;
		}

		if (!empty($meta_fields)) {
			$meta = array();

			foreach ($meta_fields as $field => $config) {
				$value = $this->input->post($field);

				if ($value !== null) {
					$meta[$field] = $value;
				}
			}

			if (!empty($meta)) {
				$this->user_model->save_meta($user_id, $meta);
			}
		}

		return true;
	}

	/**
	 * Set current user.
	 *
	 * @return void
	 */
	
}