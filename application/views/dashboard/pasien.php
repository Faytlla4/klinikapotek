<style>
/* ponytail: dashboard pasien — tiket antrean + list riwayat */
.tiket { border: 1px solid #d1fae5; border-left: 5px solid #059669; border-radius: 10px; }
.tiket-nomor { font-size: 1.6rem; font-weight: 800; color: #059669; line-height: 1; }
.dash-list .list-group-item { border-left: none; border-right: none; }
.dash-list .list-group-item:first-child { border-top: none; }
.dash-tgl { font-size: .8rem; color: #64748b; }
</style>
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
    <div class="card-header"><h3 class="card-title">Data Pribadi</h3><div class="card-tools"><a href="<?php echo site_url('dashboard/edit_pribadi'); ?>" class="btn btn-xs btn-light" title="Edit Data"><i class="fas fa-edit"></i></a></div></div>
    <div class="card-body text-center py-2">
        <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle" style="width:52px;height:52px;background:#d1fae5;color:#065f46;font-size:1.4rem;font-weight:800;"><?php echo html_escape(mb_strtoupper(mb_substr($pasien->nama, 0, 1))); ?></div>
        <h6 class="mb-0"><?php echo html_escape($pasien->nama); ?></h6>
        <p class="text-muted mb-1"><small>No. RM: <?php echo html_escape($pasien->no_rm); ?></small></p>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item py-1"><small class="text-muted">NIK: </small><strong><?php echo html_escape($pasien->nik ?: '-'); ?></strong></li>
        <li class="list-group-item py-1"><small class="text-muted">No. HP: </small><strong><?php echo html_escape($pasien->no_hp ?: '-'); ?></strong></li>
        <li class="list-group-item py-1"><small class="text-muted">Alamat: </small><strong><?php echo html_escape($pasien->alamat ?: '-'); ?></strong></li>
    </ul>
</div></div>
<div class="col-md-8"><div class="card">
    <div class="card-header"><h3 class="card-title">Antrean Saya</h3></div>
    <div class="card-body">
        <?php if (empty($antrian)): ?><p class="text-center text-muted mb-0">Tidak ada antrean.</p>
        <?php else: foreach ($antrian as $a): ?>
        <div class="tiket p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <div class="tiket-nomor mr-3"><?php echo html_escape($a->nomor_antrian); ?></div>
                <div><strong><?php echo html_escape($a->nama_poli); ?></strong><br><small class="text-muted"><?php echo html_escape($a->nama_dokter); ?> &middot; <?php echo html_escape($a->tanggal_antrian); ?></small></div>
            </div>
            <span class="badge badge-info mt-2 mt-sm-0"><?php echo html_escape($a->status); ?></span>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div></div></div>
<div class="row"><div class="col-md-6"><div class="card">
    <div class="card-header"><h3 class="card-title">Kunjungan Saya</h3></div>
    <ul class="list-group list-group-flush dash-list">
        <?php if (empty($kunjungan)): ?><li class="list-group-item text-center text-muted">Belum ada kunjungan.</li>
        <?php else: foreach ($kunjungan as $k): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <div><strong><?php echo html_escape($k->nama_pelayanan); ?></strong><br><span class="dash-tgl"><?php echo html_escape($k->tanggal_kunjungan); ?> &middot; <?php echo html_escape($k->nama_dokter); ?></span></div>
            <span class="badge badge-info mt-1 mt-sm-0"><?php echo html_escape($k->status); ?></span>
        </li>
        <?php endforeach; endif; ?>
    </ul>
</div></div>
<div class="col-md-6"><div class="card">
    <div class="card-header"><h3 class="card-title">Riwayat Pemeriksaan</h3></div>
    <ul class="list-group list-group-flush dash-list">
        <?php if (empty($riwayat)): ?><li class="list-group-item text-center text-muted">Belum ada riwayat.</li>
        <?php else: foreach ($riwayat as $r): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <div><strong><?php echo html_escape($r->nama_dokter); ?></strong><br><span class="dash-tgl"><?php echo html_escape($r->tanggal_pemeriksaan); ?> &middot; <?php echo html_escape($r->keluhan ?: '-'); ?></span></div>
            <span class="badge badge-info mt-1 mt-sm-0"><?php echo html_escape($r->status); ?></span>
        </li>
        <?php endforeach; endif; ?>
    </ul>
</div></div></div>
<?php endif; ?>
