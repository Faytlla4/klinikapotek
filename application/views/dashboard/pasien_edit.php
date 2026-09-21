<div class="row">
<div class="col-md-6"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Edit Data Pribadi</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group">
            <label>No. RM</label>
            <input class="form-control" readonly value="<?php echo html_escape($pasien->no_rm); ?>" style="background:#f0f0f0;">
        </div>
        <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input name="nama" class="form-control" required value="<?php echo html_escape(set_value('nama', $pasien->nama)); ?>">
        </div>
        <div class="form-group">
            <label>NIK</label>
            <input name="nik" class="form-control" maxlength="20" value="<?php echo html_escape(set_value('nik', $pasien->nik)); ?>">
        </div>
        <div class="form-group">
            <label>No. HP</label>
            <input name="no_hp" class="form-control" value="<?php echo html_escape(set_value('no_hp', $pasien->no_hp)); ?>">
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3"><?php echo html_escape(set_value('alamat', $pasien->alamat)); ?></textarea>
        </div>
    </div><div class="card-footer">
        <button type="submit" name="save" value="1" class="btn btn-primary">Simpan</button>
        <a href="<?php echo site_url('dashboard/pasien'); ?>" class="btn btn-secondary">Batal</a>
    </div><?php echo form_close(); ?>
</div></div>
</div>
