<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Role</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/settings/roles/create'); ?>" class="btn btn-sm btn-primary">Tambah Role</a></div></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Role</th><th>User</th><th>Permission</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($role_list as $r): ?><tr><td><strong><?php echo html_escape($r->nama_role); ?></strong></td><td><?php echo (int) $r->jml_user; ?></td><td><?php echo (int) $r->jml_perm; ?></td><td><a href="<?php echo site_url(SITE_AREA . '/settings/roles/matrix/' . $r->id_role); ?>" class="btn btn-sm btn-info">Matriks Permission</a></td></tr><?php endforeach; ?>
        </tbody>
    </table></div>
</div></div></div>
