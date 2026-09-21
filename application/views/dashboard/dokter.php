<style>
.tiket { border: 1px solid #d1fae5; border-left: 5px solid #059669; border-radius: 10px; }
.tiket-nomor { font-size: 1.6rem; font-weight: 800; color: #059669; line-height: 1; }
</style>
<div class="alert alert-info">Selamat datang, <strong><?php echo html_escape($dokter->nama_dokter); ?></strong>.<a class="btn btn-sm btn-outline-primary float-right" href="<?php echo site_url('dokter-bertugas/ganti'); ?>">Ganti Dokter</a></div>
<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $antrian_total; ?></h3><p>Antrian Hari Ini</p></div><div class="icon"><i class="fas fa-list-ol"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="small-box-footer">Antrian Dokter <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $antrian_menunggu; ?></h3><p>Pasien Menunggu</p></div><div class="icon"><i class="fas fa-user-clock"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="small-box-footer">Lihat Antrian <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $periksa_selesai; ?> / <?php echo (int) $periksa_hari_ini; ?></h3><p>Pemeriksaan Selesai / Hari Ini</p></div><div class="icon"><i class="fas fa-stethoscope"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan'); ?>" class="small-box-footer">Pemeriksaan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?php echo (int) $resep_hari_ini; ?></h3><p>Resep Dibuat Hari Ini</p></div><div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div><a href="<?php echo site_url(SITE_AREA . '/content/resep'); ?>" class="small-box-footer">Resep <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrian Hari Ini<?php echo ! empty($dokter) ? ' — ' . html_escape($dokter->nama_dokter) : ''; ?></h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan/create'); ?>" class="btn btn-sm btn-primary">Tambah Pemeriksaan</a></div></div>
    <div class="card-body">
        <?php if (empty($antrian)): ?><p class="text-center text-muted mb-0">Belum ada antrian hari ini.</p>
        <?php else: foreach ($antrian as $a): ?>
        <div class="tiket p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <div class="tiket-nomor mr-3"><?php echo html_escape($a->nomor_antrian); ?></div>
                <div><strong><?php echo html_escape($a->nama_pasien); ?></strong><br><small class="text-muted"><?php echo html_escape($a->no_rm); ?> &middot; <?php echo html_escape($a->nama_poli); ?></small></div>
            </div>
            <span class="badge badge-info mt-2 mt-sm-0"><?php echo html_escape($a->status); ?></span>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div></div></div>
