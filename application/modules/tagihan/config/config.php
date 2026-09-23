<?php defined('BASEPATH') || exit('No direct script access allowed');
// Tagihan lahir otomatis tiap ada penjualan/kunjungan; lunasnya di menu Pembayaran.
// Status: BELUM_DIBAYAR -> LUNAS (+BATAL, hanya selagi belum bayar).
$config['module_config'] = array('description' => 'Tagihan dan Pembayaran', 'name' => 'Tagihan', 'version' => '1.0.0', 'author' => 'Klinik Apotek Team', 'menus' => array('transaksi' => 'tagihan/_menu_transaksi'));
