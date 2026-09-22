<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pesanan <?php echo html_escape($pesanan->nomor_pesanan); ?></h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->tanggal_pesanan); ?></dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge badge-info"><?php echo html_escape($pesanan->status); ?></span></dd>
            <dt class="col-sm-3">Pembayaran</dt><dd class="col-sm-9"><span class="badge <?php echo $pesanan->status_bayar === 'LUNAS' ? 'badge-success' : 'badge-warning'; ?>"><?php echo html_escape($pesanan->status_bayar); ?></span></dd>
            <dt class="col-sm-3">Alamat Kirim</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->alamat_kirim); ?></dd>
            <dt class="col-sm-3">Total</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $pesanan->total, 0, ',', '.'); ?></strong></dd>
        </dl>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Obat</th><th>Satuan</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($pesanan->items as $it): ?><tr><td><?php echo html_escape($it->nama_obat); ?></td><td><?php echo html_escape($it->satuan); ?></td><td><?php echo (int) $it->jumlah; ?></td><td>Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></td><td>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></td></tr><?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
        <?php if ($pesanan->status === 'MENUNGGU'): echo form_open($this->uri->uri_string(), array('class' => 'd-inline float-right')); ?><button type="submit" name="batalkan" value="1" class="btn btn-danger" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button><?php echo form_close(); endif; ?>
    </div>
</div></div>    </div>
    <?php $retur_aktif = ! empty($retur) && in_array($retur->status, array('DIMINTA', 'SELESAI')); ?>
    <?php if (! empty($retur)): ?>
    <div class="row"><div class="col-md-12"><div class="card <?php echo $retur->status === 'SELESAI' ? 'card-success' : ($retur->status === 'DITOLAK' ? 'card-danger' : 'card-warning'); ?> card-outline">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-undo"></i> Retur <?php echo html_escape($retur->nomor_retur); ?></h3>
            <div class="card-tools"><span class="badge <?php echo $retur->status === 'SELESAI' ? 'badge-success' : ($retur->status === 'DITOLAK' ? 'badge-danger' : 'badge-warning'); ?>"><?php echo html_escape($retur->status); ?></span></div>
        </div>
        <div class="card-body">
            <div class="callout callout-warning">
                <h5><i class="fas fa-comment"></i> Alasan retur Anda</h5>
                <p class="mb-0"><?php echo nl2br(html_escape($retur->alasan)); ?></p>
                <small class="text-muted">Diajukan <?php echo html_escape($retur->tanggal_pengajuan); ?></small>
            </div>
            <?php if (! empty($retur->catatan_apoteker)): ?>
            <div class="callout callout-info">
                <h5><i class="fas fa-reply"></i> Tanggapan apoteker</h5>
                <p class="mb-0"><?php echo nl2br(html_escape($retur->catatan_apoteker)); ?></p>
                <?php if (! empty($retur->tanggal_keputusan)): ?><small class="text-muted"><?php echo html_escape($retur->tanggal_keputusan); ?></small><?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ($retur->status === 'SELESAI'): ?>
            <dl class="row mb-0">
                <dt class="col-sm-3">Refund</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $retur->nominal_refund, 0, ',', '.'); ?></strong> via <?php echo html_escape($retur->metode_refund); ?> (manual oleh apoteker)</dd>
            </dl>
            <?php endif; ?>
        </div>
    </div></div></div>
    <?php endif; ?>
    <?php if (! $retur_aktif && $pesanan->status === 'SELESAI' && $pesanan->status_bayar === 'LUNAS'): ?>
    <div class="row"><div class="col-md-12"><div class="card card-outline card-secondary collapsed-card">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-undo"></i> Ajukan Retur <?php echo ! empty($retur) ? '(pengajuan ulang)' : ''; ?></h3>
            <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button></div>
        </div>
        <div class="card-body">
            <?php echo form_open($this->uri->uri_string()); ?>
                <div class="form-group">
                    <label for="alasan">Alasan retur <span class="text-danger">*</span></label>
                    <textarea name="alasan" id="alasan" class="form-control" rows="3" required minlength="10" maxlength="1000" placeholder="Contoh: kemasan rusak saat diterima, obat tidak sesuai pesanan..."></textarea>
                    <small class="form-text text-muted">Seluruh pesanan diretur. Tulis alasan sejelas mungkin (minimal 10 karakter).</small>
                </div>
                <button type="submit" name="ajukan_retur" value="1" class="btn btn-warning" onclick="return confirm('Ajukan retur untuk seluruh pesanan ini?')"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
            <?php echo form_close(); ?>
        </div>
    </div></div></div>
    <?php endif; ?>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Bukti Pesanan Online</p></div>
        <div class="nota-row"><span>Nomor</span><strong><?php echo html_escape($pesanan->nomor_pesanan); ?></strong></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($pesanan->tanggal_pesanan); ?></span></div>
        <div class="nota-row"><span>Pemesan</span><span><?php echo html_escape(isset($nama_pemesan) && $nama_pemesan ? $nama_pemesan : '-'); ?></span></div>
        <div class="nota-row"><span>Status</span><strong><?php echo html_escape($pesanan->status . ' / ' . $pesanan->status_bayar); ?></strong></div>
        <div class="nota-sep"></div>
        <?php foreach ($pesanan->items as $it): ?>
        <div class="nota-row"><span><?php echo html_escape($it->nama_obat); ?></span><span></span></div>
        <div class="nota-row"><span class="text-muted"><?php echo (int) $it->jumlah; ?> &times; Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></span><span>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></span></div>
        <?php endforeach; ?>
        <div class="nota-sep"></div>
        <div class="nota-row nota-total"><span>TOTAL</span><span>Rp <?php echo number_format((float) $pesanan->total, 0, ',', '.'); ?></span></div>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>
