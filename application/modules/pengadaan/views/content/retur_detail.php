<?php // Putusan retur: item + batch/ED + alasan; Setujui = stok berkurang (FEFO/batch), Tolak butuh catatan. ?>
<div class="row">
    <div class="col-md-4">
        <div class="card <?php echo $retur->status === 'SELESAI' ? 'card-success' : ($retur->status === 'DITOLAK' ? 'card-danger' : 'card-warning'); ?>">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-undo mr-1"></i> <?php echo html_escape($retur->nomor_retur); ?></h3>
                <div class="card-tools"><span class="badge <?php echo $retur->status === 'DIAJUKAN' ? 'badge-dark' : 'badge-light'; ?>"><?php echo html_escape($retur->status); ?></span></div>
            </div>
            <div class="card-body">
                <strong>Penerimaan</strong><p class="text-muted"><?php echo html_escape($retur->nomor_penerimaan); ?> &middot; <?php echo html_escape($retur->nomor_pengadaan); ?></p><hr>
                <strong>Supplier</strong><p class="text-muted"><?php echo html_escape($retur->nama_supplier); ?></p><hr>
                <strong>Tanggal</strong><p class="text-muted"><?php echo date('d-m-Y H:i', strtotime($retur->tanggal_retur)); ?></p><hr>
                <strong>Keterangan</strong><p class="text-muted"><?php echo nl2br(html_escape($retur->keterangan ?: '-')); ?></p>
            </div>
            <div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/retur'); ?>" class="btn btn-default btn-block">Kembali</a></div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Item Diretur</h3></div>
            <div class="card-body table-responsive p-0"><table class="table table-bordered table-striped mb-0">
                <thead><tr><th>Obat</th><th class="text-center">Jumlah</th><th>Batch / ED</th><th>Alasan</th></tr></thead>
                <tbody>
                <?php foreach ($retur->items as $it): ?><tr>
                    <td><strong><?php echo html_escape($it->nama_obat); ?></strong><br><small class="text-muted"><?php echo html_escape($it->satuan); ?></small></td>
                    <td class="text-center"><strong>−<?php echo (int) $it->jumlah; ?></strong></td>
                    <td><?php echo html_escape($it->nomor_batch ?: '-'); ?><br><small class="text-muted"><?php echo $it->tanggal_kadaluarsa ? date('d-m-Y', strtotime($it->tanggal_kadaluarsa)) : ''; ?></small></td>
                    <td><?php echo html_escape($it->alasan ?: '-'); ?></td>
                </tr><?php endforeach; ?>
                </tbody>
            </table></div>
            <div class="card-footer">
                <?php echo form_open($this->uri->uri_string()); ?>
                <?php if ($retur->status === 'DIAJUKAN'): ?>
                    <div class="form-group"><label>Catatan putusan <small class="text-muted">(wajib bila menolak)</small></label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                    <button type="submit" name="aksi" value="tolak" class="btn btn-danger" onclick="return confirm('Tolak retur ini?')"><i class="fas fa-times mr-1"></i>Tolak</button>
                    <button type="submit" name="aksi" value="setujui" class="btn btn-success float-right" onclick="return confirm('Konfirmasi retur? Stok berkurang dan tidak dapat dibatalkan.')"><i class="fas fa-check mr-1"></i>Konfirmasi (Stok −)</button>
                <?php endif; ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
