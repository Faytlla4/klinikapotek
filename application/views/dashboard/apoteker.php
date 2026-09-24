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
    <div class="col-md-6"><div class="card card-warning card-outline">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-exclamation-triangle mr-1"></i> Monitoring Expired Date (ED)</h3></div>
        <div class="card-body table-responsive p-0"><table class="table table-sm table-hover mb-0"><thead><tr><th>Obat</th><th>Batch</th><th>ED</th><th>Stok</th><th>Status</th><th class="text-center">Aksi</th></tr></thead><tbody>
        <?php $expiry_list = array_merge($expired, $segera_expired); ?>
        <?php if (empty($expiry_list)): ?><tr><td colspan="6" class="text-center text-muted">Tidak ada data tanggal kedaluwarsa.</td></tr>
        <?php else: foreach ($expiry_list as $e): ?>
        <tr>
            <td><strong><?php echo html_escape($e->nama_obat); ?></strong></td>
            <td><code><?php echo html_escape($e->nomor_batch ?: '-'); ?></code></td>
            <td><?php echo date('d-m-Y', strtotime($e->tanggal_kadaluarsa)); ?></td>
            <td><span class="badge badge-secondary"><?php echo (int) $e->stok; ?></span></td>
            <td>
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

<!-- Modal Tindakan Obat Expired -->
<div class="modal fade" id="modalTindakanExpired" tabindex="-1" role="dialog" aria-labelledby="modalTindakanExpiredLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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
