<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Entry point context Master; menu detail tetap memakai view template existing. */
class Master extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict('kelola_master_data');
	}

	public function index()
	{
		redirect(SITE_AREA . '/master/pelayanan');
	}
}
