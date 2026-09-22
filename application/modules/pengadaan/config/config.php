<?php defined('BASEPATH') || exit('No direct script access allowed');
// ponytail: tanpa menus.apotek -> Contextslte render link langsung
// (bukan dropdown parent dgn 1 anak).
$config['module_config'] = array('description' => 'Pengadaan Obat', 'name' => 'Pengadaan', 'version' => '1.0.0', 'author' => 'Klinik Apotek Team', 'menus' => array('master' => 'pengadaan/_menu_master'));
