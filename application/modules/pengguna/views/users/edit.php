<?php if (validation_errors()): ?><div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo validation_errors(); ?></div><?php endif; ?>
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Form Edit User: <?php echo html_escape($pengguna->username); ?></h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="row">
            <div class="col-md-6"><div class="form-group"><label for="nama">Nama <span class="text-danger">*</span></label><input id="nama" name="nama" class="form-control" required value="<?php echo set_value('nama', $pengguna->nama ?? ''); ?>"><?php echo form_error('nama'); ?></div></div>
            <div class="col-md-6"><div class="form-group"><label for="password">Password Baru</label><input id="password" type="password" name="password" class="form-control"><small class="text-muted">Kosongkan bila tidak diganti (minimal 6 karakter).</small></div></div>
        </div>
        <div class="row">
            <div class="col-md-6"><div class="form-group"><label for="id_role">Role <span class="text-danger">*</span></label><select id="id_role" name="id_role" class="form-control select2" required><?php foreach ($role_list as $r): ?><option value="<?php echo $r->id_role; ?>"<?php echo (int) ($pengguna->id_role ?? 0) === (int) $r->id_role ? ' selected' : ''; ?>><?php echo html_escape($r->nama_role); ?></option><?php endforeach; ?></select></div></div>
            <div class="col-md-6"><div class="form-group"><label for="status">Status <span class="text-danger">*</span></label><select id="status" name="status" class="form-control select2"><option value="AKTIF"<?php echo ($pengguna->status ?? '') === 'AKTIF' ? ' selected' : ''; ?>>AKTIF</option><option value="NONAKTIF"<?php echo ($pengguna->status ?? '') === 'NONAKTIF' ? ' selected' : ''; ?>>NONAKTIF</option></select></div></div>
        </div>
    </div><div class="card-footer"><button type="submit" name="save" value="1" class="btn btn-primary">Simpan Perubahan</button><a href="<?php echo site_url(SITE_AREA . '/settings/users'); ?>" class="btn btn-default float-right">Batal</a></div><?php echo form_close(); ?>
</div></div></div>
