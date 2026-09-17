<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['module_config'] = array(
	'description' => 'Apotek Online Pasien',
	'name'        => 'Apotek Online',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
	'menu_topic'  => array('online' => 'APOTEK ONLINE'),
	'menus'       => array(
		'online' => 'apotekonline/_menu_online',
		'apotek' => 'apotekonline/_menu_apotek',
	),
);
