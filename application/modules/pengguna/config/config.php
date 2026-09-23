<?php defined('BASEPATH') || exit('No direct script access allowed');

// Pengganti halaman user Bonfire yang tidak cocok skema (id_user/nama, tanpa email).
// Tanpa menus: dibuka via Pengaturan (admin/settings/...) + menu user di header (Profil Saya).

$config['module_config'] = array(
	'description' => 'Administrasi User dan Role (pengganti Bonfire legacy)',
	'name'        => 'Pengguna',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
);
