<?php if (empty($pasien)): ?>
<div class="row"><div class="col-12"><div class="alert alert-warning">Akun Anda belum terhubung ke data pasien. Hubungi petugas pelayanan.</div></div></div>
<?php else: ?>
<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>Apotek</h3><p>Belanja Obat</p></div><div class="icon"><i class="fas fa-pills"></i></div><a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>" class="small-box-footer">Buka <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $keranjang_jml; ?></h3><p>Keranjang</p></div><div class="icon"><i class="fas fa-shopping-cart"></i></div><a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $pesanan_aktif; ?></h3><p>Pesanan Aktif</p></div><div class="icon"><i class="fas fa-box-open"></i></div><a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="small-box-footer">Riwayat <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3 style="font-size:1.1rem;"><?php echo $pesanan_terakhir ? html_escape($pesanan_terakhir->nomor_pesanan) : '-'; ?></h3><p>Pesanan Terakhir<?php echo $pesanan_terakhir ? ' (' . html_escape($pesanan_terakhir->status) . ')' : ''; ?></p></div><div class="icon"><i class="fas fa-receipt"></i></div><a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-md-4"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Data Pribadi</h3></div>
    <div class="card-body"><dl class="row mb-0">
        <dt class="col-sm-4">No. RM</dt><dd class="col-sm-8"><?php echo html_escape($pasien->no_rm); ?></dd>
        <dt class="col-sm-4">Nama</dt><dd class="col-sm-8"><?php echo html_escape($pasien->nama); ?></dd>
        <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo html_escape($pasien->nik ?: '-'); ?></dd>
        <dt class="col-sm-4">No. HP</dt><dd class="col-sm-8"><?php echo html_escape($pasien->no_hp ?: '-'); ?></dd>
    </dl></div>
</div></div>
<div class="col-md-8"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrean Saya</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
        <thead><tr><th>Nomor</th><th>Tanggal</th><th>Poli</th><th>Dokter</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($antrian)): ?><tr><td colspan="5" class="text-center">Tidak ada antrean.</td></tr>
        <?php else: foreach ($antrian as $a): ?><tr><td><?php echo html_escape($a->nomor_antrian); ?></td><td><?php echo html_escape($a->tanggal_antrian); ?></td><td><?php echo html_escape($a->nama_poli); ?></td><td><?php echo html_escape($a->nama_dokter); ?></td><td><span class="badge badge-info"><?php echo html_escape($a->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
<div class="row"><div class="col-md-6"><div class="card">
    <div class="card-header"><h3 class="card-title">Kunjungan Saya</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
        <thead><tr><th>Tanggal</th><th>Pelayanan</th><th>Dokter</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($kunjungan)): ?><tr><td colspan="4" class="text-center">Belum ada kunjungan.</td></tr>
        <?php else: foreach ($kunjungan as $k): ?><tr><td><?php echo html_escape($k->tanggal_kunjungan); ?></td><td><?php echo html_escape($k->nama_pelayanan); ?></td><td><?php echo html_escape($k->nama_dokter); ?></td><td><span class="badge badge-info"><?php echo html_escape($k->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div>
<div class="col-md-6"><div class="card">
    <div class="card-header"><h3 class="card-title">Riwayat Pemeriksaan</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
        <thead><tr><th>Tanggal</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($riwayat)): ?><tr><td colspan="4" class="text-center">Belum ada riwayat.</td></tr>
        <?php else: foreach ($riwayat as $r): ?><tr><td><?php echo html_escape($r->tanggal_pemeriksaan); ?></td><td><?php echo html_escape($r->nama_dokter); ?></td><td><?php echo html_escape($r->keluhan ?: '-'); ?></td><td><span class="badge badge-info"><?php echo html_escape($r->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
<?php endif; ?>
