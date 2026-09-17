<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Detail Kunjungan</h3></div>
    <div class="card-body"><dl class="row">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->tanggal_kunjungan); ?></dd>
        <dt class="col-sm-3">No. RM</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->no_rm); ?></dd>
        <dt class="col-sm-3">Pasien</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_pasien); ?></dd>
        <dt class="col-sm-3">Pelayanan</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_pelayanan); ?></dd>
        <dt class="col-sm-3">Poli</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_poli); ?></dd>
        <dt class="col-sm-3">Dokter</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_dokter); ?></dd>
        <dt class="col-sm-3">Ruangan</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_ruangan); ?></dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->status); ?></dd>
    </dl></div><div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a></div>
</div></div></div>
