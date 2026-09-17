<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $antrian_total; ?></h3><p>Antrian Hari Ini</p></div><div class="icon"><i class="fas fa-list-ol"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="small-box-footer">Antrian Dokter <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $antrian_menunggu; ?></h3><p>Pasien Menunggu</p></div><div class="icon"><i class="fas fa-user-clock"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="small-box-footer">Lihat Antrian <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $periksa_selesai; ?> / <?php echo (int) $periksa_hari_ini; ?></h3><p>Pemeriksaan Selesai / Hari Ini</p></div><div class="icon"><i class="fas fa-stethoscope"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan'); ?>" class="small-box-footer">Pemeriksaan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?php echo (int) $resep_hari_ini; ?></h3><p>Resep Dibuat Hari Ini</p></div><div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/resep'); ?>" class="small-box-footer">Resep <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrian Hari Ini<?php echo ! empty($dokter) ? ' — ' . html_escape($dokter->nama_dokter) : ''; ?></h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan/create'); ?>" class="btn btn-sm btn-primary">Tambah Pemeriksaan</a></div></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>No. RM</th><th>Pasien</th><th>Poli</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($antrian)): ?><tr><td colspan="5" class="text-center">Belum ada antrian hari ini.</td></tr>
        <?php else: foreach ($antrian as $a): ?><tr><td><?php echo html_escape($a->nomor_antrian); ?></td><td><?php echo html_escape($a->no_rm); ?></td><td><?php echo html_escape($a->nama_pasien); ?></td><td><?php echo html_escape($a->nama_poli); ?></td><td><span class="badge badge-info"><?php echo html_escape($a->status); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
