<div class="row"><div class="col-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-database mr-2"></i>Backup Database</h3></div>
    <div class="card-body">
        <div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i>Backup dibuat dengan <strong>pg_dump</strong> dan dikemas <strong>ZIP</strong> berisi folder + <code>database.sql</code>. Semua file tersimpan di server sampai dihapus manual.</div>
        <?php echo form_open(SITE_AREA . '/settings/backup/buat'); ?>
            <button type="submit" class="btn btn-success mb-3"><i class="fas fa-download mr-1"></i>Buat Backup Sekarang</button>
        <?php echo form_close(); ?>
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-2">
            <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="dari" class="form-control" style="max-width:160px;" title="Dari tanggal" value="<?php echo html_escape($f_dari); ?>" onchange="this.form.submit()"></div>
            <span class="mr-2 mb-1 text-muted">s/d</span>
            <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="sampai" class="form-control" style="max-width:160px;" title="Sampai tanggal" value="<?php echo html_escape($f_sampai); ?>" onchange="this.form.submit()"></div>
            <?php if ($f_dari !== '' || $f_sampai !== ''): ?><a href="<?php echo site_url($this->uri->uri_string()); ?>" class="btn btn-sm btn-default mb-1" title="Reset filter">&times;</a><?php endif; ?>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Nama File</th><th>Ukuran</th><th>Tanggal</th><th style="width:130px;">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($backup_list)): ?><tr><td colspan="4" class="text-center text-muted">Belum ada backup.</td></tr>
            <?php else: foreach ($backup_list as $b): ?><tr><td class="text-monospace"><?php echo html_escape($b['nama']); ?></td><td><?php echo $b['ukuran'] >= 1048576 ? number_format($b['ukuran'] / 1048576, 2, ',', '.') . ' MB' : number_format(max(1, round($b['ukuran'] / 1024)), 0, ',', '.') . ' KB'; ?></td><td><?php echo html_escape($b['tanggal']); ?></td><td><a href="<?php echo site_url(SITE_AREA . '/settings/backup/unduh/' . $b['nama']); ?>" class="btn btn-sm btn-secondary mr-1" title="Unduh"><i class="fas fa-download"></i></a><a href="<?php echo site_url(SITE_AREA . '/settings/backup/hapus/' . $b['nama']); ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus file <?php echo html_escape($b['nama']); ?>?')"><i class="fas fa-trash"></i></a></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
