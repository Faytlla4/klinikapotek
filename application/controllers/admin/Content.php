<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

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
 * Gerbang context Content (butuh izin Site.Content.View).
 * Modul-modul menumpang pola ini: class Content + child per context (Pelayanan/Apotek/dll).
 */
class Content extends App_Controller
{

	/**
	 * Controller constructor sets the Title and Permissions
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		Template::set('toolbar_title', 'Content');

		$this->auth->restrict('Site.Content.View');
	} //end __construct()

	//--------------------------------------------------------------------

	/**
	 * Displays the initial page of the Content context
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set_view('admin/content/index');
		Template::render();
	} //end index()

	//--------------------------------------------------------------------

} //end class
