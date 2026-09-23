<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <?php
        // ponytail: bell kadaluarsa hanya untuk pemegang kelola_stok_obat yang login; satu query berindeks per load.
        $exp_bell = array();
        if (class_exists('Auth', false) && $this->auth->is_logged_in() && $this->auth->has_permission('kelola_stok_obat')) {
            $this->load->model('stok/stok_model');
            $exp_days = (int) $this->settings_lib->item('apotek.expiry_warning_days');
            $exp_bell = $this->stok_model->peringatan_kedaluwarsa($exp_days > 0 ? $exp_days : 30);
        }
        ?>
        <?php if (! empty($exp_bell)): ?>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Obat kedaluwarsa">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger navbar-badge"><?php echo count($exp_bell); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header"><?php echo count($exp_bell); ?> batch perlu perhatian</span>
                <div class="dropdown-divider"></div>
                <?php foreach (array_slice($exp_bell, 0, 6) as $eb): ?>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="dropdown-item">
                    <i class="fas fa-pills mr-2 text-<?php echo (int) $eb->sisa_hari < 0 ? 'danger' : 'warning'; ?>"></i> <?php echo html_escape($eb->nama_obat); ?>
                    <span class="float-right text-muted text-sm"><?php echo (int) $eb->sisa_hari < 0 ? 'kedaluwarsa' : (int) $eb->sisa_hari . ' hari'; ?></span>
                </a>
                <div class="dropdown-divider"></div>
                <?php endforeach; ?>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="dropdown-item dropdown-footer">Lihat Stok</a>
            </div>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown user-menu">
            <?php
            	$userDisplayName = !empty($current_user->nama) ? $current_user->nama : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
            	$userRoleName = !empty($current_user->role_name) ? $current_user->role_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
            	// echo gravatar_link($current_user->email, 96, null, $userDisplayName);
            ?>
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="user-image img-circle elevation-2">
                <span class="d-none d-md-inline"><?php echo $userDisplayName; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header">
                    <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle elevation-2">
                    <p><?php echo $userDisplayName; ?><small><?php echo $userRoleName; ?></small></p>
                </li>
                <li class="user-footer">
                    <a href="<?php echo site_url('admin/profile'); ?>" class="btn btn-default btn-flat">
                        <small><?php echo lang('bf_user_settings'); ?></small>
                    </a>
                    <a href="<?php echo site_url('logout'); ?>" class="btn btn-default btn-flat float-right">
                        <small><?php echo lang('bf_action_logout'); ?></small>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>