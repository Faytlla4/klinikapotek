<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo base_url(); ?>" class="brand-link">
        <img src="<?php echo base_url('assets/images/logo.png'); ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            <?php echo html_escape($this->settings_lib->item('site.subtitle')); ?>
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
            .wrapper { overflow-x: hidden; }
            .content-wrapper { overflow-x: hidden; }
        </style>
        <nav class="mt-2">
            <?php echo Contextslte::render_menu('text', 'normal'); ?>
        </nav>
    </div>
</aside>