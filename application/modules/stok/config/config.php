<?php defined('BASEPATH') || exit('No direct script access allowed');
// Stok tampil di context apotek (Stok Obat + Obat Masuk/Keluar); angka stok sendiri livedi tabel stok_obat.
$config['module_config'] = array('description' => 'Stok Obat', 'name' => 'Stok Obat', 'version' => '1.0.0', 'author' => 'Klinik Apotek Team', 'menus' => array('apotek' => 'stok/_menu_apotek'));
