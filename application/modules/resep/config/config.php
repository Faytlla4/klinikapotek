<?php defined('BASEPATH') || exit('No direct script access allowed');
// Resep dokter (dibuat saat pemeriksaan) -> ditebus di apotek (penjualan RESEP/ONLINE).
// Status jalan: DIBUAT -> DIPROSES -> SIAP -> DISERAHKAN.
$config['module_config'] = array('description' => 'Resep & Pesanan', 'name' => 'Resep & Pesanan', 'version' => '1.0.0', 'author' => 'Klinik Apotek Team', 'menu_topic' => array('pemeriksaan' => 'Resep'));
