<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <?php
            	$userDisplayName = !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
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
                    <p><?php echo $userDisplayName; ?><small><?php echo $current_user->email; ?></small></p>
                </li>
                <li class="user-footer">
                    <a href="<?php echo site_url('users/profile'); ?>" class="btn btn-default btn-flat">
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