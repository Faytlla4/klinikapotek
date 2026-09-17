<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $resep_menunggu; ?></h3><p>Resep Menunggu</p></div><div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="small-box-footer">Resep &amp; Pesanan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $stok_menipis; ?></h3><p>Obat Stok Menipis</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="small-box-footer">Stok Obat <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $penjualan_hari_ini; ?></h3><p>Penjualan Hari Ini</p></div><div class="icon"><i class="fas fa-cash-register"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/penjualan'); ?>" class="small-box-footer">Penjualan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $pengadaan_aktif; ?></h3><p>Pengadaan Aktif</p></div><div class="icon"><i class="fas fa-truck"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="small-box-footer">Pengadaan <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Resep Menunggu Diproses</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>No. Resep</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($resep_list)): ?><tr><td colspan="5" class="text-center">Tidak ada resep menunggu.</td></tr>
        <?php else: foreach ($resep_list as $r): ?><tr><td><?php echo html_escape($r->nomor_resep); ?></td><td><?php echo html_escape($r->tanggal_resep); ?></td><td><?php echo html_escape($r->nama_pasien); ?></td><td><?php echo html_escape($r->nama_dokter); ?></td><td><span class="badge badge-info"><?php echo html_escape($r->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
