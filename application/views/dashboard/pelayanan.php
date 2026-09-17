<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $pasien_total; ?></h3><p>Pasien Aktif</p></div><div class="icon"><i class="fas fa-users"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/pasien'); ?>" class="small-box-footer">Pendaftaran <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $kunjungan_hari_ini; ?></h3><p>Kunjungan Hari Ini</p></div><div class="icon"><i class="fas fa-clipboard-list"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/kunjungan'); ?>" class="small-box-footer">Kunjungan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $antrian_menunggu; ?> / <?php echo (int) $antrian_total; ?></h3><p>Menunggu / Antrian Hari Ini</p></div><div class="icon"><i class="fas fa-list-ol"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/antrian'); ?>" class="small-box-footer">Antrian <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $tagihan_belum; ?></h3><p>Tagihan Belum Lunas</p></div><div class="icon"><i class="fas fa-file-invoice-dollar"></i></div><a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan'); ?>" class="small-box-footer">Tagihan <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrian Hari Ini</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/pelayanan/kunjungan/create'); ?>" class="btn btn-sm btn-primary">Tambah Kunjungan</a></div></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>No. RM</th><th>Pasien</th><th>Poli</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($antrian)): ?><tr><td colspan="5" class="text-center">Belum ada antrian hari ini.</td></tr>
        <?php else: foreach ($antrian as $a): ?><tr><td><?php echo html_escape($a->nomor_antrian); ?></td><td><?php echo html_escape($a->no_rm); ?></td><td><?php echo html_escape($a->nama_pasien); ?></td><td><?php echo html_escape($a->nama_poli); ?></td><td><span class="badge badge-info"><?php echo html_escape($a->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
