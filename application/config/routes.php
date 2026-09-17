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
