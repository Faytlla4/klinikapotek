<?php // Form retur manual: jumlah ≤ sisa dapat-diretur, alasan wajib, batch/ED bila spesifik. ?>
<div class="row"><div class="col-md-12"><div class="card card-danger">
    <div class="card-header"><h3 class="card-title">Buat Retur — <?php echo html_escape($penerimaan->nomor_penerimaan); ?> (<?php echo html_escape($penerimaan->nama_supplier); ?>)</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group"><label>Keterangan retur</label><input type="text" name="keterangan" class="form-control" maxlength="255" placeholder="Contoh: 5 strip rusak saat bongkar"></div>
        <div class="table-responsive"><table class="table table-bordered">
            <thead><tr><th>Obat</th><th>Diterima</th><th style="width:110px">Jumlah Retur</th><th>Alasan <span class="text-danger">*</span></th><th style="width:130px">Batch</th><th style="width:150px">ED</th></tr></thead>
            <tbody>
            <?php foreach ($penerimaan->items as $it): ?><tr>
                <td><strong><?php echo html_escape($it->nama_obat); ?></strong><br><small class="text-muted"><?php echo html_escape($it->satuan); ?> &middot; <?php echo html_escape($it->kondisi); ?></small></td>
                <td class="text-center">+<?php echo (int) $it->jumlah_terima; ?></td>
                <td><input type="number" min="0" name="items[<?php echo (int) $it->id_obat; ?>][jumlah]" class="form-control" placeholder="0"></td>
                <td><input type="text" name="items[<?php echo (int) $it->id_obat; ?>][alasan]" class="form-control" maxlength="255" placeholder="Rusak/kadaluarsa/..."></td>
                <td><input type="text" name="items[<?php echo (int) $it->id_obat; ?>][nomor_batch]" class="form-control" maxlength="100" value="<?php echo html_escape($it->nomor_batch ?: ''); ?>"></td>
                <td><input type="date" name="items[<?php echo (int) $it->id_obat; ?>][tanggal_kadaluarsa]" class="form-control" value="<?php echo html_escape($it->tanggal_kadaluarsa ?: ''); ?>"></td>
            </tr><?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card-footer">
        <button type="submit" name="save" value="1" class="btn btn-danger" onclick="return confirm('Ajukan retur ini? Stok berkurang setelah dikonfirmasi.')"><i class="fas fa-undo mr-1"></i>Ajukan Retur</button>
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/penerimaan_detail/' . (int) $penerimaan->id_penerimaan); ?>" class="btn btn-default float-right">Batal</a>
    </div><?php echo form_close(); ?>
</div></div></div>
