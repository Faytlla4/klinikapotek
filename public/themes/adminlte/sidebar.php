<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo site_url(SITE_AREA); ?>" class="brand-link">
        <img src="<?php echo base_url('assets/images/logo_apotek.png'); ?>" class="brand-image img-circle elevation-3" alt="Logo Apotek">
        <span class="brand-text font-weight-light">
            <?php echo html_escape($this->settings_lib->item('site.subtitle') ?: 'Klinik & Apotek'); ?>
        </span>
    </a>

    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo base_url('themes/admin/images/user.png'); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php
                    $user = $this->auth->user();
                    echo html_escape(!empty($user->display_name) ? $user->display_name : $user->username);
                ?></a>
            </div>
        </div>

        <style>
            /* Only force text visible when sidebar is expanded (NOT collapsed) */
            body.sidebar-mini:not(.sidebar-collapse) .nav-sidebar .nav-link p { display: inline-block; visibility: visible; width: auto; }
            body.sidebar-mini:not(.sidebar-collapse) .nav-sidebar > .nav-item > .nav-link > span { display: inline-block; }
            body.sidebar-mini:not(.sidebar-collapse) .brand-link .brand-text { display: inline-block; visibility: visible; }
            /* ponytail: bar putih + teks hijau; logo banner lebar di-crop jadi icon via object-fit */
            .main-sidebar .brand-link { background: #fff !important; border-bottom: 1px solid rgba(0,0,0,.08) !important; }
            .main-sidebar .brand-link .brand-text { color: #065F46 !important; font-weight: 700 !important; }
            .brand-link .brand-image { object-fit: cover; width: 33px; height: 33px; background: #fff; }
            .wrapper { overflow-x: hidden; }
            .content-wrapper { overflow-x: hidden; }
        </style>
        <nav class="mt-2">
            <?php echo Contextslte::render_menu('text', 'normal'); ?>
        </nav>
    </div>
</aside>