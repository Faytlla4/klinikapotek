<?php if (validation_errors()): ?>
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
                        <input id='tanggal' type='text' class='form-control' required='required' name='tanggal' maxlength='30' value='<?php echo set_value('tanggal', isset($sk_tidak_mampu->tanggal) ? $sk_tidak_mampu->tanggal : ''); ?>' data-dd-large-default="true" />
                        <span class='help-inline'><?php echo form_error('tanggal'); ?></span>
                    </div>
                </div>

                <div class='col-md-12'>
                    <div class='form-group<?php echo form_error('user') ? ' error' : ''; ?>'>
                        <?php echo form_label(lang('sk_tidak_mampu_field_nama') . lang('bf_form_label_required'), 'user_text', array('class' => '')); ?>
                        <input id='user_text' type='text' class='form-control lookup_modal' required='required' name='user_text' maxlength='30' data-target="user" value='<?php echo set_value('user_text', isset($sk_tidak_mampu->user_text) ? $sk_tidak_mampu->user_text : ''); ?>' />
                        <input id='user' type='hidden' required='required' name='user' maxlength='30' value='<?php echo set_value('user', isset($sk_tidak_mampu->user) ? $sk_tidak_mampu->user : ''); ?>' />
                        <span class='help-inline'><?php echo form_error('user'); ?></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-info">Sign in</button>
                <button type="submit" class="btn btn-default float-right">Cancel</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="user_lookup_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Default Modal</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="user_lookup_table" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th><?php echo lang('sk_tidak_mampu_field_nama'); ?></th>
                            <th><?php echo lang('sk_tidak_mampu_field_alamat'); ?></th>
                            <th><?php echo lang('sk_tidak_mampu_field_jenis_surat'); ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save_lookup" data-target="user">Save changes</button>
            </div>
        </div>
    </div>
</div>