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
                <h3 class="card-title">Form Tambah Ruangan</h3>
            </div>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="card-body">
                <div class="form-group <?php echo form_error('nama_ruangan') ? 'has-error' : ''; ?>">
                    <label for="nama_ruangan">Nama Ruangan <span class="text-danger">*</span></label>
                    <input id="nama_ruangan" type="text" class="form-control" name="nama_ruangan" required value="<?php echo set_value('nama_ruangan'); ?>" />
                    <?php echo form_error('nama_ruangan'); ?>
                </div>

                <div class="form-group">
                    <label for="id_poli">Poli Terkait</label>
                    <select id="id_poli" name="id_poli" class="form-control select2">
                        <option value="">-- Pilih Poli (Opsional) --</option>
                        <?php foreach ($poli_list as $p): ?>
                            <option value="<?php echo $p->id_poli; ?>" <?php echo set_select('id_poli', $p->id_poli); ?>>
                                <?php echo html_escape($p->nama_poli); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                <a href="<?php echo site_url(SITE_AREA . '/master/ruangan'); ?>" class="btn btn-default float-right">Batal</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
