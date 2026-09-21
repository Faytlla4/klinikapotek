<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $resep_menunggu; ?></h3><p>Resep Menunggu</p></div><div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="small-box-footer">Resep &amp; Pesanan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $stok_menipis; ?></h3><p>Obat Stok Menipis</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="small-box-footer">Stok Obat <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $penjualan_hari_ini; ?></h3><p>Penjualan Hari Ini</p></div><div class="icon"><i class="fas fa-cash-register"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/penjualan'); ?>" class="small-box-footer">Penjualan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $pengadaan_aktif; ?></h3><p>Pengadaan Aktif</p></div><div class="icon"><i class="fas fa-truck"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="small-box-footer">Pengadaan <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Resep Menunggu Diproses</h3></div>
    <ul class="list-group list-group-flush">
        <?php if (empty($resep_list)): ?><li class="list-group-item text-center text-muted">Tidak ada resep menunggu.</li>
        <?php else: foreach ($resep_list as $r): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <div><strong><?php echo html_escape($r->nomor_resep); ?></strong><br><small class="text-muted"><?php echo html_escape($r->nama_pasien); ?> &middot; <?php echo html_escape($r->nama_dokter); ?> &middot; <?php echo html_escape($r->tanggal_resep); ?></small></div>
            <span class="badge badge-info mt-1 mt-sm-0"><?php echo html_escape($r->status); ?></span>
        </li>
        <?php endforeach; endif; ?>
    </ul>
</div></div></div>
