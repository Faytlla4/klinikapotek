<?php defined('BASEPATH') || exit('No direct script access allowed');
// Backup hanya untuk admin sistem; file ZIP tersimpan di application/archives (diabaikan git).

$config['module_config'] = array(
	'description' => 'Backup database PostgreSQL via pg_dump',
	'name'        => 'Backup Database',
	'version'     => '1.0.0',
	'author'      => 'Klinik Apotek Team',
);
