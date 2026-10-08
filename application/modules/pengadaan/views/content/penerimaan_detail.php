<?php // Detail penerimaan: dokumen, item+batch/ED, tombol Periksa/Konfirmasi, link retur & PO. ?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-truck mr-1"></i> <?php echo html_escape($terima->nomor_penerimaan); ?></h3></div>
            <div class="card-body">
                <strong>PO</strong><p class="text-muted"><?php echo html_escape($terima->nomor_pengadaan); ?> &middot; <?php echo html_escape($terima->nama_supplier); ?></p><hr>
                <strong>Tanggal Terima</strong><p class="text-muted"><?php echo date('d-m-Y H:i', strtotime($terima->tanggal_terima)); ?></p><hr>
                <strong>Surat Jalan / Faktur</strong><p class="text-muted"><?php echo html_escape($terima->no_surat_jalan ?: '-'); ?> / <?php echo html_escape($terima->no_faktur ?: '-'); ?></p><hr>
                <strong>Status</strong><p><span class="badge <?php echo $terima->status_konfirmasi === 'DIKONFIRMASI' ? 'badge-success' : ($terima->status_konfirmasi === 'DIBATALKAN' ? 'badge-danger' : 'badge-warning'); ?>"><?php echo html_escape($terima->status_konfirmasi); ?></span> <span class="badge badge-info"><?php echo html_escape($terima->status); ?></span></p><hr>
                <strong>Catatan</strong><p class="text-muted"><?php echo nl2br(html_escape($terima->catatan ?: '-')); ?></p>
            </div>
            <div class="card-footer">
                <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/detail/' . (int) $terima->id_pengadaan); ?>" class="btn btn-default btn-block">Kembali ke PO</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Item Diterima</h3></div>
            <div class="card-body table-responsive p-0"><table class="table table-bordered table-striped mb-0">
                <thead><tr><th>Obat</th><th class="text-center">Jumlah</th><th>Kondisi</th><th>Batch</th><th>ED</th></tr></thead>
                <tbody>
                <?php foreach ($terima->items as $it): ?><tr>
                    <td><strong><?php echo html_escape($it->nama_obat); ?></strong><br><small class="text-muted"><?php echo html_escape($it->satuan); ?></small></td>
                    <td class="text-center"><strong>+<?php echo (int) $it->jumlah_terima; ?></strong></td>
                    <td><span class="badge badge-<?php echo strtoupper($it->kondisi) === 'BAIK' ? 'success' : 'danger'; ?>"><?php echo html_escape($it->kondisi); ?></span></td>
                    <td><?php echo html_escape($it->nomor_batch ?: '-'); ?></td>
                    <td><?php echo $it->tanggal_kadaluarsa ? date('d-m-Y', strtotime($it->tanggal_kadaluarsa)) : '-'; ?></td>
                </tr><?php endforeach; ?>
                </tbody>
            </table></div>
            <div class="card-footer">
                <?php echo form_open($this->uri->uri_string()); ?>
                <?php if ($terima->status_konfirmasi === 'DRAFT'): ?>
                    <div class="form-group"><label>Catatan periksa</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                    <button type="submit" name="aksi" value="periksa" class="btn btn-warning" onclick="return confirm('Tandai DIPERIKSA? Stok belum berubah.')"><i class="fas fa-search mr-1"></i>Tandai Diperiksa</button>
                <?php endif; ?>
                <?php if ($terima->status_konfirmasi === 'DIPERIKSA'): ?>
                    <div class="form-group"><label>Catatan konfirmasi</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                    <button type="submit" name="aksi" value="konfirmasi" class="btn btn-success" onclick="return confirm('Konfirmasi? Stok + batch + mutasi tercatat. Tidak dapat dibatalkan.')"><i class="fas fa-check mr-1"></i>Konfirmasi (Stok +)</button>
                <?php endif; ?>
                <?php if ($terima->status_konfirmasi === 'DIKONFIRMASI'): ?>
                    <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/retur_buat/' . (int) $terima->id_penerimaan); ?>" class="btn btn-danger"><i class="fas fa-undo mr-1"></i>Buat Retur</a>
                <?php endif; ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
