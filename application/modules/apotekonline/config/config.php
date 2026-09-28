<?php defined('BASEPATH') || exit('No direct script access allowed');

// Etalase obat buat pasien (context online) + meja kerja apoteker (context apotek).
// Dua baris menus di bawah ini yang membentuk isi sidebar di kedua sisi itu.

$config['module_config'] = array(
	'description' => 'Apotek Online Pasien',
	'name'        => 'APOTEK ONLINE',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
	'menu_topic'  => array('online' => 'APOTEK ONLINE'),
	'menus'       => array(
		'online' => 'apotekonline/_menu_online',
		'apotek' => 'apotekonline/_menu_apotek',
	),
);
