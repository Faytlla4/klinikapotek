<!-- 1. RINGKASAN OPERASIONAL -->
<div class="mb-4">
    <h5 class="mb-3 text-secondary font-weight-bold"><i class="fas fa-chart-line mr-2"></i>Ringkasan Operasional</h5>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo (int) $resep_menunggu; ?></h3>
                    <p>Resep Menunggu</p>
                </div>
                <div class="icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="small-box-footer">Resep &amp; Pesanan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><?php echo (int) $total_obat; ?></h3>
                    <p>Total Obat Aktif</p>
                </div>
                <div class="icon"><i class="fas fa-pills"></i></div>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="small-box-footer">Stok Obat <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo (int) $penjualan_hari_ini; ?></h3>
                    <p>Penjualan Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-cash-register"></i></div>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/penjualan'); ?>" class="small-box-footer">Penjualan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?php echo (int) $pengadaan_aktif; ?></h3>
                    <p>Pengadaan Aktif</p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
                <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="small-box-footer">Pengadaan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- 2. PERINGATAN -->
<div class="mb-4">
    <h5 class="mb-3 text-danger font-weight-bold"><i class="fas fa-exclamation-circle mr-2"></i>Peringatan</h5>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo (int) $stok_menipis; ?></h3>
                    <p>Stok Menipis</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3><?php echo (int) $stok_habis; ?></h3>
                    <p>Stok Habis</p>
                </div>
                <div class="icon"><i class="fas fa-ban"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo (int) $expired_total; ?></h3>
                    <p>Sudah Kedaluwarsa</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-times"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo (int) $segera_expired_total; ?></h3>
                    <p>Segera Kedaluwarsa (<?php echo (int) $expiry_warning_days; ?> Hari)</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- 3. RESEP MENUNGGU DIPROSES -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-prescription-bottle-alt mr-2 text-info"></i>Resep Menunggu Diproses
                </h3>
                <div class="card-tools">
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/resep'); ?>" class="btn btn-tool text-primary">
                        <i class="fas fa-list mr-1"></i> Kelola Semua Resep
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap">
                    <thead class="thead-light">
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
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                    Tidak ada resep yang menunggu diproses.
                                </td>
                            </tr>
                        <?php else: foreach ($resep_list as $r): ?>
                            <tr>
                                <td><strong class="text-primary"><?php echo html_escape($r->nomor_resep); ?></strong></td>
                                <td><?php echo html_escape($r->nama_pasien); ?></td>
                                <td><?php echo html_escape($r->nama_dokter); ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($r->tanggal_resep)); ?></td>
                                <td><span class="badge badge-info"><?php echo html_escape($r->status); ?></span></td>
                                <td class="text-center">
                                    <a href="<?php echo site_url(SITE_AREA . '/apotek/resep/detail/' . $r->id_resep); ?>" class="btn btn-xs btn-primary">
                                        <i class="fas fa-eye mr-1"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 4. STOK PERLU DIPERHATIKAN -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-warning">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-boxes mr-2 text-warning"></i>Stok Perlu Diperhatikan
                </h3>
                <div class="card-tools">
                    <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="btn btn-tool text-primary">
                        <i class="fas fa-boxes mr-1"></i> Kelola Stok Obat
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap">
                    <thead class="thead-light">
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
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-shield-alt text-success fa-2x mb-2 d-block"></i>
                                    Semua stok obat dalam kondisi aman.
                                </td>
                            </tr>
                        <?php else: foreach ($stok_list as $s): ?>
                            <tr>
                                <td><code><?php echo html_escape($s->kode_obat ?: '-'); ?></code></td>
                                <td><strong><?php echo html_escape($s->nama_obat); ?></strong></td>
                                <td><?php echo html_escape($s->satuan ?: 'Pcs'); ?></td>
                                <td class="text-center"><span class="badge badge-secondary" style="font-size: 13px;"><?php echo (int) $s->stok; ?></span></td>
                                <td class="text-center"><span class="text-muted"><?php echo (int) $s->stok_minimum; ?></span></td>
                                <td class="text-center">
                                    <span class="badge badge-<?php echo $s->status_stok === 'HABIS' ? 'dark' : 'danger'; ?>">
                                        <?php echo html_escape($s->status_stok); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 5. MONITORING KEDALUWARSA OBAT -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-calendar-times mr-2 text-danger"></i>Monitoring Kedaluwarsa Obat
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modalSettingsExpiry">
                        <i class="fas fa-cog mr-1"></i> Pengaturan
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap">
                    <thead class="thead-light">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-check text-success fa-2x mb-2 d-block"></i>
                                    Tidak ada data obat kedaluwarsa atau mendekati kedaluwarsa.
                                </td>
                            </tr>
                        <?php else: foreach ($expiry_list as $e): ?>
                            <tr>
                                <td><strong><?php echo html_escape($e->nama_obat); ?></strong></td>
                                <td><code><?php echo html_escape($e->nomor_batch ?: '-'); ?></code></td>
                                <td><?php echo date('d-m-Y', strtotime($e->tanggal_kadaluarsa)); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?php echo $e->status_expired === 'SUDAH KEDALUWARSA' ? 'danger' : 'warning'; ?>">
                                        <?php echo (int) $e->sisa_hari; ?> Hari
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge badge-secondary" style="font-size: 13px;"><?php echo (int) $e->stok; ?></span></td>
                                <td class="text-center">
                                    <?php if ($e->status_expired === 'SUDAH KEDALUWARSA'): ?>
                                        <span class="badge badge-danger">🔴 KEDALUWARSA</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">🟡 SEGERA EXPIRED</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-outline-primary btn-tindak"
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
</div>

<!-- Modal Pengaturan Peringatan Kedaluwarsa -->
<div class="modal fade" id="modalSettingsExpiry" tabindex="-1" role="dialog" aria-labelledby="modalSettingsExpiryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?php echo form_open('dashboard/apoteker'); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSettingsExpiryLabel"><i class="fas fa-cog mr-1 text-secondary"></i> Pengaturan Peringatan Kedaluwarsa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="expiry_warning_days" class="font-weight-normal">Tampilkan peringatan obat sebelum ED:</label>
                        <div class="input-group">
                            <input type="number" id="expiry_warning_days" name="expiry_warning_days" class="form-control" min="1" max="3650" required value="<?php echo (int) $expiry_warning_days; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">hari ke depan</span>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">Batas hari ini menentukan kapan obat masuk kategori "Segera Kedaluwarsa".</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_peringatan_expired" value="1" class="btn btn-primary">
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
        <div class="modal-content">
            <?php echo form_open('dashboard/apoteker'); ?>
                <input type="hidden" name="id_detail" id="tindak_id_detail" value="">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalTindakanExpiredLabel"><i class="fas fa-pills mr-1"></i> Tindakan Obat Kedaluwarsa / Segera Expired</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="callout callout-info py-2 mb-3">
                        <strong id="tindak_nama_obat">-</strong><br>
                        <small class="text-muted">Batch: <span id="tindak_batch">-</span> | ED: <span id="tindak_ed">-</span> | Stok Batch: <strong id="tindak_stok_text">0</strong></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="jenis_tindakan">Pilih Jenis Tindakan <span class="text-danger">*</span></label>
                        <select name="jenis_tindakan" id="jenis_tindakan" class="form-control" required>
                            <option value="RETUR">1. RETUR — Dikembalikan kepada supplier</option>
                            <option value="PEMUSNAHAN">2. PEMUSNAHAN — Diproses untuk dimusnahkan</option>
                            <option value="TUNDA">3. TUNDA TINDAKAN — Belum menentukan tindakan (stok tidak berkurang)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="group_jumlah_tindakan">
                        <label for="jumlah_tindakan">Jumlah Obat Ditindak <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tindakan" id="jumlah_tindakan" class="form-control" min="1" required value="1">
                        <small class="form-text text-muted">Bisa diisi sebagian atau seluruh dari total stok batch (misal 5 dari 20).</small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan / Alasan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" name="proses_tindakan_expired" value="1" class="btn btn-primary">
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
