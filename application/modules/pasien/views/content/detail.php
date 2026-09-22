<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Detail Pasien</h3></div>
    <div class="card-body"><dl class="row">
        <dt class="col-sm-3">Nomor Rekam Medis</dt><dd class="col-sm-9"><?php echo html_escape($pasien->no_rm); ?></dd>
        <dt class="col-sm-3">NIK</dt><dd class="col-sm-9"><?php echo html_escape($pasien->nik ?: '-'); ?></dd>
        <dt class="col-sm-3">Nama</dt><dd class="col-sm-9"><?php echo html_escape($pasien->nama); ?></dd>
        <dt class="col-sm-3">Tanggal Lahir</dt><dd class="col-sm-9"><?php echo html_escape($pasien->tanggal_lahir ?: '-'); ?></dd>
        <dt class="col-sm-3">Jenis Kelamin</dt><dd class="col-sm-9"><?php echo html_escape($pasien->jenis_kelamin ?: '-'); ?></dd>
        <dt class="col-sm-3">Alamat</dt><dd class="col-sm-9"><?php echo html_escape($pasien->alamat ?: '-'); ?></dd>
        <dt class="col-sm-3">No. HP</dt><dd class="col-sm-9"><?php echo html_escape($pasien->no_hp ?: '-'); ?></dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo html_escape($pasien->status); ?></dd>
    </dl><hr><h4>Riwayat Kunjungan</h4>
    <table class="table table-bordered table-hover"><thead><tr><th>Tanggal</th><th>Pelayanan</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($pasien->kunjungan)): ?><tr><td colspan="3" class="text-center">Belum ada kunjungan.</td></tr><?php else: foreach ($pasien->kunjungan as $k): ?><tr><td><?php echo html_escape($k->tanggal_kunjungan); ?></td><td><?php echo html_escape($k->nama_pelayanan ?: '-'); ?></td><td><?php echo html_escape($k->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table><h4>Riwayat Pemeriksaan</h4>
    <table class="table table-bordered table-hover"><thead><tr><th>Tanggal</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($pasien->pemeriksaan)): ?><tr><td colspan="4" class="text-center">Belum ada pemeriksaan.</td></tr><?php else: foreach ($pasien->pemeriksaan as $p): ?><tr><td><?php echo html_escape($p->tanggal_pemeriksaan); ?></td><td><?php echo html_escape($p->nama_dokter ?: '-'); ?></td><td><?php echo html_escape($p->keluhan ?: '-'); ?></td><td><?php echo html_escape($p->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table><h4>Riwayat Resep</h4>
    <table class="table table-bordered table-hover"><thead><tr><th>Nomor</th><th>Tanggal</th><th>Dokter</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($pasien->resep)): ?><tr><td colspan="4" class="text-center">Belum ada resep.</td></tr><?php else: foreach ($pasien->resep as $r): ?><tr><td><?php echo html_escape($r->nomor_resep); ?></td><td><?php echo html_escape($r->tanggal_resep); ?></td><td><?php echo html_escape($r->nama_dokter ?: '-'); ?></td><td><?php echo html_escape($r->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table><h4>Riwayat Transaksi Pelayanan</h4>
    <table class="table table-bordered table-hover"><thead><tr><th>Nomor</th><th>Tagihan</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead><tbody>
        <?php if (empty($pasien->transaksi)): ?><tr><td colspan="5" class="text-center">Belum ada transaksi pelayanan.</td></tr><?php else: foreach ($pasien->transaksi as $t): ?><tr><td><?php echo html_escape($t->nomor_transaksi); ?></td><td><?php echo html_escape($t->nomor_tagihan); ?></td><td><?php echo html_escape($t->tanggal_transaksi); ?></td><td><?php echo html_escape($t->total); ?></td><td><?php echo html_escape($t->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div><div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a> <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button></div>
</div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Kartu Pasien</p></div>
        <div class="nota-row"><span>No. RM</span><strong><?php echo html_escape($pasien->no_rm); ?></strong></div>
        <div class="nota-row"><span>Nama</span><span><?php echo html_escape($pasien->nama); ?></span></div>
        <div class="nota-row"><span>NIK</span><span><?php echo html_escape($pasien->nik ?: '-'); ?></span></div>
        <div class="nota-row"><span>Lahir</span><span><?php echo html_escape(($pasien->tanggal_lahir ?: '-') . ' / ' . ($pasien->jenis_kelamin ?: '-')); ?></span></div>
        <?php if (!empty($pasien->kunjungan)): $tk = $pasien->kunjungan[0]; ?>
        <div class="nota-sep"></div>
        <div class="nota-row"><span>Kunjungan Terakhir</span><span><?php echo html_escape($tk->tanggal_kunjungan); ?></span></div>
        <div class="nota-row"><span>Pelayanan</span><span><?php echo html_escape($tk->nama_pelayanan ?: '-'); ?></span></div>
        <?php endif; ?>
        <div class="nota-foot">Simpan kartu ini untuk setiap kunjungan.</div>
    </div>
</div>
