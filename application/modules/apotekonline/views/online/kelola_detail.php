<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pesanan <?php echo html_escape($pesanan->nomor_pesanan); ?></h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->tanggal_pesanan); ?></dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge badge-info"><?php echo html_escape($pesanan->status); ?></span></dd>
            <dt class="col-sm-3">Pembayaran</dt><dd class="col-sm-9"><span class="badge <?php echo $pesanan->status_bayar === 'LUNAS' ? 'badge-success' : 'badge-warning'; ?>"><?php echo html_escape($pesanan->status_bayar); ?></span><?php if (! empty($pesanan->tagihan)): ?> (Tagihan: <?php echo html_escape($pesanan->tagihan->nomor_tagihan . ' - ' . $pesanan->tagihan->status); ?>)<?php endif; ?></dd>
            <dt class="col-sm-3">Alamat Kirim</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->alamat_kirim); ?></dd>
            <dt class="col-sm-3">No. HP Pasien</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->no_hp_pasien); ?></dd>
            <dt class="col-sm-3">Total</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $pesanan->total, 0, ',', '.'); ?></strong></dd>
        </dl>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Obat</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th><th>Stok Saat Ini</th></tr></thead>
            <tbody>
            <?php foreach ($pesanan->items as $it): ?><tr><td><?php echo html_escape($it->nama_obat); ?></td><td><?php echo (int) $it->jumlah; ?></td><td>Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></td><td>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></td><td><?php echo ((int) $it->stok >= (int) $it->jumlah) ? (int) $it->stok : '<span class="badge badge-danger">KURANG (' . (int) $it->stok . ')</span>'; ?></td></tr><?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pesanan-online'); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
        <?php echo form_open($this->uri->uri_string(), array('class' => 'd-inline float-right')); ?>
            <?php if ($pesanan->status === 'MENUNGGU'): ?><button type="submit" name="aksi" value="validasi" class="btn btn-info">Validasi</button><?php endif; ?>
            <?php if (in_array($pesanan->status, array('MENUNGGU', 'DIVERIFIKASI'))): ?><button type="submit" name="aksi" value="proses" class="btn btn-primary">Proses (buat penjualan+tagihan)</button><?php endif; ?>
            <?php if ($pesanan->status === 'DIPROSES'): ?><button type="submit" name="aksi" value="siap" class="btn btn-success">Siap</button><?php endif; ?>
            <?php if ($pesanan->status === 'SIAP'): ?><button type="submit" name="aksi" value="selesai" class="btn btn-success">Selesai</button><?php endif; ?>
            <?php if (! in_array($pesanan->status, array('SELESAI', 'BATAL'))): ?><button type="submit" name="aksi" value="batal" class="btn btn-danger" onclick="return confirm('Batalkan pesanan ini?')">Batal</button><?php endif; ?>
        <?php echo form_close(); ?>
    </div>
</div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Bukti Pesanan Online</p></div>
        <div class="nota-row"><span>Nomor</span><strong><?php echo html_escape($pesanan->nomor_pesanan); ?></strong></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($pesanan->tanggal_pesanan); ?></span></div>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape($pesanan->nama_pasien . ' (' . $pesanan->no_hp_pasien . ')'); ?></span></div>
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
