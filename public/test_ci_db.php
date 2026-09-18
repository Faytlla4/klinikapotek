<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/klinikapotek/public/admin/content/pasien';
$_SERVER['SCRIPT_NAME'] = '/klinikapotek/public/index.php';
$_SERVER['PHP_SELF'] = '/klinikapotek/public/index.php/admin/content/pasien';
define('ENVIRONMENT', 'development');
$system_path = '../bonfire/ci3';
$application_folder = '../application';
require 'index.php';
