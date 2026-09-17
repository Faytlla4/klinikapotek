<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bonfire
 *
 * An open source project to allow developers to jumpstart their development of
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
 * DT_Model extends BF_Model for backwards compatibility, and to provide a
 * placeholder class that your project can customize/extend as needed.
 *
 * @package    Bonfire\Core\DT_Model
 * @author     Lonnie Ezell
 * @link       http://cibonfire.com/docs/developer/bonfire_models
 */
class DT_Model extends BF_Model
{

	/**
	 * DT_Model's constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function find_all()
	{
		$request = $this->input->post();
		$output['draw'] = (int) $request['draw'];

		if (!empty($request['search']['value']) && strlen($request['search']['value']) > 0) {
			parent::where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}

		$output['recordsTotal'] = parent::count_all();

		if (!empty($request['search']['value']) && strlen($request['search']['value']) > 0) {
			parent::where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}

		if (empty($request['sort'])) {
			parent::order_by($this->key, 'desc');
		} else {
			parent::order_by($request['sort']);
		}

		parent::limit($request['length'], $request['start']);
		$data = parent::find_all();
		$output['data'] = $data;
		$output['recordsFiltered'] = $data ? count($data) : 0;

		return $output;
	}
}
/* End of file ./application/core/DT_Model.php */