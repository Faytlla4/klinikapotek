<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Struk <?php echo html_escape($jual->nomor_penjualan); ?></h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($jual->tanggal_penjualan); ?></dd>
            <dt class="col-sm-3">Jenis</dt><dd class="col-sm-9"><span class="badge badge-info"><?php echo html_escape($jual->jenis_penjualan); ?></span></dd>
            <dt class="col-sm-3">Pasien</dt><dd class="col-sm-9"><?php echo html_escape(isset($jual->nama_pasien) && $jual->nama_pasien ? $jual->nama_pasien : '-'); ?></dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo html_escape($jual->status); ?></dd>
            <dt class="col-sm-3">Total</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $jual->total, 0, ',', '.'); ?></strong></dd>
        </dl>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Obat</th><th>Satuan</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($jual->items as $it): ?><tr><td><?php echo html_escape($it->nama_obat); ?></td><td><?php echo html_escape($it->satuan); ?></td><td><?php echo (int) $it->jumlah; ?></td><td>Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></td><td>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></td></tr><?php endforeach; ?>
            <?php if (empty($jual->items)): ?><tr><td colspan="5" class="text-center text-muted">Tidak ada item.</td></tr><?php endif; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/penjualan'); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
    </div>
</div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Struk Penjualan Obat</p></div>
        <div class="nota-row"><span>Nomor</span><strong><?php echo html_escape($jual->nomor_penjualan); ?></strong></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($jual->tanggal_penjualan); ?></span></div>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape(isset($jual->nama_pasien) && $jual->nama_pasien ? $jual->nama_pasien : '-'); ?></span></div>
        <div class="nota-sep"></div>
        <?php foreach ($jual->items as $it): ?>
        <div class="nota-row"><span><?php echo html_escape($it->nama_obat); ?></span><span></span></div>
        <div class="nota-row"><span class="text-muted"><?php echo (int) $it->jumlah; ?> &times; Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></span><span>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></span></div>
        <?php endforeach; ?>
        <div class="nota-sep"></div>
        <div class="nota-row nota-total"><span>TOTAL</span><span>Rp <?php echo number_format((float) $jual->total, 0, ',', '.'); ?></span></div>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>
