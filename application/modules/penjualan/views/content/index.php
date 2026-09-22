<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Riwayat Penjualan<?php echo ($f_dari || $f_sampai) ? '' : ' (50 terbaru)'; ?></h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) . '/create'); ?>" class="btn btn-sm btn-primary">Jual Obat</a></div></div>
    <div class="card-body">
    <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-2">
        <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="dari" class="form-control" style="max-width:160px;" title="Dari tanggal" value="<?php echo html_escape($f_dari); ?>" onchange="this.form.submit()"></div>
        <span class="mr-2 mb-1 text-muted">s/d</span>
        <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="sampai" class="form-control" style="max-width:160px;" title="Sampai tanggal" value="<?php echo html_escape($f_sampai); ?>" onchange="this.form.submit()"></div>
        <?php if ($f_dari || $f_sampai): ?><a href="<?php echo site_url($this->uri->uri_string()); ?>" class="btn btn-sm btn-default mb-1" title="Reset filter">&times;</a><?php endif; ?>
    </form>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>Tanggal</th><th>Jenis</th><th>Pasien</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (empty($jual_list)): ?><tr><td colspan="7" class="text-center">Belum ada penjualan.</td></tr>
        <?php else: foreach ($jual_list as $j): ?><tr><td><?php echo html_escape($j->nomor_penjualan); ?></td><td><?php echo html_escape($j->tanggal_penjualan); ?></td><td><span class="badge badge-info"><?php echo html_escape($j->jenis_penjualan); ?></span></td><td><?php echo html_escape($j->nama_pasien ?: '-'); ?></td><td>Rp <?php echo number_format((float) $j->total, 0, ',', '.'); ?></td><td><?php echo html_escape($j->status); ?></td><td><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/penjualan/detail/' . (int) $j->id_penjualan); ?>" class="btn btn-sm btn-primary" title="Lihat Struk"><i class="fas fa-eye"></i></a></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
