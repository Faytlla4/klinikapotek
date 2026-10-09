<?php // Detail permintaan: informasi, item, alur status, dan surat cetak. ?>
<?php $this->load->view('pengadaan/content/_print_styles'); ?>
<div class="pengadaan-screen">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt mr-1"></i> <?php echo html_escape($minta->nomor_permintaan); ?></h3>
                    <div class="card-tools">
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-light"><i class="fas fa-file-pdf mr-1"></i>Cetak / Simpan PDF</button>
                    </div>
                </div>
                <div class="card-body">
                    <strong>Tanggal</strong><p class="text-muted"><?php echo date('d-m-Y H:i', strtotime($minta->tanggal_permintaan)); ?></p><hr>
                    <strong>Unit</strong><p class="text-muted"><?php echo html_escape($minta->unit); ?></p><hr>
                    <strong>Status</strong><p><span class="badge <?php echo $minta->status === 'SELESAI' ? 'badge-success' : (in_array($minta->status, array('DITOLAK', 'DIBATALKAN')) ? 'badge-danger' : ($minta->status === 'DISETUJUI' ? 'badge-primary' : 'badge-warning')); ?>"><?php echo html_escape($minta->status); ?></span></p><hr>
                    <strong>Catatan</strong><p class="text-muted"><?php echo nl2br(html_escape($minta->catatan ?: '-')); ?></p>
                    <?php if (! empty($minta->tanggal_persetujuan)): ?><hr>
                    <strong>Persetujuan</strong><p class="text-muted"><?php echo date('d-m-Y H:i', strtotime($minta->tanggal_persetujuan)); ?><br><?php echo nl2br(html_escape($minta->catatan_persetujuan ?: '-')); ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/permintaan'); ?>" class="btn btn-default btn-block">Kembali</a>
                </div>
            </div>
            <?php if (! empty($minta->riwayat)): ?>
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Histori Status</h3></div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($minta->riwayat as $h): ?>
                    <li class="list-group-item py-2"><small class="text-muted"><?php echo date('d-m-Y H:i', strtotime($h->tanggal)); ?></small><br><?php echo $h->status_lama ? html_escape($h->status_lama) . ' → ' : ''; ?><strong><?php echo html_escape($h->status_baru); ?></strong><?php echo trim($h->catatan) !== '' ? '<br><small>' . html_escape($h->catatan) . '</small>' : ''; ?></li>
                    <?php endforeach; ?>
<<<<<<< Updated upstream
                    <div class="form-group"><label>Catatan</label><input type="text" name="catatan" class="form-control" value="<?php echo html_escape($minta->catatan); ?>"></div>
                    <button type="submit" name="aksi" value="simpan_draft" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                <?php endif; ?>
                <?php if ($minta->status === 'DIAJUKAN'): ?>
                    <div class="form-group"><label>Catatan putusan <span class="text-danger">*</span> <small class="text-muted">(wajib bila menolak)</small></label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                    <button type="submit" name="aksi" value="tolak" class="btn btn-danger" onclick="return confirm('Tolak permintaan ini?')"><i class="fas fa-times mr-1"></i>Tolak</button>
                    <button type="submit" name="aksi" value="setujui" class="btn btn-success float-right" onclick="return confirm('Setujui permintaan ini?')"><i class="fas fa-check mr-1"></i>Setujui</button>
                <?php endif; ?>
                <?php if (in_array($minta->status, array('DRAFT', 'DIAJUKAN'))): ?>
                    <div class="form-group mt-2"><label>Alasan batal <small class="text-muted">(wajib)</small></label><input type="text" name="catatan" class="form-control" placeholder="Batalkan bila salah input..."></div>
                    <button type="submit" name="aksi" value="batalkan" class="btn btn-default" onclick="return confirm('Batalkan permintaan ini?')">Batalkan</button>
                <?php endif; ?>
                <?php echo form_close(); ?>
                <?php if ($minta->status === 'DISETUJUI'): ?>
                <?php echo form_open($this->uri->uri_string()); ?>
                <hr><h5>Buat PO dari Permintaan Ini</h5>
                <div class="form-group"><label>Supplier <span class="text-danger">*</span></label><select name="id_supplier" class="form-control" required><option value="">-- Pilih Supplier --</option><?php foreach ($supplier_list as $s): ?><option value="<?php echo $s->id_supplier; ?>"<?php echo isset($supplier_default) && (int) $supplier_default === (int) $s->id_supplier ? ' selected' : ''; ?>><?php echo html_escape($s->nama_supplier); ?></option><?php endforeach; ?></select><?php if (! empty($supplier_default)): ?><small class="form-text text-muted">Otomatis dari Supplier Utama obat.</small><?php endif; ?></div>
                <p class="text-muted small">Harga terisi dari referensi; ubah bila harga supplier berbeda. Harga master tidak ikut berubah.</p>
                <?php foreach ($minta->items as $it): ?>
                <div class="form-group row"><label class="col-sm-6 col-form-label"><?php echo html_escape($it->nama_obat); ?> <small class="text-muted">(<?php echo (int) $it->jumlah_minta; ?> <?php echo html_escape($it->satuan); ?>)</small></label><div class="col-sm-6"><input type="number" min="0" step="1" name="harga[<?php echo (int) $it->id_obat; ?>]" class="form-control" value="<?php echo (float) $it->harga_referensi; ?>"></div></div>
                <?php endforeach; ?>
                <button type="submit" name="aksi" value="buat_po" class="btn btn-success btn-block" onclick="return confirm('Buat PO dari permintaan ini?')"><i class="fas fa-file-invoice mr-1"></i>Buat PO</button>
                <?php echo form_close(); ?>
                <?php endif; ?>
=======
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Item Diminta</h3></div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead><tr><th>Obat</th><th class="text-center">Stok Saat Minta</th><th class="text-center">Jumlah</th><th class="text-right">Harga Ref.</th></tr></thead>
                        <tbody>
                        <?php foreach ($minta->items as $it): ?>
                            <tr>
                                <td><strong><?php echo html_escape($it->nama_obat); ?></strong><br><small class="text-muted"><?php echo html_escape($it->kode_obat); ?> &middot; <?php echo html_escape($it->satuan); ?></small><?php echo trim($it->catatan) !== '' ? '<br><small>' . html_escape($it->catatan) . '</small>' : ''; ?></td>
                                <td class="text-center"><?php echo (int) $it->stok_saat_minta; ?></td>
                                <td class="text-center"><strong><?php echo (int) $it->jumlah_minta; ?></strong></td>
                                <td class="text-right">Rp <?php echo number_format((float) $it->harga_referensi, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <?php echo form_open($this->uri->uri_string()); ?>
                    <?php if ($minta->status === 'DRAFT'): ?>
                        <button type="submit" name="aksi" value="ajukan" class="btn btn-primary float-right" onclick="return confirm('Ajukan permintaan ini? Draft tidak bisa diubah setelah diajukan.')"><i class="fas fa-paper-plane mr-1"></i>Ajukan</button>
                        <hr>
                        <h5>Ubah Draft</h5>
                        <?php foreach ($minta->items as $it): ?>
                        <input type="hidden" name="items[<?php echo (int) $it->id_obat; ?>][id_obat]" value="<?php echo (int) $it->id_obat; ?>">
                        <div class="form-group row"><label class="col-sm-6 col-form-label"><?php echo html_escape($it->nama_obat); ?></label><div class="col-sm-6"><input type="number" min="1" name="items[<?php echo (int) $it->id_obat; ?>][jumlah_minta]" class="form-control" value="<?php echo (int) $it->jumlah_minta; ?>"></div></div>
                        <?php endforeach; ?>
                        <div class="form-group"><label>Catatan</label><input type="text" name="catatan" class="form-control" value="<?php echo html_escape($minta->catatan); ?>"></div>
                        <button type="submit" name="aksi" value="simpan_draft" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                    <?php endif; ?>
                    <?php if ($minta->status === 'DIAJUKAN'): ?>
                        <div class="form-group"><label>Catatan putusan <span class="text-danger">*</span> <small class="text-muted">(wajib bila menolak)</small></label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger" onclick="return confirm('Tolak permintaan ini?')"><i class="fas fa-times mr-1"></i>Tolak</button>
                        <button type="submit" name="aksi" value="setujui" class="btn btn-success float-right" onclick="return confirm('Setujui permintaan ini?')"><i class="fas fa-check mr-1"></i>Setujui</button>
                    <?php endif; ?>
                    <?php if (in_array($minta->status, array('DRAFT', 'DIAJUKAN'))): ?>
                        <div class="form-group mt-2"><label>Alasan batal <small class="text-muted">(wajib)</small></label><input type="text" name="catatan" class="form-control" placeholder="Batalkan bila salah input..."></div>
                        <button type="submit" name="aksi" value="batalkan" class="btn btn-default" onclick="return confirm('Batalkan permintaan ini?')">Batalkan</button>
                    <?php endif; ?>
                    <?php echo form_close(); ?>
                    <?php if ($minta->status === 'DISETUJUI'): ?>
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <hr><h5>Buat PO dari Permintaan Ini</h5>
                        <div class="form-group"><label>Supplier <span class="text-danger">*</span></label><select name="id_supplier" class="form-control" required><option value="">-- Pilih Supplier --</option><?php foreach ($supplier_list as $s): ?><option value="<?php echo $s->id_supplier; ?>"><?php echo html_escape($s->nama_supplier); ?></option><?php endforeach; ?></select></div>
                        <p class="text-muted small">Harga terisi dari referensi; ubah bila harga supplier berbeda. Harga master tidak ikut berubah.</p>
                        <?php foreach ($minta->items as $it): ?>
                        <div class="form-group row"><label class="col-sm-6 col-form-label"><?php echo html_escape($it->nama_obat); ?> <small class="text-muted">(<?php echo (int) $it->jumlah_minta; ?> <?php echo html_escape($it->satuan); ?>)</small></label><div class="col-sm-6"><input type="number" min="0" step="1" name="harga[<?php echo (int) $it->id_obat; ?>]" class="form-control" value="<?php echo (float) $it->harga_referensi; ?>"></div></div>
                        <?php endforeach; ?>
                        <button type="submit" name="aksi" value="buat_po" class="btn btn-success btn-block" onclick="return confirm('Buat PO dari permintaan ini?')"><i class="fas fa-file-invoice mr-1"></i>Buat PO</button>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                </div>
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
</div>
<div class="pengadaan-print">
    <div class="print-kop"><h2>Klinik &amp; Apotek</h2><div>Dokumen Pengadaan Obat</div></div>
    <div class="print-title">Permintaan Pengadaan</div>
    <table class="print-info">
        <tr><th>Nomor Permintaan</th><td><?php echo html_escape($minta->nomor_permintaan); ?></td></tr>
        <tr><th>Tanggal</th><td><?php echo date('d-m-Y H:i', strtotime($minta->tanggal_permintaan)); ?></td></tr>
        <tr><th>Unit / Bagian</th><td><?php echo html_escape($minta->unit); ?></td></tr>
        <tr><th>Status</th><td><?php echo html_escape($minta->status); ?></td></tr>
        <tr><th>Catatan</th><td><?php echo nl2br(html_escape($minta->catatan ?: '-')); ?></td></tr>
        <?php if (! empty($minta->tanggal_persetujuan)): ?>
        <tr><th>Tanggal Persetujuan</th><td><?php echo date('d-m-Y H:i', strtotime($minta->tanggal_persetujuan)); ?></td></tr>
        <tr><th>Catatan Persetujuan</th><td><?php echo nl2br(html_escape($minta->catatan_persetujuan ?: '-')); ?></td></tr>
        <?php endif; ?>
    </table>
    <table class="print-items">
        <thead><tr><th>No.</th><th>Obat</th><th>Kode</th><th>Satuan</th><th>Stok Saat Minta</th><th>Jumlah Diminta</th><th>Harga Referensi</th><th>Subtotal</th></tr></thead>
        <tbody><?php $total_referensi = 0; foreach ($minta->items as $index => $it): $subtotal_referensi = (float) $it->jumlah_minta * (float) $it->harga_referensi; $total_referensi += $subtotal_referensi; ?>
            <tr>
                <td class="print-center"><?php echo (int) $index + 1; ?></td>
                <td><?php echo html_escape($it->nama_obat); ?></td>
                <td><?php echo html_escape($it->kode_obat); ?></td>
                <td><?php echo html_escape(isset($it->satuan) ? $it->satuan : '-'); ?></td>
                <td class="print-center"><?php echo (int) $it->stok_saat_minta; ?></td>
                <td class="print-center"><?php echo (int) $it->jumlah_minta; ?></td>
                <td class="print-right">Rp <?php echo number_format((float) $it->harga_referensi, 0, ',', '.'); ?></td>
                <td class="print-right">Rp <?php echo number_format($subtotal_referensi, 0, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?></tbody>
        <tfoot><tr><th colspan="7" class="print-right">Total Referensi</th><th class="print-right">Rp <?php echo number_format($total_referensi, 0, ',', '.'); ?></th></tr></tfoot>
    </table>
    <?php if (! empty($minta->riwayat)): ?>
    <strong>Riwayat Status</strong>
    <table class="print-items">
        <thead><tr><th>Tanggal</th><th>Status</th><th>Catatan</th></tr></thead>
        <tbody><?php foreach ($minta->riwayat as $h): ?>
            <tr>
                <td><?php echo date('d-m-Y H:i', strtotime($h->tanggal)); ?></td>
                <td><?php echo html_escape(($h->status_lama ? $h->status_lama . ' → ' : '') . $h->status_baru); ?></td>
                <td><?php echo html_escape($h->catatan ?: '-'); ?></td>
            </tr>
        <?php endforeach; ?></tbody>
    </table>
    <?php endif; ?>
    <div class="print-signatures">
        <div class="print-signature">Pemohon<div class="print-signature-space"></div><strong>(........................................)</strong></div>
        <div class="print-signature">Menyetujui<div class="print-signature-space"></div><strong>(........................................)</strong></div>
    </div>
</div>
