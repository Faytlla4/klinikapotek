<style>
/* Override background area for dashboard content */
.content-wrapper {
    background-color: #F5F7F6 !important;
}

/* Base Dashboard Styles */
.dash-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #155E57;
    padding-bottom: 24px;
}

.dash-title {
    color: #155E57;
    font-weight: 700;
    font-size: 22px;
    letter-spacing: -0.2px;
}

.dash-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
}

.dash-section-title i {
    color: #087F6C;
    margin-right: 8px;
    font-size: 16px;
}

/* Card Styling */
.dash-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E4ECEB;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    padding: 18px 20px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.dash-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}

/* Operational Summary Cards */
.dash-op-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-op-info .dash-op-value {
    font-size: 26px;
    font-weight: 700;
    color: #087F6C;
    line-height: 1.2;
}

.dash-op-info .dash-op-label {
    font-size: 13px;
    font-weight: 600;
    color: #607D8B;
    margin-top: 2px;
    margin-bottom: 0;
}

.dash-op-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #EAF5F3;
    color: #087F6C;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.dash-op-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
    padding-top: 10px;
    border-top: 1px solid #F0F4F4;
    font-size: 12px;
    font-weight: 600;
    color: #087F6C;
    text-decoration: none !important;
}

.dash-op-footer:hover {
    color: #055C4E;
}

/* Warning Cards Styling - Soft Muted Accents */
.dash-warn-card {
    border-top: 3px solid transparent;
}

.warn-stok-menipis {
    border-top-color: #A58B4F;
}

.warn-stok-menipis .dash-warn-icon {
    background: #F8F5EE;
    color: #A58B4F;
}

.warn-stok-menipis .dash-warn-value {
    color: #A58B4F;
}

.warn-stok-habis {
    border-top-color: #5F7773;
}

.warn-stok-habis .dash-warn-icon {
    background: #F0F4F3;
    color: #5F7773;
}

.warn-stok-habis .dash-warn-value {
    color: #5F7773;
}

.warn-segera-exp {
    border-top-color: #9A946A;
}

.warn-segera-exp .dash-warn-icon {
    background: #F6F5EE;
    color: #9A946A;
}

.warn-segera-exp .dash-warn-value {
    color: #9A946A;
}

.warn-sudah-exp {
    border-top-color: #A87575;
}

.warn-sudah-exp .dash-warn-icon {
    background: #F9F2F2;
    color: #A87575;
}

.warn-sudah-exp .dash-warn-value {
    color: #A87575;
}

.dash-warn-value {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.2;
}

.dash-warn-label {
    font-size: 13px;
    font-weight: 600;
    color: #607D8B;
    margin-top: 2px;
    margin-bottom: 0;
}

.dash-warn-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

/* Modern Card Box for Tables */
.dash-table-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E4ECEB;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}

.dash-table-header {
    padding: 16px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid #E4ECEB;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-table-title {
    font-size: 16px;
    font-weight: 700;
    color: #155E57;
    margin: 0;
    display: flex;
    align-items: center;
}

.dash-table-title i {
    color: #087F6C;
    margin-right: 10px;
}

.dash-btn-link {
    color: #087F6C;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    padding: 6px 12px;
    border-radius: 6px;
    background: #EAF5F3;
    transition: background 0.15s ease, color 0.15s ease;
}

.dash-btn-link:hover {
    background: #D7EDE9;
    color: #055C4E;
}

/* Table Styling */
.dash-table {
    margin-bottom: 0;
    width: 100%;
}

.dash-table th {
    background-color: #F5F7F6;
    color: #607D8B;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-top: none;
    border-bottom: 1px solid #E4ECEB;
    padding: 12px 20px;
}

.dash-table td {
    padding: 14px 20px;
    vertical-align: middle;
    border-top: 1px solid #F0F4F4;
    color: #2D3748;
    font-size: 13.5px;
}

.dash-table tbody tr:hover {
    background-color: #FAFBFB;
}

/* Badges */
.badge-soft-primary {
    background: #EAF5F3;
    color: #087F6C;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.badge-soft-warning {
    background: #F9F6ED;
    color: #A58B4F;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.badge-soft-danger {
    background: #FDF2F2;
    color: #A87575;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.badge-soft-dark {
    background: #F0F4F3;
    color: #5F7773;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
}

/* Empty State Styling */
.dash-empty-state {
    padding: 32px 20px;
    text-align: center;
}

.dash-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #EAF5F3;
    color: #087F6C;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}

.dash-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 4px;
}

.dash-empty-sub {
    font-size: 13px;
    color: #607D8B;
    margin-bottom: 0;
}
</style>

<div class="dash-container">
    <!-- JUDUL DASHBOARD -->
    <div class="mb-4">
        <h3 class="dash-title">Dashboard Apoteker</h3>
    </div>

    <!-- 1. RINGKASAN OPERASIONAL -->
    <div class="mb-4">
        <div class="dash-section-title">
            <i class="fas fa-chart-line"></i> Ringkasan Operasional
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $resep_menunggu; ?></div>
                            <div class="dash-op-label">Resep Menunggu</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="dash-op-footer">
                        <span>Resep &amp; Pesanan</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $total_obat; ?></div>
                            <div class="dash-op-label">Total Obat Aktif</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="dash-op-footer">
                        <span>Stok Obat</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-sm-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $penjualan_hari_ini; ?></div>
                            <div class="dash-op-label">Penjualan Hari Ini</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/penjualan'); ?>" class="dash-op-footer">
                        <span>Penjualan</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $pengadaan_aktif; ?></div>
                            <div class="dash-op-label">Pengadaan Aktif</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="dash-op-footer">
                        <span>Pengadaan</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. PERINGATAN -->
    <div class="mb-4">
        <div class="dash-section-title">
            <i class="fas fa-exclamation-circle" style="color: #A58B4F;"></i> Peringatan
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card dash-warn-card warn-stok-menipis">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-warn-value"><?php echo (int) $stok_menipis; ?></div>
                            <div class="dash-warn-label">Stok Menipis</div>
                        </div>
                        <div class="dash-warn-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card dash-warn-card warn-stok-habis">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-warn-value"><?php echo (int) $stok_habis; ?></div>
                            <div class="dash-warn-label">Stok Habis</div>
                        </div>
                        <div class="dash-warn-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-sm-0">
                <div class="dash-card dash-warn-card warn-segera-exp">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-warn-value"><?php echo (int) $segera_expired_total; ?></div>
                            <div class="dash-warn-label">Segera Kedaluwarsa (<?php echo (int) $expiry_warning_days; ?> Hari)</div>
                        </div>
                        <div class="dash-warn-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-card dash-warn-card warn-sudah-exp">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-warn-value"><?php echo (int) $expired_total; ?></div>
                            <div class="dash-warn-label">Sudah Kedaluwarsa</div>
                        </div>
                        <div class="dash-warn-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. RESEP MENUNGGU DIPROSES -->
    <div class="dash-table-card">
        <div class="dash-table-header">
            <h3 class="dash-table-title">
                <i class="fas fa-prescription-bottle-alt"></i> Resep Menunggu Diproses
            </h3>
            <a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="dash-btn-link">
                <i class="fas fa-list mr-1"></i> Kelola Semua Resep
            </a>
        </div>
        <div class="table-responsive">
            <table class="table dash-table text-nowrap">
                <thead>
                    <tr>
                        <th>No. Resep</th>
                        <th>Nama Pasien</th>
                        <th>Dokter Penulis</th>
                        <th>Tanggal Resep</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($resep_list)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="dash-empty-state">
                                    <div class="dash-empty-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="dash-empty-title">Tidak ada resep yang menunggu diproses.</div>
                                    <div class="dash-empty-sub">Semua resep telah selesai diproses.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($resep_list as $r): ?>
                        <tr>
                            <td><strong style="color: #087F6C;"><?php echo html_escape($r->nomor_resep); ?></strong></td>
                            <td><?php echo html_escape($r->nama_pasien); ?></td>
                            <td><?php echo html_escape($r->nama_dokter); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($r->tanggal_resep)); ?></td>
                            <td><span class="badge-soft-primary"><?php echo html_escape($r->status); ?></span></td>
                            <td class="text-center">
                                <a href="<?php echo site_url(SITE_AREA . '/apotek/resep/detail/' . $r->id_resep); ?>" class="btn btn-xs btn-outline-success style-btn-action" style="color: #087F6C; border-color: #087F6C;">
                                    <i class="fas fa-eye mr-1"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. STOK PERLU DIPERHATIKAN -->
    <div class="dash-table-card">
        <div class="dash-table-header">
            <h3 class="dash-table-title">
                <i class="fas fa-boxes"></i> Stok Perlu Diperhatikan
            </h3>
            <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="dash-btn-link">
                <i class="fas fa-boxes mr-1"></i> Kelola Stok Obat
            </a>
        </div>
        <div class="table-responsive">
            <table class="table dash-table text-nowrap">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Obat</th>
                        <th>Satuan</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center">Stok Minimum</th>
                        <th class="text-center">Status Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stok_list)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="dash-empty-state">
                                    <div class="dash-empty-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="dash-empty-title">Semua stok obat dalam kondisi aman.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($stok_list as $s): ?>
                        <tr>
                            <td><code><?php echo html_escape($s->kode_obat ?: '-'); ?></code></td>
                            <td><strong><?php echo html_escape($s->nama_obat); ?></strong></td>
                            <td><?php echo html_escape($s->satuan ?: 'Pcs'); ?></td>
                            <td class="text-center"><span class="badge-soft-dark"><?php echo (int) $s->stok; ?></span></td>
                            <td class="text-center"><span class="text-muted"><?php echo (int) $s->stok_minimum; ?></span></td>
                            <td class="text-center">
                                <span class="<?php echo $s->status_stok === 'HABIS' ? 'badge-soft-dark' : 'badge-soft-warning'; ?>">
                                    <?php echo html_escape($s->status_stok); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. MONITORING KEDALUWARSA OBAT -->
    <div class="dash-table-card">
        <div class="dash-table-header">
            <h3 class="dash-table-title">
                <i class="fas fa-calendar-times"></i> Monitoring Kedaluwarsa Obat
            </h3>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modalSettingsExpiry" style="border-radius: 6px; font-size: 13px;">
                <i class="fas fa-cog mr-1"></i> Pengaturan
            </button>
        </div>
        <div class="table-responsive">
            <table class="table dash-table text-nowrap">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>No. Batch</th>
                        <th>Tanggal ED</th>
                        <th class="text-center">Sisa Hari</th>
                        <th class="text-center">Stok Batch</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $expiry_list = array_merge($expired, $segera_expired); ?>
                    <?php if (empty($expiry_list)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="dash-empty-state">
                                    <div class="dash-empty-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="dash-empty-title">Tidak ada data obat kedaluwarsa atau mendekati kedaluwarsa.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($expiry_list as $e): ?>
                        <tr>
                            <td><strong><?php echo html_escape($e->nama_obat); ?></strong></td>
                            <td><code><?php echo html_escape($e->nomor_batch ?: '-'); ?></code></td>
                            <td><?php echo date('d-m-Y', strtotime($e->tanggal_kadaluarsa)); ?></td>
                            <td class="text-center">
                                <span class="<?php echo $e->status_expired === 'SUDAH KEDALUWARSA' ? 'badge-soft-danger' : 'badge-soft-warning'; ?>">
                                    <?php echo (int) $e->sisa_hari; ?> Hari
                                </span>
                            </td>
                            <td class="text-center"><span class="badge-soft-dark"><?php echo (int) $e->stok; ?></span></td>
                            <td class="text-center">
                                <?php if ($e->status_expired === 'SUDAH KEDALUWARSA'): ?>
                                    <span class="badge-soft-danger">🔴 KEDALUWARSA</span>
                                <?php else: ?>
                                    <span class="badge-soft-warning">🟡 SEGERA EXPIRED</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-success btn-tindak" style="color: #087F6C; border-color: #087F6C;"
                                        data-id="<?php echo isset($e->id_detail) ? (int)$e->id_detail : 0; ?>"
                                        data-nama="<?php echo html_escape($e->nama_obat); ?>"
                                        data-batch="<?php echo html_escape($e->nomor_batch ?: '-'); ?>"
                                        data-ed="<?php echo date('d-m-Y', strtotime($e->tanggal_kadaluarsa)); ?>"
                                        data-stok="<?php echo (int) $e->stok; ?>"
                                        data-toggle="modal" data-target="#modalTindakanExpired">
                                    <i class="fas fa-edit mr-1"></i> Tindak Lanjut
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pengaturan Peringatan Kedaluwarsa -->
<div class="modal fade" id="modalSettingsExpiry" tabindex="-1" role="dialog" aria-labelledby="modalSettingsExpiryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden;">
            <?php echo form_open('dashboard/apoteker'); ?>
                <div class="modal-header" style="background: #FAFBFB; border-bottom: 1px solid #E4ECEB;">
                    <h5 class="modal-title" id="modalSettingsExpiryLabel" style="color: #155E57; font-weight: 700; font-size: 16px;"><i class="fas fa-cog mr-1 text-secondary"></i> Pengaturan Peringatan Kedaluwarsa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="form-group">
                        <label for="expiry_warning_days" class="font-weight-normal" style="color: #155E57;">Tampilkan peringatan obat sebelum ED:</label>
                        <div class="input-group">
                            <input type="number" id="expiry_warning_days" name="expiry_warning_days" class="form-control" min="1" max="3650" required value="<?php echo (int) $expiry_warning_days; ?>" style="border-radius: 6px 0 0 6px;">
                            <div class="input-group-append">
                                <span class="input-group-text" style="background: #F5F7F6; border-radius: 0 6px 6px 0; color: #607D8B;">hari ke depan</span>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">Batas hari ini menentukan kapan obat masuk kategori "Segera Kedaluwarsa".</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #E4ECEB; background: #FAFBFB;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button type="submit" name="simpan_peringatan_expired" value="1" class="btn btn-primary" style="background: #087F6C; border-color: #087F6C; border-radius: 6px;">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal Tindakan Obat Expired -->
<div class="modal fade" id="modalTindakanExpired" tabindex="-1" role="dialog" aria-labelledby="modalTindakanExpiredLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden;">
            <?php echo form_open('dashboard/apoteker'); ?>
                <input type="hidden" name="id_detail" id="tindak_id_detail" value="">
                <div class="modal-header" style="background: #F9F6ED; border-bottom: 1px solid #E4ECEB;">
                    <h5 class="modal-title" id="modalTindakanExpiredLabel" style="color: #A58B4F; font-weight: 700; font-size: 16px;"><i class="fas fa-pills mr-1"></i> Tindakan Obat Kedaluwarsa / Segera Expired</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="callout callout-info py-2 mb-3" style="border-left-color: #087F6C; background: #EAF5F3;">
                        <strong id="tindak_nama_obat" style="color: #155E57;">-</strong><br>
                        <small class="text-muted">Batch: <span id="tindak_batch">-</span> | ED: <span id="tindak_ed">-</span> | Stok Batch: <strong id="tindak_stok_text">0</strong></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="jenis_tindakan" style="color: #155E57;">Pilih Jenis Tindakan <span class="text-danger">*</span></label>
                        <select name="jenis_tindakan" id="jenis_tindakan" class="form-control" required style="border-radius: 6px;">
                            <option value="RETUR">1. RETUR — Dikembalikan kepada supplier</option>
                            <option value="PEMUSNAHAN">2. PEMUSNAHAN — Diproses untuk dimusnahkan</option>
                            <option value="TUNDA">3. TUNDA TINDAKAN — Belum menentukan tindakan (stok tidak berkurang)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="group_jumlah_tindakan">
                        <label for="jumlah_tindakan" style="color: #155E57;">Jumlah Obat Ditindak <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tindakan" id="jumlah_tindakan" class="form-control" min="1" required value="1" style="border-radius: 6px;">
                        <small class="form-text text-muted">Bisa diisi sebagian atau seluruh dari total stok batch (misal 5 dari 20).</small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan" style="color: #155E57;">Keterangan / Alasan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan opsional..." style="border-radius: 6px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #E4ECEB; background: #FAFBFB;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button type="submit" name="proses_tindakan_expired" value="1" class="btn btn-primary" style="background: #087F6C; border-color: #087F6C; border-radius: 6px;">
                        <i class="fas fa-check mr-1"></i> Konfirmasi Tindakan
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-tindak').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var batch = $(this).data('batch');
        var ed = $(this).data('ed');
        var stok = $(this).data('stok');

        $('#tindak_id_detail').val(id);
        $('#tindak_nama_obat').text(nama);
        $('#tindak_batch').text(batch);
        $('#tindak_ed').text(ed);
        $('#tindak_stok_text').text(stok);
        $('#jumlah_tindakan').attr('max', stok).val(stok);
    });

    $('#jenis_tindakan').on('change', function() {
        if ($(this).val() === 'TUNDA') {
            $('#group_jumlah_tindakan').hide();
            $('#jumlah_tindakan').prop('required', false);
        } else {
            $('#group_jumlah_tindakan').show();
            $('#jumlah_tindakan').prop('required', true);
        }
    });
});
</script>
