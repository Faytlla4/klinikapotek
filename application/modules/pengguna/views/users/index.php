<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar User</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/settings/users/create'); ?>" class="btn btn-sm btn-primary">Tambah User</a></div></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control mr-2" value="<?php echo html_escape($q); ?>" placeholder="Cari username/nama">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($user_list)): ?><tr><td colspan="5" class="text-center">Tidak ada user.</td></tr>
            <?php else: foreach ($user_list as $u): ?><tr><td><?php echo html_escape($u->username); ?></td><td><?php echo html_escape($u->nama); ?></td><td><?php echo html_escape($u->nama_role ?: '-'); ?></td><td><span class="badge <?php echo $u->status === 'AKTIF' ? 'badge-success' : 'badge-danger'; ?>"><?php echo html_escape($u->status); ?></span></td><td><a href="<?php echo site_url(SITE_AREA . '/settings/users/edit/' . $u->id_user); ?>" class="btn btn-sm btn-default">Edit</a></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
