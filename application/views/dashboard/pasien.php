<?php // Dashboard pasien — sistem class dash-* sama dengan dashboard apoteker/dokter/pelayanan. ?>
<style>
.content-wrapper { background-color: #F5F7F6 !important; }
.dash-container { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color: #155E57; padding-bottom: 24px; }
.dash-title { color: #155E57; font-weight: 700; font-size: 22px; letter-spacing: -0.2px; }
.dash-section-title { font-size: 15px; font-weight: 700; color: #155E57; margin-bottom: 14px; display: flex; align-items: center; }
.dash-section-title i { color: #087F6C; margin-right: 8px; font-size: 16px; }
.dash-card { background: #FFFFFF; border-radius: 12px; border: 1px solid #E4ECEB; box-shadow: 0 2px 8px rgba(0,0,0,0.03); padding: 18px 20px; height: 100%; }
.dash-op-card { display: flex; align-items: center; justify-content: space-between; }
.dash-op-value { font-size: 26px; font-weight: 700; color: #087F6C; line-height: 1.2; }
.dash-op-label { font-size: 13px; font-weight: 600; color: #607D8B; margin-top: 2px; margin-bottom: 0; }
.dash-op-icon { width: 48px; height: 48px; border-radius: 10px; background: #EAF5F3; color: #087F6C; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.dash-op-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 14px; padding-top: 10px; border-top: 1px solid #F0F4F4; font-size: 12px; font-weight: 600; color: #087F6C; text-decoration: none !important; }
.dash-op-sub { margin-top: 14px; padding-top: 10px; border-top: 1px solid #F0F4F4; font-size: 12px; color: #607D8B; }
.dash-tiket-no { font-size: 30px; font-weight: 700; color: #087F6C; line-height: 1.1; }
.dash-table-card { background: #FFFFFF; border-radius: 12px; border: 1px solid #E4ECEB; box-shadow: 0 2px 8px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px; }
.dash-table-header { padding: 16px 20px; background: #FFFFFF; border-bottom: 1px solid #E4ECEB; display: flex; align-items: center; justify-content: space-between; }
.dash-table-title { font-size: 16px; font-weight: 700; color: #155E57; margin: 0; display: flex; align-items: center; }
.dash-table-title i { color: #087F6C; margin-right: 10px; }
.dash-table { margin-bottom: 0; width: 100%; }
.dash-table th { background-color: #F5F7F6; color: #607D8B; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #E4ECEB; padding: 12px 20px; }
.dash-table td { padding: 14px 20px; vertical-align: middle; border-top: 1px solid #F0F4F4; color: #2D3748; font-size: 13.5px; }
.dash-table tbody tr:hover { background-color: #FAFBFB; }
.badge-soft-primary { background: #EAF5F3; color: #087F6C; font-weight: 600; padding: 4px 8px; border-radius: 6px; font-size: 12px; }
.dash-empty-state { padding: 32px 20px; text-align: center; }
.dash-empty-icon { width: 48px; height: 48px; border-radius: 50%; background: #EAF5F3; color: #087F6C; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
.dash-empty-title { font-size: 14px; font-weight: 700; color: #155E57; margin-bottom: 4px; }
.dash-empty-sub { font-size: 13px; color: #607D8B; margin-bottom: 0; }
</style>
<?php if (empty($pasien)): ?>
<div class="dash-container"><div class="dash-empty-state"><div class="dash-empty-icon"><i class="fas fa-user"></i></div><div class="dash-empty-title">Akun belum terhubung ke data pasien.</div><div class="dash-empty-sub">Hubungi petugas pelayanan.</div></div></div>
<?php else: ?>
<?php
$jml_antrean = is_array($antrian) ? count($antrian) : 0;
$jml_kunjungan = is_array($kunjungan) ? count($kunjungan) : 0;
$tiket = ($jml_antrean > 0) ? $antrian[0] : null;
?>
<div class="dash-container">

    <div class="mb-4">
        <div class="dash-section-title"><i class="fas fa-chart-line"></i> Ringkasan</div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0"><div class="dash-card"><div class="dash-op-card">
                <div><div class="dash-op-value"><?php echo $jml_antrean; ?></div><div class="dash-op-label">Antrean Aktif</div></div>
                <div class="dash-op-icon"><i class="fas fa-ticket-alt"></i></div>
            </div><div class="dash-op-sub"><?php echo $tiket ? html_escape($tiket->nama_poli) : 'Tidak ada antrean'; ?></div></div></div>
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0"><div class="dash-card"><div class="dash-op-card">
                <div><div class="dash-op-value"><?php echo (int) $pesanan_aktif; ?></div><div class="dash-op-label">Pesanan Aktif</div></div>
                <div class="dash-op-icon"><i class="fas fa-box-open"></i></div>
            </div><a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="dash-op-footer"><span>Lihat pesanan</span><i class="fas fa-arrow-right"></i></a></div></div>
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-sm-0"><div class="dash-card"><div class="dash-op-card">
                <div><div class="dash-op-value"><?php echo (int) $keranjang_jml; ?></div><div class="dash-op-label">Keranjang</div></div>
                <div class="dash-op-icon"><i class="fas fa-shopping-cart"></i></div>
            </div><a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>" class="dash-op-footer"><span>Lanjut belanja</span><i class="fas fa-arrow-right"></i></a></div></div>
            <div class="col-lg-3 col-sm-6 col-12"><div class="dash-card"><div class="dash-op-card">
                <div><div class="dash-op-value"><?php echo $jml_kunjungan; ?></div><div class="dash-op-label">Total Kunjungan</div></div>
                <div class="dash-op-icon"><i class="fas fa-calendar-check"></i></div>
            </div><div class="dash-op-sub"><?php echo html_escape($pasien->nama); ?> &middot; <?php echo html_escape($pasien->no_rm); ?></div></div></div>
        </div>
    </div>

    <div class="mb-4">
        <div class="dash-section-title"><i class="fas fa-ticket-alt"></i> Tiket Antrean Saya</div>
        <div class="row">
            <?php if (empty($antrian)): ?>
            <div class="col-12"><div class="dash-card"><div class="dash-empty-state"><div class="dash-empty-icon"><i class="fas fa-ticket-alt"></i></div><div class="dash-empty-title">Tidak ada antrean.</div><div class="dash-empty-sub">Daftar di bagian pelayanan untuk membuat antrean baru.</div></div></div></div>
            <?php else: foreach ($antrian as $a): ?>
            <div class="col-lg-4 col-sm-6 col-12 mb-3"><div class="dash-card"><div class="dash-op-card">
                <div><div class="dash-tiket-no"><?php echo html_escape($a->nomor_antrian); ?></div><div class="dash-op-label"><?php echo html_escape($a->nama_poli); ?></div></div>
                <div class="dash-op-icon"><i class="fas fa-user-md"></i></div>
            </div><div class="dash-op-sub"><?php echo html_escape($a->nama_dokter); ?> &middot; <?php echo html_escape($a->tanggal_antrian); ?> &middot; <span class="badge-soft-primary"><?php echo html_escape($a->status); ?></span></div></div></div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="dash-table-card">
        <div class="dash-table-header"><h3 class="dash-table-title"><i class="fas fa-stethoscope"></i> Riwayat Pemeriksaan</h3></div>
        <div class="table-responsive"><table class="table dash-table text-nowrap">
            <thead><tr><th>Dokter</th><th>Tanggal</th><th>Keluhan</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="4"><div class="dash-empty-state"><div class="dash-empty-icon"><i class="fas fa-check"></i></div><div class="dash-empty-title">Belum ada riwayat pemeriksaan.</div><div class="dash-empty-sub">Hasil periksa akan tampil di sini.</div></div></td></tr>
            <?php else: foreach (array_slice($riwayat, 0, 8) as $r): ?>
                <tr>
                    <td><strong style="color: #087F6C;"><?php echo html_escape($r->nama_dokter); ?></strong></td>
                    <td><?php echo html_escape($r->tanggal_pemeriksaan); ?></td>
                    <td><?php echo html_escape(mb_substr($r->keluhan ?: '-', 0, 50)); ?></td>
                    <td><span class="badge-soft-primary"><?php echo html_escape($r->status); ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>

    <div class="dash-table-card">
        <div class="dash-table-header"><h3 class="dash-table-title"><i class="fas fa-calendar-alt"></i> Kunjungan Saya</h3></div>
        <div class="table-responsive"><table class="table dash-table text-nowrap">
            <thead><tr><th>Tanggal</th><th>Pelayanan</th><th>Dokter</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($kunjungan)): ?>
                <tr><td colspan="4"><div class="dash-empty-state"><div class="dash-empty-icon"><i class="fas fa-calendar-alt"></i></div><div class="dash-empty-title">Belum ada kunjungan.</div><div class="dash-empty-sub">Kunjungan tercatat otomatis saat pendaftaran.</div></div></td></tr>
            <?php else: foreach (array_slice($kunjungan, 0, 8) as $k): ?>
                <tr>
                    <td><?php echo html_escape($k->tanggal_kunjungan); ?></td>
                    <td><strong style="color: #087F6C;"><?php echo html_escape($k->nama_pelayanan); ?></strong></td>
                    <td><?php echo html_escape($k->nama_dokter); ?></td>
                    <td><span class="badge-soft-primary"><?php echo html_escape($k->status); ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>
