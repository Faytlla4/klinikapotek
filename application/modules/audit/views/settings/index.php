<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Jejak Aktivitas (100 terbaru)</h3></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3">
            <label class="mr-2">Modul</label>
            <input type="text" name="modul" class="form-control form-control-sm mr-3" value="<?php echo html_escape($f_modul ?: ''); ?>" placeholder="cth: resep">
            <label class="mr-2">Aksi</label>
            <input type="text" name="aksi" class="form-control form-control-sm mr-3" value="<?php echo html_escape($f_aksi ?: ''); ?>" placeholder="cth: create">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
            <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Modul</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php if (empty($log_list)): ?><tr><td colspan="5" class="text-center">Belum ada aktivitas.</td></tr>
            <?php else: foreach ($log_list as $l): ?><tr><td><?php echo html_escape($l->waktu); ?></td><td><?php echo html_escape($l->username ?: '-'); ?></td><td><span class="badge badge-info"><?php echo html_escape($l->aksi); ?></span></td><td><?php echo html_escape($l->modul); ?></td><td><?php echo html_escape($l->keterangan ?: '-'); ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
