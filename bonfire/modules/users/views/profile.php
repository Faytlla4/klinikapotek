<?php /* /users/views/profile.php — ponytail: field sesuai skema apotek (nama, username, password) + markup Bootstrap 4 agar nyatu dengan adminlte. */
$userNama = set_value('display_name', isset($user->nama) ? $user->nama : (isset($user->username) ? $user->username : ''));
$userName = set_value('username', isset($user->username) ? $user->username : '');
?>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?php echo lang('us_edit_profile'); ?></h3>
    </div>
    <?php echo form_open($this->uri->uri_string(), array('autocomplete' => 'off')); ?>
    <div class="card-body">
        <?php if (validation_errors()) : ?>
        <div class="alert alert-danger">
            <?php echo validation_errors(); ?>
        </div>
        <?php endif; ?>
        <div class="form-group row<?php echo form_error('display_name') ? ' has-error' : ''; ?>">
            <label class="col-sm-3 col-form-label" for="display_name">Nama</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="display_name" name="display_name" value="<?php echo html_escape($userNama); ?>" />
                <?php echo form_error('display_name', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
        <div class="form-group row<?php echo form_error('username') ? ' has-error' : ''; ?>">
            <label class="col-sm-3 col-form-label" for="username"><?php echo lang('bf_username'); ?> *</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="username" name="username" value="<?php echo html_escape($userName); ?>" />
                <?php echo form_error('username', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
        <div class="form-group row<?php echo form_error('password') ? ' has-error' : ''; ?>">
            <label class="col-sm-3 col-form-label" for="password"><?php echo lang('bf_password'); ?></label>
            <div class="col-sm-9">
                <input class="form-control" type="password" id="password" name="password" value="" placeholder="Kosongkan bila tidak ganti password" />
                <?php echo form_error('password', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
        <div class="form-group row<?php echo form_error('pass_confirm') ? ' has-error' : ''; ?>">
            <label class="col-sm-3 col-form-label" for="pass_confirm"><?php echo lang('bf_password_confirm'); ?></label>
            <div class="col-sm-9">
                <input class="form-control" type="password" id="pass_confirm" name="pass_confirm" value="" />
                <?php echo form_error('pass_confirm', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('bf_action_save'); ?>" />
    </div>
    <?php echo form_close(); ?>
</div>
