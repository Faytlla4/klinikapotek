<div class="row"><div class="col-md-8"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Edit Satuan Obat</h3></div>
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
    <div class="card-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Nama Satuan <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="nama_satuan" value="<?php echo set_value('nama_satuan', isset($satuan) ? $satuan->nama_satuan : ''); ?>" required>
                <?php echo form_error('nama_satuan'); ?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Status</label>
            <div class="col-sm-9">
                <select name="status" class="form-control">
                    <option value="AKTIF" <?php echo set_select('status', 'AKTIF', isset($satuan) && $satuan->status == 'AKTIF'); ?>>AKTIF</option>
                    <option value="NONAKTIF" <?php echo set_select('status', 'NONAKTIF', isset($satuan) && $satuan->status == 'NONAKTIF'); ?>>NONAKTIF</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" name="save" class="btn btn-primary">Simpan</button>
        <a href="<?php echo site_url(SITE_AREA . '/master/satuan'); ?>" class="btn btn-default">Batal</a>
    </div>
    <?php echo form_close(); ?>
</div></div></div>
