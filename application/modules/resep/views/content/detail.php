<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-12"><div class="card card-primary"><div class="card-header"><h3 class="card-title">Detail Resep</h3></div><div class="card-body"><dl class="row"><dt class="col-sm-3">No. Resep</dt><dd class="col-sm-9"><?php echo html_escape($resep->nomor_resep); ?></dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo html_escape($resep->status); ?></dd></dl><table class="table table-bordered"><thead><tr><th>Obat</th><th>Jumlah</th><th>Dosis</th><th>Aturan Pakai</th><th>Stok</th></tr></thead><tbody><?php foreach($resep->detail as $d): ?><tr><td><?php echo html_escape($d->nama_obat); ?></td><td><?php echo html_escape($d->jumlah); ?></td><td><?php echo html_escape($d->dosis ?: '-'); ?></td><td><?php echo html_escape($d->aturan_pakai ?: '-'); ?></td><td><?php echo html_escape($d->stok); ?></td></tr><?php endforeach; ?></tbody></table></div><div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/content/resep'); ?>" class="btn btn-default">Kembali</a> <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button></div></div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Salinan Resep</p></div>
        <div class="nota-row"><span>No. Resep</span><strong><?php echo html_escape($resep->nomor_resep); ?></strong></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($resep->tanggal_resep); ?></span></div>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape(isset($resep->nama_pasien) && $resep->nama_pasien ? $resep->nama_pasien : '-'); ?></span></div>
        <div class="nota-row"><span>Dokter</span><span><?php echo html_escape(isset($resep->nama_dokter) && $resep->nama_dokter ? $resep->nama_dokter : '-'); ?></span></div>
        <div class="nota-sep"></div>
        <?php foreach($resep->detail as $d): ?>
        <div class="nota-row"><span><?php echo html_escape($d->nama_obat); ?></span><span><?php echo html_escape($d->jumlah); ?></span></div>
        <div class="nota-row"><span class="text-muted"><?php echo html_escape(($d->dosis ?: '-') . ' • ' . ($d->aturan_pakai ?: '-')); ?></span><span></span></div>
        <?php endforeach; ?>
        <?php if (!empty($resep->catatan)): ?>
        <div class="nota-sep"></div>
        <div class="nota-row"><span>Catatan</span><span><?php echo html_escape($resep->catatan); ?></span></div>
        <?php endif; ?>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>
