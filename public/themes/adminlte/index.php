<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        	$title_text = isset($toolbar_title) ? "{$toolbar_title} : " : '';
        	if (isset($this->settings_lib)) {
        		$title_text .= $this->settings_lib->item('site.title');
        	} else {
        		$title_text .= 'Bonfire';
        	}
        	echo $title_text;
        ?>
    </title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">

    <?php
    	Assets::add_css([
    		'plugins/fontawesome-free/css/all.min.css',
    		'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
    		'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
    		'plugins/datatables-select/css/select.bootstrap4.min.css',
    		'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    		'plugins/select2/css/select2.min.css',
    		'plugins/sweetalert2/sweetalert2.min.css',
    		'css/adminlte.min.css',
    	]);
    	echo Assets::css();
    ?>

    <style>
        /* ponytail: kill ALL transitions and animations in admin */
        *, *::before, *::after {
            transition: none !important;
            animation: none !important;
        }
        .preloader { display: none !important; }

        /* === Pharmacy Emerald Theme === */
        :root {
            --primary: #059669;
            --primary-light: #34d399;
            --bs-primary: #059669;
            --bs-primary-rgb: 5, 150, 105;
        }

        /* Sidebar - subtle emerald gradient */
        .main-sidebar.sidebar-dark-primary,
        .main-sidebar.sidebar-dark-primary .sidebar {
            background: linear-gradient(180deg, #065F46 0%, #047857 40%, #059669 100%) !important;
        }
        .brand-link {
            background: #065F46 !important;
            border-bottom: 1px solid rgba(255,255,255,.06) !important;
        }
        .brand-link .brand-text { color: #fff !important; }

        /* Nav links - background sendiri */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link {
            color: rgba(255,255,255,.85) !important;
            background-color: rgba(255,255,255,.08);
            margin: 2px 8px;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255,255,255,.18) !important;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            color: #fff !important;
            background-color: rgba(255,255,255,.25) !important;
            font-weight: 600;
        }
        .sidebar-dark-primary .nav-treeview {
            background-color: transparent;
        }
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link {
            margin: 1px 8px 1px 20px;
            padding: 8px 12px;
            border-radius: 5px;
            color: rgba(255,255,255,.75) !important;
            background-color: rgba(255,255,255,.06);
        }
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link:hover {
            background-color: rgba(255,255,255,.15) !important;
            color: #fff !important;
        }
        .sidebar-dark-primary .nav-link .nav-icon {
            color: rgba(255,255,255,.7);
        }
        .sidebar-dark-primary .nav-link.active .nav-icon,
        .sidebar-dark-primary .nav-link:hover .nav-icon {
            color: #fff;
        }
        /* Allow long text to wrap */
        .nav-sidebar .nav-link,
        .nav-sidebar .nav-link p {
            white-space: normal !important;
        }

        /* User panel */
        .sidebar-dark-primary .user-panel {
            border-bottom: 1px solid rgba(255,255,255,.06);
            margin-bottom: 0;
            padding-bottom: 12px;
        }
        .sidebar-dark-primary .user-panel a { color: rgba(255,255,255,.9) !important; }

        /* Sidebar collapsed hover */
        body.sidebar-mini.sidebar-collapse .main-sidebar {
            background: linear-gradient(180deg, #065F46, #059669) !important;
        }
        .sidebar-mini.sidebar-collapse .main-sidebar:hover,
        .sidebar-mini.sidebar-collapse .main-sidebar.sidebar-focused {
            background: linear-gradient(180deg, #065F46 0%, #047857 40%, #059669 100%) !important;
        }

        /* Expanded state text */
        body.sidebar-mini:not(.sidebar-collapse) .sidebar .nav-sidebar > .nav-item > .nav-link {
            color: rgba(255,255,255,.85) !important;
        }
        body.sidebar-mini:not(.sidebar-collapse) .sidebar .nav-sidebar > .nav-item > .nav-link:hover,
        body.sidebar-mini:not(.sidebar-collapse) .sidebar .nav-sidebar > .nav-item > .nav-link.active {
            color: #fff !important;
        }

        /* Navbar */
        .main-header.navbar {
            background: #059669 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
        }
        .main-header .nav-link,
        .main-header .navbar-nav .nav-link {
            color: #fff !important;
        }

        /* Content header */
        .content-header h1 { color: #047857; }

        /* Buttons */
        .btn-primary {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }
        .btn-primary:hover {
            background-color: #047857 !important;
            border-color: #047857 !important;
        }
        .btn-outline-primary {
            color: #059669;
            border-color: #059669;
        }
        .btn-outline-primary:hover {
            background-color: #059669;
            color: #fff;
        }

        /* Cards */
        .card-primary:not(.card-outline) > .card-header {
            background-color: #059669 !important;
            border-color: #059669 !important;
            color: #fff;
        }
        .card-primary.card-outline > .card-header {
            border-top: 3px solid #059669;
        }

        /* Tables */
        thead.table-primary th {
            background-color: #059669 !important;
            color: #fff;
            border-color: #047857;
        }

        /* Breadcrumb */
        .breadcrumb-item.active { color: #047857; }

        /* Text utilities */
        .text-primary { color: #059669 !important; }
    </style>

    <script type="text/javascript" async>
    var run_title_text = " <?=$title_text?> ";
    var run_title_speed = 300;
    var run_title_refresh = null;

    function running_title_text() {
        document.title = run_title_text;
        run_title_text = run_title_text.substring(1, run_title_text.length) + run_title_text.charAt(0);
        run_title_refresh = setTimeout("running_title_text()", run_title_speed);
    }
    running_title_text();

    var site_url = '<?=base_url()?>';
    </script>
</head>

    <body class="sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php
        	echo theme_view('header');
        	echo theme_view('sidebar');
        ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <?php if (isset($toolbar_title)): ?>
                            <h1 class="m-0"><?php echo $toolbar_title; ?></h1>
                            <?php endif;?>
                        </div>
                        <div class="col-sm-6" id="sub-menu">
                            <?php Template::block('sub_nav', '');?>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php
                    	echo Template::message();
                    	echo isset($content) ? $content : Template::content();
                    ?>
                </div>
            </section>
        </div>

        <?php echo theme_view('footer'); ?>
    </div>

    <?php
    	Assets::add_js([
    		'plugins/jquery/jquery.min.js',
    		'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
    		'plugins/moment/moment.min.js',
    		'plugins/bootstrap/js/bootstrap.bundle.min.js',
    		'plugins/datatables/jquery.dataTables.min.js',
    		'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js',
    		'plugins/datatables-select/js/dataTables.select.min.js',
    		'plugins/datatables-select/js/select.bootstrap4.min.js',
    		'plugins/select2/js/select2.full.min.js',
    		'plugins/sweetalert2/sweetalert2.min.js',
    		'plugins/datedropper-jquery.3.1.1/datedropper-jquery.js',
    		'plugins/timedropper-jquery.1.2.0/timedropper-jquery.js',
    		'plugins/lodash/lodash.min.js',
    		'js/adminlte.js',
    	], 'external', true);
    	echo Assets::js();
    ?>
    <script>
    $(document).ready(function () {
        $('body').removeClass('sidebar-collapse');
        try { localStorage.removeItem('sidebar-collapse'); } catch (e) {}
    });
    </script>
</body>

</html>