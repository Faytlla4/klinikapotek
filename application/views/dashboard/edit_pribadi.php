<div class="row justify-content-center">
<div class="col-12 col-md-8 col-lg-6"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Data Pribadi</h3></div>
    <div class="card-body text-center border-bottom">
        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width:72px;height:72px;background:#d1fae5;color:#065f46;font-size:1.8rem;font-weight:800;"><?php echo html_escape(mb_strtoupper(mb_substr($pasien->nama, 0, 1))); ?></div>
        <h5 class="mb-0"><?php echo html_escape($pasien->nama); ?></h5>
        <p class="text-muted mb-0">No. RM: <?php echo html_escape($pasien->no_rm); ?></p>
    </div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <h6 class="text-muted mb-3"><i class="fas fa-id-card mr-1"></i>Identitas</h6>
        <div class="form-group">
            <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                <input id="nama" name="nama" class="form-control" required value="<?php echo html_escape(set_value('nama', $pasien->nama)); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="nik">NIK</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-address-card"></i></span></div>
                <input id="nik" name="nik" class="form-control" maxlength="20" value="<?php echo html_escape(set_value('nik', $pasien->nik)); ?>">
            </div>
        </div>
        <hr>
        <h6 class="text-muted mb-3"><i class="fas fa-address-book mr-1"></i>Kontak</h6>
        <div class="form-group">
            <label for="no_hp">No. HP</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                <input id="no_hp" name="no_hp" class="form-control" value="<?php echo html_escape(set_value('no_hp', $pasien->no_hp)); ?>">
            </div>
        </div>
        <div class="form-group mb-0">
            <label for="alamat">Alamat</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                <textarea id="alamat" name="alamat" class="form-control" rows="3"><?php echo html_escape(set_value('alamat', $pasien->alamat)); ?></textarea>
            </div>
        </div>
    </div><div class="card-footer">
        <button type="submit" name="save" value="1" class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
        <a href="<?php echo site_url('dashboard/pasien'); ?>" class="btn btn-default btn-block">Batal</a>
    </div><?php echo form_close(); ?>
</div></div>
</div>
