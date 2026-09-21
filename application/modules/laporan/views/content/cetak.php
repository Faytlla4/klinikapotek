<style>
@media print {
    .main-sidebar, .main-header, .main-footer, .content-header,
    .cetak-aksi, .card-tools, .breadcrumb { display: none !important; }
    .content-wrapper { margin: 0 !important; background: #fff !important; }
    .content { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
.lap-kop { text-align: center; border-bottom: 2px solid #064e3b; margin-bottom: 12px; padding-bottom: 8px; }
.lap-kop h4 { margin: 0; color: #064e3b; font-weight: 800; }
.card-body table { width: 100%; border-collapse: collapse; }
.card-body th, .card-body td { border: 1px solid #dee2e6; padding: .3rem; }
</style>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title"><?php echo html_escape($judul); ?></h3>
    <div class="card-tools cetak-aksi">
        <a href="<?php echo site_url(SITE_AREA . '/cetak/xlsx/' . $jenis . '?dari=' . $dari . '&sampai=' . $sampai); ?>" class="btn btn-sm btn-success"><i class="fas fa-file-excel mr-1"></i>Excel</a>
        <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="fas fa-print mr-1"></i>Cetak / PDF</button>
    </div></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3 cetak-aksi">
            <label class="mr-2" for="dari">Dari</label>
            <input type="date" id="dari" name="dari" class="form-control form-control-sm mr-3" value="<?php echo html_escape($dari); ?>" onchange="this.form.submit()">
            <label class="mr-2" for="sampai">Sampai</label>
            <input type="date" id="sampai" name="sampai" class="form-control form-control-sm mr-3" value="<?php echo html_escape($sampai); ?>" onchange="this.form.submit()">
        </form>
        <div class="lap-kop">
            <h4>Klinik &amp; Apotek</h4>
            <div><?php echo html_escape($judul); ?></div>
            <div><small>Periode <?php echo html_escape($dari); ?> s/d <?php echo html_escape($sampai); ?></small></div>
        </div>
        <?php $this->load->view('laporan/content/_tabel', array('jenis' => $jenis, 'rows' => $rows)); ?>
    </div>
</div></div></div>
