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
                <h3 class="card-title">Form Tambah Spesialis</h3>
            </div>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="card-body">
                <div class="form-group <?php echo form_error('nama_spesialis') ? 'has-error' : ''; ?>">
                    <label for="nama_spesialis">Nama Spesialis <span class="text-danger">*</span></label>
                    <input id="nama_spesialis" type="text" class="form-control" name="nama_spesialis" required value="<?php echo set_value('nama_spesialis'); ?>" />
                    <?php echo form_error('nama_spesialis'); ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control select2">
                        <option value="AKTIF">AKTIF</option>
                        <option value="NONAKTIF">NONAKTIF</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <a href="<?php echo site_url(SITE_AREA . '/master/spesialis'); ?>" class="btn btn-default float-right">Batal</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
