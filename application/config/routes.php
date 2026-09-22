<?php
defined('BASEPATH') || exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;

// Authentication
Route::any(LOGIN_URL, 'users/login', array('as' => 'login'));
Route::any(REGISTER_URL, 'users/register', array('as' => 'register'));
Route::block('users/login');
Route::block('users/register');

Route::any('logout', 'users/logout');
Route::any('dokter-bertugas', 'dokter_bertugas/index');
Route::any('dokter-bertugas/ganti', 'dokter_bertugas/ganti');
Route::any('forgot_password', 'users/forgot_password');
Route::any('reset_password/(:any)/(:any)', 'users/reset_password/$1/$2');

// Activation
Route::any('activate', 'users/activate');
Route::any('activate/(:any)', 'users/activate/$1');
Route::any('resend_activation', 'users/resend_activation');

// Contexts
Route::prefix(SITE_AREA, function(){
    Route::context('content', array('home' => SITE_AREA .'/content/index'));
    Route::context('master', array('home' => SITE_AREA .'/master/index'));
    Route::context('reports', array('home' => SITE_AREA .'/reports/index'));
    Route::context('developer');
    Route::context('settings');
});

// Master data application module. The AdminLTE context uses "master" as a
// context name, while these controllers live in application/modules/master.
$route['admin/master/pelayanan'] = 'master/pelayanan/index';
$route['admin/master/pelayanan/(:any)'] = 'master/pelayanan/$1';
$route['admin/master/pelayanan/(:any)/(:any)'] = 'master/pelayanan/$1/$2';
$route['admin/master/dokter'] = 'master/dokter/index';
$route['admin/master/dokter/(:any)'] = 'master/dokter/$1';
$route['admin/master/dokter/(:any)/(:any)'] = 'master/dokter/$1/$2';
$route['admin/master/spesialis'] = 'master/spesialis/index';
$route['admin/master/spesialis/(:any)'] = 'master/spesialis/$1';
$route['admin/master/spesialis/(:any)/(:any)'] = 'master/spesialis/$1/$2';
$route['admin/master/poli'] = 'master/poli/index';
$route['admin/master/poli/(:any)'] = 'master/poli/$1';
$route['admin/master/poli/(:any)/(:any)'] = 'master/poli/$1/$2';
$route['admin/master/ruangan'] = 'master/ruangan/index';
$route['admin/master/ruangan/(:any)'] = 'master/ruangan/$1';
$route['admin/master/ruangan/(:any)/(:any)'] = 'master/ruangan/$1/$2';
$route['admin/master/obat'] = 'master/obat/index';
$route['admin/master/obat/(:any)'] = 'master/obat/$1';
$route['admin/master/obat/(:any)/(:any)'] = 'master/obat/$1/$2';

// PELAYANAN: Pendaftaran (pasien), Kunjungan, Antrian.
$route['admin/pelayanan/pasien'] = 'pasien/pelayanan/index';
$route['admin/pelayanan/pasien/(:any)'] = 'pasien/pelayanan/$1';
$route['admin/pelayanan/pasien/(:any)/(:any)'] = 'pasien/pelayanan/$1/$2';
$route['admin/pelayanan/kunjungan'] = 'kunjungan/pelayanan/index';
$route['admin/pelayanan/kunjungan/(:any)'] = 'kunjungan/pelayanan/$1';
$route['admin/pelayanan/kunjungan/(:any)/(:any)'] = 'kunjungan/pelayanan/$1/$2';
$route['admin/pelayanan/antrian'] = 'antrian/pelayanan/index';
$route['admin/pelayanan/antrian/(:any)'] = 'antrian/pelayanan/$1';
$route['admin/pelayanan/antrian/(:any)/(:any)'] = 'antrian/pelayanan/$1/$2';

// PEMERIKSAAN & REKAM MEDIS: Antrian Dokter, Pemeriksaan, Resep (dokter).
$route['admin/pemeriksaan/antrian'] = 'antrian/pemeriksaan/index';
$route['admin/pemeriksaan/antrian/(:any)'] = 'antrian/pemeriksaan/$1';
$route['admin/pemeriksaan/antrian/(:any)/(:any)'] = 'antrian/pemeriksaan/$1/$2';
$route['admin/pemeriksaan/pemeriksaan'] = 'pemeriksaan/pemeriksaan/index';
// API pemeriksaan harus berada sebelum wildcard agar tidak masuk ke
// controller Pemeriksaan biasa.
$route['admin/pemeriksaan/pemeriksaan/api/buka'] = 'pemeriksaan/api/buka';
$route['admin/pemeriksaan/pemeriksaan/api/rekam_medis/(:num)'] = 'pemeriksaan/api/rekam_medis/$1';
$route['admin/pemeriksaan/pemeriksaan/api/diagnosis'] = 'pemeriksaan/api/diagnosis';
$route['admin/pemeriksaan/pemeriksaan/api/tindakan'] = 'pemeriksaan/api/tindakan';
$route['admin/pemeriksaan/pemeriksaan/api/selesai/(:num)'] = 'pemeriksaan/api/selesai/$1';
$route['admin/pemeriksaan/pemeriksaan/(:any)'] = 'pemeriksaan/pemeriksaan/$1';
$route['admin/pemeriksaan/pemeriksaan/(:any)/(:any)'] = 'pemeriksaan/pemeriksaan/$1/$2';
$route['admin/pemeriksaan/resep'] = 'resep/pemeriksaan/index';
$route['admin/pemeriksaan/resep/(:any)'] = 'resep/pemeriksaan/$1';
$route['admin/pemeriksaan/resep/(:any)/(:any)'] = 'resep/pemeriksaan/$1/$2';

// APOTEK: Resep & Pesanan, Penjualan, Stok (+mutasi), Pengadaan (+supplier).
$route['admin/apotek/resep'] = 'resep/apotek/index';
$route['admin/apotek/resep/(:any)'] = 'resep/apotek/$1';
$route['admin/apotek/resep/(:any)/(:any)'] = 'resep/apotek/$1/$2';
$route['admin/apotek/penjualan'] = 'penjualan/apotek/index';
$route['admin/apotek/penjualan/(:any)'] = 'penjualan/apotek/$1';
$route['admin/apotek/penjualan/(:any)/(:any)'] = 'penjualan/apotek/$1/$2';
$route['admin/apotek/stok'] = 'stok/apotek/index';
$route['admin/apotek/stok/(:any)'] = 'stok/apotek/$1';
$route['admin/apotek/stok/(:any)/(:any)'] = 'stok/apotek/$1/$2';
$route['admin/apotek/pengadaan'] = 'pengadaan/apotek/index';
$route['admin/apotek/pengadaan/(:any)'] = 'pengadaan/apotek/$1';
$route['admin/apotek/pengadaan/(:any)/(:any)'] = 'pengadaan/apotek/$1/$2';

// TRANSAKSI & PEMBAYARAN: Transaksi, Tagihan (+pembayaran), Pembayaran.
$route['admin/transaksi/transaksi'] = 'transaksi/transaksi/index';
$route['admin/transaksi/transaksi/(:any)'] = 'transaksi/transaksi/$1';
$route['admin/transaksi/transaksi/(:any)/(:any)'] = 'transaksi/transaksi/$1/$2';
$route['admin/transaksi/tagihan'] = 'tagihan/transaksi/index';
// API harus didefinisikan sebelum rute wildcard tagihan agar tidak diarahkan
// ke controller Transaksi (yang tidak memiliki method API tersebut).
$route['admin/transaksi/tagihan/api/susun/(:num)'] = 'tagihan/api/susun/$1';
$route['admin/transaksi/tagihan/api/detail/(:num)'] = 'tagihan/api/detail/$1';
$route['admin/transaksi/tagihan/api/batalkan/(:num)'] = 'tagihan/api/batalkan/$1';
$route['admin/transaksi/tagihan/api/bayar'] = 'tagihan/api/bayar';
$route['admin/transaksi/tagihan/api/bukti/(:num)'] = 'tagihan/api/bukti/$1';
$route['admin/transaksi/tagihan/(:any)'] = 'tagihan/transaksi/$1';
$route['admin/transaksi/tagihan/(:any)/(:any)'] = 'tagihan/transaksi/$1/$2';

// APOTEK ONLINE sisi APOTEKER (kelola pesanan).
$route['admin/apotek/pesanan-online'] = 'apotekonline/apotek/index';
$route['admin/apotek/pesanan-online/(:any)'] = 'apotekonline/apotek/$1';
$route['admin/apotek/pesanan-online/(:any)/(:any)'] = 'apotekonline/apotek/$1/$2';

// Retur online (apoteker memutus, pasien mengajukan dari detail pesanan).
$route['admin/apotek/retur'] = 'apotekonline/apotek/retur';
$route['admin/apotek/retur/(:num)'] = 'apotekonline/apotek/retur_detail/$1';

// APOTEK ONLINE sisi PASIEN (context online).
$route['admin/online/obat'] = 'apotekonline/online/obat';
$route['admin/online/keranjang'] = 'apotekonline/online/keranjang';
$route['admin/online/checkout'] = 'apotekonline/online/checkout';
$route['admin/online/pesanan'] = 'apotekonline/online/pesanan';
$route['admin/online/pesanan/detail/(:num)'] = 'apotekonline/online/pesanan_detail/$1';

// Alias dash /admin/apotek-online/* ke context online yang sama.
$route['admin/apotek-online/obat'] = 'apotekonline/online/obat';
$route['admin/apotek-online/keranjang'] = 'apotekonline/online/keranjang';
$route['admin/apotek-online/checkout'] = 'apotekonline/online/checkout';
$route['admin/apotek-online/pesanan'] = 'apotekonline/online/pesanan';
$route['admin/apotek-online/pesanan/detail/(:num)'] = 'apotekonline/online/pesanan_detail/$1';

// LAPORAN.
$route['admin/laporan/laporan'] = 'laporan/laporan/index';

// LAPORAN CETAK (konteks terpisah dari LAPORAN).
// Urutan penting: rute xls harus sebelum wildcard agar tak tertelan (:any = .+).
$route['admin/cetak'] = 'laporan/cetak/index';
$route['admin/cetak/xlsx/(:any)'] = 'laporan/cetak/xlsx/$1';
$route['admin/cetak/(:any)'] = 'laporan/cetak/lihat/$1';

// MASTER DATA: supplier dikelola lewat pengadaan.
$route['admin/master/pengadaan'] = 'pengadaan/master/index';
$route['admin/master/pengadaan/(:any)'] = 'pengadaan/master/$1';

// MANAJEMEN SISTEM: audit log.
$route['admin/settings/audit'] = 'audit/settings/index';
$route['admin/settings/audit/(:any)'] = 'audit/settings/$1';

// MANAJEMEN SISTEM: backup database (pg_dump).
$route['admin/settings/backup'] = 'backup/settings/index';
$route['admin/settings/backup/(:any)'] = 'backup/settings/$1';
$route['admin/settings/backup/(:any)/(:any)'] = 'backup/settings/$1/$2';

// MANAJEMEN SISTEM: user & role via modul pengguna (pengganti Bonfire
// legacy yang tak kompatibel dengan skema custom).
$route['admin/settings/users'] = 'pengguna/users/index';
$route['admin/settings/users/(:any)'] = 'pengguna/users/$1';
$route['admin/settings/users/(:any)/(:any)'] = 'pengguna/users/$1/$2';
$route['admin/settings/roles'] = 'pengguna/roles/index';
$route['admin/settings/roles/(:any)'] = 'pengguna/roles/$1';
$route['admin/settings/roles/(:any)/(:any)'] = 'pengguna/roles/$1/$2';
$route['admin/profile'] = 'pengguna/users/profile';
// ponytail: users/profile legacy (Bonfire) tak kompatibel skema custom — arahkan ke profile baru.
$route['users/profile'] = 'pengguna/users/profile';

// Alias lama admin/content/* (kompatibilitas; sidebar memakai context baru).
// Pendaftaran dan data pasien.
$route['admin/content/pasien'] = 'pasien/content/index';
$route['admin/content/pasien/(:any)'] = 'pasien/content/$1';
$route['admin/content/pasien/(:any)/(:any)'] = 'pasien/content/$1/$2';
$route['admin/content/kunjungan'] = 'kunjungan/content/index';
$route['admin/content/kunjungan/(:any)'] = 'kunjungan/content/$1';
$route['admin/content/kunjungan/(:any)/(:any)'] = 'kunjungan/content/$1/$2';

// Antrian Tahap D.
$route['admin/content/antrian'] = 'antrian/content/index';
$route['admin/content/antrian/(:any)'] = 'antrian/content/$1';
$route['admin/content/antrian/(:any)/(:any)'] = 'antrian/content/$1/$2';

// Pemeriksaan dan rekam medis Tahap E.
$route['admin/content/pemeriksaan'] = 'pemeriksaan/content/index';
$route['admin/content/pemeriksaan/(:any)'] = 'pemeriksaan/content/$1';
$route['admin/content/pemeriksaan/(:any)/(:any)'] = 'pemeriksaan/content/$1/$2';

// Resep, stok, penjualan Tahap F.
$route['admin/content/penjualan/api/retur/(:num)'] = 'penjualan/api/retur/$1';
$route['admin/content/resep'] = 'resep/content/index';
$route['admin/content/resep/(:any)'] = 'resep/content/$1';
$route['admin/content/resep/(:any)/(:any)'] = 'resep/content/$1/$2';
$route['admin/content/stok'] = 'stok/content/index';
$route['admin/content/stok/(:any)'] = 'stok/content/$1';
$route['admin/content/penjualan'] = 'penjualan/content/index';
$route['admin/content/penjualan/(:any)'] = 'penjualan/content/$1';
$route['admin/content/pengadaan'] = 'pengadaan/content/index';
$route['admin/content/pengadaan/(:any)'] = 'pengadaan/content/$1';
$route['admin/content/pengadaan/(:any)/(:any)'] = 'pengadaan/content/$1/$2';
$route['admin/content/tagihan'] = 'tagihan/content/index';
$route['admin/content/tagihan/(:any)'] = 'tagihan/content/$1';
$route['admin/content/transaksi'] = 'transaksi/content/index';
$route['admin/content/transaksi/(:any)'] = 'transaksi/content/$1';

// Laporan Tahap I.
$route['admin/content/laporan'] = 'laporan/content/index';

$route = Route::map($route);
