<?php

if (validation_errors()) :
?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('sk_tidak_mampu_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php
endif;

$id = isset($sk_tidak_mampu->id) ? $sk_tidak_mampu->id : '';

?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('sk_tidak_mampu_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <div class='col-md-12'>
                    <div class='form-group<?php echo form_error('nama') ? ' error' : ''; ?>'>
                        <?php echo form_label(lang('sk_tidak_mampu_field_nama') . lang('bf_form_label_required'), 'nama', array('class' => '')); ?>
                        <input id='nama' type='text' class='form-control' required='required' name='nama' maxlength='30' value='<?php echo set_value('nama', isset($sk_tidak_mampu->nama) ? $sk_tidak_mampu->nama : ''); ?>' />
                        <span class='help-inline'><?php echo form_error('nama'); ?></span>
                    </div>
                </div>

                <div class='col-md-12'>
                    <div class='form-group<?php echo form_error('alamat') ? ' error' : ''; ?>'>
                        <?php echo form_label(lang('sk_tidak_mampu_field_alamat') . lang('bf_form_label_required'), 'alamat', array('class' => '')); ?>
                        <?php echo form_textarea(array('name' => 'alamat', 'id' => 'alamat', 'class' => 'form-control', 'rows' => '5', 'cols' => '80', 'value' => set_value('alamat', isset($sk_tidak_mampu->alamat) ? $sk_tidak_mampu->alamat : ''), 'required' => 'required')); ?>
                        <span class='help-inline'><?php echo form_error('alamat'); ?></span>
                    </div>
                </div>

                <div class='col-md-12'>
                    <?php
                    	$options = array(
                    		'Sekolah' => 'Sekolah',
                    		'Rumah Sakit' => 'Rumah Sakit',
                    	);
                    	echo form_dropdown_lte(array('name' => 'jenis_surat', 'id' => 'jenis_surat', 'class' => 'form-control select2', 'required' => 'required'), $options, set_value('jenis_surat', isset($sk_tidak_mampu->jenis_surat) ? $sk_tidak_mampu->jenis_surat : ''), lang('sk_tidak_mampu_field_jenis_surat') . lang('bf_form_label_required'));
                    ?>
                </div>

                <div class='col-md-12'>
                    <div class='form-group<?php echo form_error('no_telepon') ? ' error' : ''; ?>'>
                        <?php echo form_label(lang('sk_tidak_mampu_field_no_telepon') . lang('bf_form_label_required'), 'no_telepon', array('class' => '')); ?>
                        <input id='no_telepon' type='text' class='form-control' required='required' name='no_telepon' maxlength='30' value='<?php echo set_value('no_telepon', isset($sk_tidak_mampu->no_telepon) ? $sk_tidak_mampu->no_telepon : ''); ?>' />
                        <span class='help-inline'><?php echo form_error('no_telepon'); ?></span>
                    </div>
                </div>

                <div class='col-md-12'>
                    <div class='form-group<?php echo form_error('tanggal') ? ' error' : ''; ?>'>
                        <?php echo form_label(lang('sk_tidak_mampu_field_tanggal') . lang('bf_form_label_required'), 'tanggal', array('class' => '')); ?>
                        <input id='tanggal' type='text' class='form-control' required='required' name='tanggal' maxlength='30' value='<?php echo set_value('tanggal', isset($sk_tidak_mampu->tanggal) ? $sk_tidak_mampu->tanggal : ''); ?>' />
                        <span class='help-inline'><?php echo form_error('tanggal'); ?></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type='submit' name='save' class='btn btn-primary' value="<?php echo lang('sk_tidak_mampu_action_edit'); ?>" />
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/content/sk_tidak_mampu', lang('sk_tidak_mampu_cancel'), 'class="btn btn-warning"'); ?>
                
                <?php if ($this->auth->has_permission('SK_Tidak_Mampu.Content.Delete')) : ?>
                    <?php echo lang('bf_or'); ?>
                    <button type='submit' name='delete' formnovalidate class='btn btn-danger' id='delete-me' onclick="return confirm('<?php e(js_escape(lang('sk_tidak_mampu_delete_confirm'))); ?>');">
                        <span class='icon-trash icon-white'></span>&nbsp;<?php echo lang('sk_tidak_mampu_delete_record'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>