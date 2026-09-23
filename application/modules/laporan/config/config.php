<?php defined('BASEPATH') || exit('No direct script access allowed');
// Satu modul, dua context: Laporan (lihat di layar) + Cetak (print/Excel). Isi tab dari _menu_*.
$config['module_config'] = array('description' => 'Laporan Klinik', 'name' => 'Laporan', 'version' => '1.0.0', 'author' => 'Klinik Apotek Team', 'menus' => array('laporan' => 'laporan/_menu_laporan', 'cetak' => 'laporan/_menu_cetak'));
