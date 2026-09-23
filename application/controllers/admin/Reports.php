<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Gerbang context Reports (butuh izin Site.Reports.View).
 * Laporan asli ada di modul laporan; ini hanya pintu context-nya.
 */
class Reports extends Admin_Controller
{


	/**
	 * Controller constructor sets the Title and Permissions
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		Template::set('toolbar_title', 'Reports');

		$this->auth->restrict('Site.Reports.View');
	}//end __construct()

	//--------------------------------------------------------------------

	/**
	 * Displays the Reports context homepage
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set_view('admin/reports/index');
		Template::render();
	}//end index()

	//--------------------------------------------------------------------


}//end class
