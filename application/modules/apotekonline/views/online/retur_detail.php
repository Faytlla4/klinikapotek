<div class="row"><div class="col-md-12"><div class="card <?php echo $retur->status === 'SELESAI' ? 'card-success' : ($retur->status === 'DITOLAK' ? 'card-danger' : 'card-warning'); ?>">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-undo"></i> Retur <?php echo html_escape($retur->nomor_retur); ?></h3>
        <div class="card-tools"><span class="badge <?php echo $retur->status === 'DIMINTA' ? 'badge-dark' : 'badge-light'; ?>"><?php echo html_escape($retur->status); ?></span></div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 col-6 mb-2"><small class="text-muted d-block">PESANAN</small><strong><?php echo html_escape($retur->pesanan->nomor_pesanan); ?></strong><br><small class="text-muted">Rp <?php echo number_format((float) $retur->pesanan->total, 0, ',', '.'); ?> &bull; <?php echo html_escape($retur->pesanan->status_bayar); ?></small></div>
            <div class="col-md-3 col-6 mb-2"><small class="text-muted d-block">PASIEN</small><strong><?php echo html_escape($retur->pasien ? $retur->pasien->nama : '-'); ?></strong><?php echo ($retur->pasien && $retur->pasien->no_hp) ? '<br><small class="text-muted">' . html_escape($retur->pasien->no_hp) . '</small>' : ''; ?></div>
            <div class="col-md-3 col-6 mb-2"><small class="text-muted d-block">DIAJUKAN</small><strong><?php echo html_escape($retur->tanggal_pengajuan); ?></strong></div>
            <div class="col-md-3 col-6 mb-2"><small class="text-muted d-block">DIPUTUS</small><strong><?php echo ! empty($retur->tanggal_keputusan) ? html_escape($retur->tanggal_keputusan) : '&mdash;'; ?></strong></div>
        </div>
        <div class="callout callout-warning">
            <h5><i class="fas fa-comment"></i> Pesan alasan retur dari pasien</h5>
            <p class="mb-0" style="font-size:15px"><?php echo nl2br(html_escape($retur->alasan)); ?></p>
        </div>
        <?php if ($retur->status === 'DIMINTA') echo form_open($this->uri->uri_string()); ?>
        <div class="table-responsive"><table class="table table-bordered table-striped table-hover">
            <thead><tr><th>Obat</th><th>Jumlah</th><th>Subtotal</th><th style="width:220px">Disposisi</th></tr></thead>
            <tbody>
            <?php foreach ($retur->items as $it): ?><tr>
                <td><?php echo html_escape($it->nama_obat); ?><br><small class="text-muted"><?php echo html_escape($it->satuan); ?> &times; Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></small></td>
                <td><?php echo (int) $it->jumlah; ?></td>
                <td>Rp <?php echo number_format((float) $it->jumlah * (float) $it->harga, 0, ',', '.'); ?></td>
                <td>
                    <?php if ($retur->status === 'DIMINTA'): ?>
                    <select name="disposisi[<?php echo (int) $it->id_detail; ?>]" class="form-control" required>
                        <option value="RESTOCK" <?php echo $it->disposisi === 'RESTOCK' ? 'selected' : ''; ?>>Restock — segel utuh, masuk stok</option>
                        <option value="MUSNAH" <?php echo $it->disposisi === 'MUSNAH' ? 'selected' : ''; ?>>Musnah — tidak kembali dijual</option>
                    </select>
                    <?php else: ?>
                    <span class="badge <?php echo $it->disposisi === 'MUSNAH' ? 'badge-danger' : 'badge-success'; ?>"><?php echo html_escape($it->disposisi ?: '-'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>            <?php endforeach; ?>
            </tbody>
            <tfoot><tr class="table-active"><th colspan="2" class="text-right">Total pesanan</th><th>Rp <?php echo number_format((float) $retur->pesanan->total, 0, ',', '.'); ?></th><th></th></tr></tfoot>
        </table></div>
        <?php if ($retur->status === 'DIMINTA'): ?>
        <div class="row">
            <div class="col-md-4"><div class="form-group">
                <label>Metode refund <span class="text-danger">*</span></label>
                <select name="metode_refund" class="form-control" required>
                    <option value="TUNAI">Tunai</option>
                    <option value="TRANSFER">Transfer</option>
                    <option value="E_WALLET">E-Wallet</option>
                    <option value="LAINNYA">Lainnya</option>
                </select>
                <small class="form-text text-muted">Refund manual oleh apoteker, tercatat di sini.</small>
            </div></div>
            <div class="col-md-4"><div class="form-group">
                <label>Nominal refund (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="nominal_refund" class="form-control" required min="0" max="<?php echo (float) $retur->pesanan->total; ?>" step="1" value="<?php echo (float) $retur->pesanan->total; ?>">
            </div></div>
            <div class="col-md-4"><div class="form-group">
                <label>Catatan untuk pasien</label>
                <textarea name="catatan" class="form-control" rows="2" maxlength="1000" placeholder="Wajib diisi bila menolak (min. 10 karakter)"></textarea>
            </div></div>
        </div>
        <?php else: ?>
        <dl class="row">
            <?php if (! empty($retur->catatan_apoteker)): ?>
            <dt class="col-sm-3">Catatan apoteker</dt><dd class="col-sm-9"><?php echo nl2br(html_escape($retur->catatan_apoteker)); ?></dd>
            <?php endif; ?>
            <?php if ($retur->status === 'SELESAI'): ?>
            <dt class="col-sm-3">Refund</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $retur->nominal_refund, 0, ',', '.'); ?></strong> via <?php echo html_escape($retur->metode_refund); ?></dd>
            <?php endif; ?>
        </dl>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/retur'); ?>" class="btn btn-default">Kembali</a>
        <?php if ($retur->status === 'DIMINTA'): ?>
        <span class="float-right">
            <button type="submit" name="aksi" value="tolak" class="btn btn-lg btn-danger" onclick="return confirm('Tolak pengajuan retur ini? Catatan wajib terisi.')"><i class="fas fa-times"></i> Tolak</button>
            <button type="submit" name="aksi" value="setujui" class="btn btn-lg btn-success" onclick="return confirm('Setujui retur? Stok RESTOCK bertambah dan refund tercatat. Tidak dapat dibatalkan.')"><i class="fas fa-check"></i> Setujui</button>
        </span>
        <?php endif; ?>
    </div>
    <?php if ($retur->status === 'DIMINTA') echo form_close(); ?>
</div></div></div>
