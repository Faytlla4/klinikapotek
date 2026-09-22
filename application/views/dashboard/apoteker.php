<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $resep_menunggu; ?></h3><p>Resep Menunggu</p></div><div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="small-box-footer">Resep &amp; Pesanan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-secondary"><div class="inner"><h3><?php echo (int) $total_obat; ?></h3><p>Total Obat Aktif</p></div><div class="icon"><i class="fas fa-pills"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="small-box-footer">Stok Obat <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $penjualan_hari_ini; ?></h3><p>Penjualan Hari Ini</p></div><div class="icon"><i class="fas fa-cash-register"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/penjualan'); ?>" class="small-box-footer">Penjualan <i class="fas fa-arrow-circle-right"></i></a></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $pengadaan_aktif; ?></h3><p>Pengadaan Aktif</p></div><div class="icon"><i class="fas fa-truck"></i></div><a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="small-box-footer">Pengadaan <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row">
    <div class="col-12"><div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Pengaturan Peringatan Kedaluwarsa</h3></div>
        <div class="card-body">
            <?php echo form_open('dashboard/apoteker', array('class' => 'form-inline')); ?>
                <label for="expiry_warning_days" class="mr-2">Tampilkan obat yang kedaluwarsa dalam</label>
                <input type="number" id="expiry_warning_days" name="expiry_warning_days" class="form-control mr-2" min="1" max="3650" required value="<?php echo (int) $expiry_warning_days; ?>">
                <span class="mr-2">hari ke depan</span>
                <button type="submit" name="simpan_peringatan_expired" value="1" class="btn btn-primary">Simpan</button>
            <?php echo form_close(); ?>
            <small class="text-muted d-block mt-2">Tanggal sebelum hari ini tetap ditampilkan sebagai sudah kedaluwarsa.</small>
            <?php if (empty($expiry_columns_available)): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    Fitur tanggal kedaluwarsa belum aktif pada database ini. Jalankan migration <code>008_expiry_penerimaan.php</code> atau script update database sebelum menerima obat.
                </div>
            <?php endif; ?>
        </div>
    </div></div>
</div>
<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $stok_menipis; ?></h3><p>Stok Menipis</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-dark"><div class="inner"><h3><?php echo (int) $stok_habis; ?></h3><p>Stok Habis</p></div><div class="icon"><i class="fas fa-ban"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?php echo (int) $expired_total; ?></h3><p>Sudah Kedaluwarsa</p></div><div class="icon"><i class="fas fa-calendar-times"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $segera_expired_total; ?></h3><p>Segera Kedaluwarsa (<?php echo (int) $expiry_warning_days; ?> Hari)</p></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div></div>
</div>
<div class="row">
    <div class="col-md-6"><div class="card">
        <div class="card-header"><h3 class="card-title">Stok Perlu Diperhatikan</h3></div>
        <div class="card-body table-responsive p-0"><table class="table table-sm mb-0"><thead><tr><th>Obat</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($stok_list)): ?><tr><td colspan="4" class="text-center text-muted">Semua stok aman.</td></tr>
        <?php else: foreach ($stok_list as $s): ?><tr><td><?php echo html_escape($s->nama_obat); ?></td><td><?php echo (int) $s->stok; ?></td><td><?php echo (int) $s->stok_minimum; ?></td><td><span class="badge badge-<?php echo $s->status_stok === 'HABIS' ? 'dark' : 'danger'; ?>"><?php echo html_escape($s->status_stok); ?></span></td></tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </div></div>
    <div class="col-md-6"><div class="card">
        <div class="card-header"><h3 class="card-title">Expired / Segera Expired</h3></div>
        <div class="card-body table-responsive p-0"><table class="table table-sm mb-0"><thead><tr><th>Obat</th><th>Batch</th><th>Expired</th><th>Sisa Hari</th><th>Status</th></tr></thead><tbody>
        <?php $expiry_list = array_merge($expired, $segera_expired); ?>
        <?php if (empty($expiry_list)): ?><tr><td colspan="5" class="text-center text-muted">Tidak ada data tanggal kedaluwarsa.</td></tr>
        <?php else: foreach ($expiry_list as $e): ?><tr><td><?php echo html_escape($e->nama_obat); ?></td><td><?php echo html_escape($e->nomor_batch ?: '-'); ?></td><td><?php echo date('d-m-Y', strtotime($e->tanggal_kadaluarsa)); ?></td><td><?php echo (int) $e->sisa_hari; ?></td><td><span class="badge badge-<?php echo $e->status_expired === 'SUDAH KEDALUWARSA' ? 'danger' : 'warning'; ?>"><?php echo html_escape($e->status_expired); ?></span></td></tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </div></div>
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
