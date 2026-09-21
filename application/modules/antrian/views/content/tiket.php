<style>
.tiket-kertas { max-width: 320px; margin: 0 auto; background: #fff; text-align: center; }
.tiket-nomor { font-size: 3.2rem; font-weight: 800; color: #064e3b; line-height: 1.1; }
.tiket-poli { font-size: 1.1rem; font-weight: 700; }
.tiket-sep { border-top: 2px dashed #94a3b8; margin: 10px 0; }
@media print {
    .main-sidebar, .main-header, .main-footer, .content-header,
    .card-footer, .card-tools, .breadcrumb, .tiket-aksi { display: none !important; }
    .content-wrapper { margin: 0 !important; background: #fff !important; }
    .content { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Tiket Antrian</h3></div>
    <div class="card-body">
        <div class="tiket-kertas">
            <h5 class="mb-0">Klinik &amp; Apotek</h5>
            <small class="text-muted">Nomor Antrian</small>
            <div class="tiket-nomor"><?php echo html_escape($tiket->nomor_antrian); ?></div>
            <div class="tiket-poli"><?php echo html_escape($tiket->nama_poli ?: '-'); ?></div>
            <div class="tiket-sep"></div>
            <div class="text-left">
                <div class="d-flex justify-content-between"><span>Pasien</span><strong><?php echo html_escape($tiket->nama_pasien); ?></strong></div>
                <div class="d-flex justify-content-between"><span>No. RM</span><span><?php echo html_escape($tiket->no_rm); ?></span></div>
                <div class="d-flex justify-content-between"><span>Tanggal</span><span><?php echo html_escape($tiket->tanggal_antrian); ?></span></div>
                <div class="d-flex justify-content-between"><span>Dokter</span><span><?php echo html_escape($tiket->nama_dokter ?: '-'); ?></span></div>
            </div>
            <div class="tiket-sep"></div>
            <small class="text-muted">Simpan tiket ini &amp; tunggu panggilan.</small>
        </div>
    </div>
    <div class="card-footer tiket-aksi">
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/antrian'); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary float-right"><i class="fas fa-print mr-1"></i>Cetak</button>
    </div>
</div></div></div>
