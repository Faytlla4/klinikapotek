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
    </tbody></table></div><div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a></div>
</div></div></div>
