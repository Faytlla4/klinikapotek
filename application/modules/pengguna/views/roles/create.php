<?php if (validation_errors()): ?><div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo validation_errors(); ?></div><?php endif; ?>
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Form Tambah Role</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group"><label for="nama_role">Nama Role <span class="text-danger">*</span></label><input id="nama_role" name="nama_role" class="form-control" required value="<?php echo set_value('nama_role'); ?>"><small class="text-muted">Huruf besar, contoh: KASIR.</small><?php echo form_error('nama_role'); ?></div>
    </div><div class="card-footer"><button type="submit" name="save" value="1" class="btn btn-primary">Simpan</button><a href="<?php echo site_url(SITE_AREA . '/settings/roles'); ?>" class="btn btn-default float-right">Batal</a></div><?php echo form_close(); ?>
</div></div></div>
