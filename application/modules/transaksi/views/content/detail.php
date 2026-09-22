<?php /* ponytail: nota bukti transaksi (bahasa terpadu di partials/_nota) */ ?>
<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="row"><div class="col-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Bukti Transaksi</h3></div>
    <div class="card-body">
        <div class="nota">
            <div class="nota-head">
                <h4>Klinik &amp; Apotek</h4>
                <p>Bukti Pembayaran</p>
            </div>
            <div class="nota-row"><span>No. Transaksi</span><strong><?php echo html_escape($transaksi->nomor_transaksi); ?></strong></div>
            <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($transaksi->tanggal_transaksi); ?></span></div>
            <div class="nota-row"><span>Status</span><strong><?php echo html_escape($transaksi->status); ?></strong></div>
            <div class="nota-sep"></div>
            <?php foreach ($transaksi->items as $item): ?>
            <div class="nota-row"><span><?php echo html_escape($item->nama_item); ?></span><span></span></div>
            <div class="nota-row"><span class="text-muted"><?php echo (int) $item->jumlah; ?> &times; Rp <?php echo number_format((float) $item->harga, 0, ',', '.'); ?></span><span>Rp <?php echo number_format((float) $item->subtotal, 0, ',', '.'); ?></span></div>
            <?php endforeach; ?>
            <div class="nota-sep"></div>
            <div class="nota-row nota-total"><span>TOTAL</span><span>Rp <?php echo number_format((float) $transaksi->total, 0, ',', '.'); ?></span></div>
            <?php if (!empty($transaksi->pembayaran)): foreach ($transaksi->pembayaran as $p): ?>
            <div class="nota-row"><span>Bayar (<?php echo html_escape($p->tanggal_pembayaran); ?>)</span><span>Rp <?php echo number_format((float) $p->jumlah_bayar, 0, ',', '.'); ?></span></div>
            <div class="nota-row"><span>Kembalian</span><span>Rp <?php echo number_format((float) $p->kembalian, 0, ',', '.'); ?></span></div>
            <?php endforeach; endif; ?>
            <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
        </div>
    </div>
    <div class="card-footer nota-aksi">
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary float-right"><i class="fas fa-print"></i> Cetak</button>
    </div>
</div></div></div>
