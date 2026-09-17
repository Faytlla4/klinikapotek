<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Content controller
 */
class Content extends App_Controller
{
	protected $permissionCreate = 'Sk_tidak_mampu.Content.Create';
	protected $permissionDelete = 'Sk_tidak_mampu.Content.Delete';
	protected $permissionEdit = 'Sk_tidak_mampu.Content.Edit';
	protected $permissionView = 'Sk_tidak_mampu.Content.View';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permissionView);
		$this->load->model('sk_tidak_mampu/sk_tidak_mampu_model');
		$this->lang->load('sk_tidak_mampu');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('sk_tidak_mampu', 'sk_tidak_mampu.js');
	}

	/**
	 * Display a list of SK Tidak Mampu data.
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set('toolbar_title', lang('sk_tidak_mampu_manage'));
		Template::render();
	}

	/**
	 * Create a SK Tidak Mampu object.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->auth->restrict($this->permissionCreate);

		if (isset($_POST['save'])) {
			if ($insert_id = $this->save_sk_tidak_mampu()) {
				log_activity($this->auth->user_id(), lang('sk_tidak_mampu_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'sk_tidak_mampu');
				Template::set_message(lang('sk_tidak_mampu_create_success'), 'success');

				redirect(SITE_AREA . '/content/sk_tidak_mampu');
			}

			// Not validation error
			if (!empty($this->sk_tidak_mampu_model->error)) {
				Template::set_message(lang('sk_tidak_mampu_create_failure') . $this->sk_tidak_mampu_model->error, 'error');
			}
		}

		Template::set('toolbar_title', lang('sk_tidak_mampu_action_create'));
		Template::render();
	}

	/**
	 * Allows editing of SK Tidak Mampu data.
	 *
	 * @return void
	 */
	public function edit()
	{
		$id = $this->uri->segment(5);
		if (empty($id)) {
			Template::set_message(lang('sk_tidak_mampu_invalid_id'), 'error');

			redirect(SITE_AREA . '/content/sk_tidak_mampu');
		}

		if (isset($_POST['save'])) {
			$this->auth->restrict($this->permissionEdit);

			if ($this->save_sk_tidak_mampu('update', $id)) {
				log_activity($this->auth->user_id(), lang('sk_tidak_mampu_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'sk_tidak_mampu');
				Template::set_message(lang('sk_tidak_mampu_edit_success'), 'success');
				redirect(SITE_AREA . '/content/sk_tidak_mampu');
			}

			// Not validation error
			if (!empty($this->sk_tidak_mampu_model->error)) {
				Template::set_message(lang('sk_tidak_mampu_edit_failure') . $this->sk_tidak_mampu_model->error, 'error');
			}
		} elseif (isset($_POST['delete'])) {
			$this->auth->restrict($this->permissionDelete);

			if ($this->sk_tidak_mampu_model->delete($id)) {
				log_activity($this->auth->user_id(), lang('sk_tidak_mampu_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'sk_tidak_mampu');
				Template::set_message(lang('sk_tidak_mampu_delete_success'), 'success');

				redirect(SITE_AREA . '/content/sk_tidak_mampu');
			}

			Template::set_message(lang('sk_tidak_mampu_delete_failure') . $this->sk_tidak_mampu_model->error, 'error');
		}

		Template::set('sk_tidak_mampu', $this->sk_tidak_mampu_model->find($id));
		Template::set('toolbar_title', lang('sk_tidak_mampu_edit_heading'));
		Template::render();
	}

	//--------------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------------

	/**
	 * Save the data.
	 *
	 * @param string $type Either 'insert' or 'update'.
	 * @param int    $id   The ID of the record to update, ignored on inserts.
	 *
	 * @return boolean|integer An ID for successful inserts, true for successful
	 * updates, else false.
	 */
	private function save_sk_tidak_mampu($type = 'insert', $id = 0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

		// Validate the data
		$this->form_validation->set_rules($this->sk_tidak_mampu_model->get_validation_rules());
		if ($this->form_validation->run() === false) {
			return false;
		}

		// Make sure we only pass in the fields we want
		$data = $this->sk_tidak_mampu_model->prep_data($this->input->post());

		// Additional handling for default values should be added below,
		// or in the model's prep_data() method
		$data['tanggal'] = $this->input->post('tanggal') ? $this->input->post('tanggal') : '0000-00-00';

		$return = false;
		if ($type == 'insert') {
			$id = $this->sk_tidak_mampu_model->insert($data);

			if (is_numeric($id)) {
				$return = $id;
			}
		} elseif ($type == 'update') {
			$return = $this->sk_tidak_mampu_model->update($id, $data);
		}

		return $return;
	}

	public function get_data()
	{
		$return = $this->sk_tidak_mampu_model->find_all();
		echo json_encode($return);
	}

	public function user_lookup()
	{
		$return = $this->sk_tidak_mampu_model->user_lookup();
		echo json_encode($return);
	}
}