<div class="row">
<div class="col-md-4"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Tambah Supplier</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group"><label for="kode_supplier">Kode</label><input id="kode_supplier" name="kode_supplier" class="form-control" readonly value="<?php echo html_escape($kode_supplier_baru); ?>"></div>
        <div class="form-group"><label for="nama_supplier">Nama Supplier <span class="text-danger">*</span></label><input id="nama_supplier" name="nama_supplier" class="form-control" required value="<?php echo set_value('nama_supplier'); ?>"></div>
        <div class="form-group"><label for="alamat">Alamat</label><textarea id="alamat" name="alamat" class="form-control" rows="2"><?php echo set_value('alamat'); ?></textarea></div>
        <div class="form-group"><label for="no_hp">No. HP</label><input id="no_hp" name="no_hp" class="form-control" value="<?php echo set_value('no_hp'); ?>"></div>
    </div><div class="card-footer"><button type="submit" name="save" value="1" class="btn btn-primary">Simpan</button></div><?php echo form_close(); ?>
</div></div>
<div class="col-md-8"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Supplier</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Kode</th><th>Nama</th><th>Alamat</th><th>No. HP</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($supplier_list)): ?><tr><td colspan="5" class="text-center">Belum ada supplier.</td></tr>
        <?php else: foreach ($supplier_list as $s): ?><tr><td><?php echo html_escape($s->kode_supplier); ?></td><td><?php echo html_escape($s->nama_supplier); ?></td><td><?php echo html_escape($s->alamat ?: '-'); ?></td><td><?php echo html_escape($s->no_hp ?: '-'); ?></td><td><span class="badge badge-info"><?php echo html_escape($s->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div>
</div>
