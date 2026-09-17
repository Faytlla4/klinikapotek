<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Riwayat Penjualan (50 terbaru)</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) . '/create'); ?>" class="btn btn-sm btn-primary">Jual Obat</a></div></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>Tanggal</th><th>Jenis</th><th>Pasien</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($jual_list)): ?><tr><td colspan="6" class="text-center">Belum ada penjualan.</td></tr>
        <?php else: foreach ($jual_list as $j): ?><tr><td><?php echo html_escape($j->nomor_penjualan); ?></td><td><?php echo html_escape($j->tanggal_penjualan); ?></td><td><span class="badge badge-info"><?php echo html_escape($j->jenis_penjualan); ?></span></td><td><?php echo html_escape($j->nama_pasien ?: '-'); ?></td><td>Rp <?php echo number_format((float) $j->total, 0, ',', '.'); ?></td><td><?php echo html_escape($j->status); ?></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
