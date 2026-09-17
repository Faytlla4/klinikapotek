<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['module_config'] = array(
	'description' => 'Pemeriksaan dan Rekam Medis',
	'name'        => 'Pemeriksaan',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
	'menu_topic'  => array('pemeriksaan' => 'Pemeriksaan & Rekam Medis'),
	'menus'       => array(
		'pemeriksaan' => 'pemeriksaan/_menu_pemeriksaan',
	),
);
