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
 * Gerbang area admin: login dulu, lalu lempar ke context default sesuai role user.
 * (Bukan dashboard; tiap role punya dashboard sendiri di Dashboard.php.)
 */
class Home extends Admin_Controller
{


	/**
	 * Controller constructor sets the login restriction
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict();
	}//end __construct()

	//--------------------------------------------------------------------


	/**
	 * Redirects the user to the Content context
	 *
	 * @return void
	 */
    public function index()
    {
        // ponytail: skema apotek memakai roles.id_role
        $user_role = $this->db->where('id_role', $this->current_user->role_id)->get('roles')->row();
        $default_context = ($user_role !== false && isset($user_role->default_context)) ? $user_role->default_context : '';
        redirect(SITE_AREA .'/'.(isset($default_context) && !empty($default_context) ? $default_context : 'content'));
    }//end index()

	//--------------------------------------------------------------------


}//end class
