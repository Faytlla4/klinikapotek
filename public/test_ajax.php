<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/klinikapotek/public/admin/content/pasien/get_data';
$_SERVER['SCRIPT_NAME'] = '/klinikapotek/public/index.php';
$_SERVER['PHP_SELF'] = '/klinikapotek/public/index.php/admin/content/pasien/get_data';
$_POST['draw'] = 1;
$_POST['search'] = ['value' => ''];
$_POST['length'] = 10;
$_POST['start'] = 0;

session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1; // Admin role

require 'index.php';
