<?php defined('BASEPATH') || exit('No direct script access allowed');

// Referensi yang dipakai seluruh web: pelayanan, poli, spesialis, ruangan, dokter, obat.
// Ubah di sini = berubah di dropdown pendaftaran, resep, penjualan, dan pengadaan.

$config['module_config'] = array(
	'description' => 'Modul Master Data Klinik & Apotek',
	'name'        => 'Master Data',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
	'menus'       => array(
		'master' => 'master/master_sub_menu'
	)
);
