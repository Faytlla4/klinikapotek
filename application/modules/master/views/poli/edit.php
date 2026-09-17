<?php if (validation_errors()): ?>
<div class='alert alert-danger alert-dismissible'>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Edit Poli</h3>
            </div>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="card-body">
                <div class="form-group <?php echo form_error('nama_poli') ? 'has-error' : ''; ?>">
                    <label for="nama_poli">Nama Poli <span class="text-danger">*</span></label>
                    <input id="nama_poli" type="text" class="form-control" name="nama_poli" required value="<?php echo set_value('nama_poli', $poli->nama_poli ?? ''); ?>" />
                    <?php echo form_error('nama_poli'); ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control select2">
                        <option value="AKTIF" <?php echo ($poli->status ?? '') === 'AKTIF' ? 'selected' : ''; ?>>AKTIF</option>
                        <option value="NONAKTIF" <?php echo ($poli->status ?? '') === 'NONAKTIF' ? 'selected' : ''; ?>>NONAKTIF</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?php echo site_url(SITE_AREA . '/master/poli'); ?>" class="btn btn-default float-right">Batal</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
