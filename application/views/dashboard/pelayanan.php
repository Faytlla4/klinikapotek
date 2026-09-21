<style>
.tiket { border: 1px solid #d1fae5; border-left: 5px solid #059669; border-radius: 10px; }
.tiket-nomor { font-size: 1.6rem; font-weight: 800; color: #059669; line-height: 1; }
</style>
<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $pasien_total; ?></h3><p>Pasien Aktif</p></div><div class="icon"><i class="fas fa-users"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/pasien'); ?>" class="small-box-footer">Pendaftaran <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $kunjungan_hari_ini; ?></h3><p>Kunjungan Hari Ini</p></div><div class="icon"><i class="fas fa-clipboard-list"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/kunjungan'); ?>" class="small-box-footer">Kunjungan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $antrian_menunggu; ?> / <?php echo (int) $antrian_total; ?></h3><p>Menunggu / Antrian Hari Ini</p></div><div class="icon"><i class="fas fa-list-ol"></i></div><a href="<?php echo site_url(SITE_AREA . '/pelayanan/antrian'); ?>" class="small-box-footer">Antrian <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $tagihan_belum; ?></h3><p>Tagihan Belum Lunas</p></div><div class="icon"><i class="fas fa-file-invoice-dollar"></i></div><a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan'); ?>" class="small-box-footer">Tagihan <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrian Hari Ini</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/pelayanan/kunjungan/create'); ?>" class="btn btn-sm btn-primary">Tambah Kunjungan</a></div></div>
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
