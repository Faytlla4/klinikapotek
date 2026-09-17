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
                <h3 class="card-title">Form Edit Pelayanan</h3>
            </div>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="card-body">
                <div class="row">
                <div class="col-md-6">
                <div class="form-group <?php echo form_error('nama_pelayanan') ? 'has-error' : ''; ?>">
                    <label for="nama_pelayanan">Nama Pelayanan <span class="text-danger">*</span></label>
                    <input id="nama_pelayanan" type="text" class="form-control" name="nama_pelayanan" required value="<?php echo set_value('nama_pelayanan', $pelayanan->nama_pelayanan ?? ''); ?>" />
                    <?php echo form_error('nama_pelayanan'); ?>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                    <label for="jenis_pelayanan">Jenis Pelayanan</label>
                    <select id="jenis_pelayanan" name="jenis_pelayanan" class="form-control select2">
                        <?php
                        	$opts = ['Rawat Jalan', 'Rawat Inap', 'Tindakan Medis', 'Laboratorium'];
                        	$cur = set_value('jenis_pelayanan', $pelayanan->jenis_pelayanan ?? '');
                        	foreach ($opts as $opt) {
                        		$sel = ($opt === $cur) ? 'selected' : '';
                        		echo "<option value='{$opt}' {$sel}>{$opt}</option>";
                        	}
                        ?>
                    </select>
                </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-6">
                <div class="form-group <?php echo form_error('tarif') ? 'has-error' : ''; ?>">
                    <label for="tarif">Tarif (Rp) <span class="text-danger">*</span></label>
                    <input id="tarif" type="number" step="1000" min="0" class="form-control" name="tarif" required value="<?php echo set_value('tarif', (int)($pelayanan->tarif ?? 0)); ?>" />
                    <?php echo form_error('tarif'); ?>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control select2">
                        <option value="AKTIF" <?php echo ($pelayanan->status ?? '') === 'AKTIF' ? 'selected' : ''; ?>>AKTIF</option>
                        <option value="NONAKTIF" <?php echo ($pelayanan->status ?? '') === 'NONAKTIF' ? 'selected' : ''; ?>>NONAKTIF</option>
                    </select>
                </div>
                </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?php echo site_url(SITE_AREA . '/master/pelayanan'); ?>" class="btn btn-default float-right">Batal</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
