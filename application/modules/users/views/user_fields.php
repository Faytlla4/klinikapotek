<?php /* Custom user_fields.php — matches apotek schema: id_user, username, password, nama, status */

$currentMethod = $this->router->method;

$errorClass   = empty($errorClass) ? ' error' : $errorClass;
$controlClass = empty($controlClass) ? 'span4' : $controlClass;
$registerClass = $currentMethod == 'register' ? ' required' : '';

?>
<div class="control-group<?php echo form_error('nama') ? $errorClass : ''; ?>">
    <label class="control-label required" for="nama">Nama Lengkap</label>
    <div class="controls">
        <input class="<?php echo $controlClass; ?>" type="text" id="nama" name="nama" value="<?php echo set_value('nama', isset($user) ? $user->nama : ''); ?>" />
        <span class="help-inline"><?php echo form_error('nama'); ?></span>
    </div>
</div>
<?php if (settings_item('auth.login_type') !== 'email' || settings_item('auth.use_usernames')) : ?>
<div class="control-group<?php echo form_error('username') ? $errorClass : ''; ?>">
    <label class="control-label required" for="username"><?php echo lang('bf_username'); ?></label>
    <div class="controls">
        <input class="<?php echo $controlClass; ?>" type="text" id="username" name="username" value="<?php echo set_value('username', isset($user) ? $user->username : ''); ?>" />
        <span class="help-inline"><?php echo form_error('username'); ?></span>
    </div>
</div>
<?php endif; ?>
<div class="control-group<?php echo form_error('password') ? $errorClass : ''; ?>">
    <label class="control-label<?php echo $registerClass; ?>" for="password"><?php echo lang('bf_password'); ?></label>
    <div class="controls">
        <input class="<?php echo $controlClass; ?>" type="password" id="password" name="password" value="" />
        <span class="help-inline"><?php echo form_error('password'); ?></span>
        <p class="help-block"><?php echo isset($password_hints) ? $password_hints : ''; ?></p>
    </div>
</div>
<div class="control-group<?php echo form_error('pass_confirm') ? $errorClass : ''; ?>">
    <label class="control-label<?php echo $registerClass; ?>" for="pass_confirm"><?php echo lang('bf_password_confirm'); ?></label>
    <div class="controls">
        <input class="<?php echo $controlClass; ?>" type="password" id="pass_confirm" name="pass_confirm" value="" />
        <span class="help-inline"><?php echo form_error('pass_confirm'); ?></span>
    </div>
</div>
