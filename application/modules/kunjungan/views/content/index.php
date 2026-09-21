<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Kunjungan</h3></div>
    <div class="card-body">
    <div class="form-inline mb-2">
        <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" id="filter-dari" class="form-control" style="max-width:160px;" title="Dari tanggal"></div>
        <span class="mr-2 mb-1 text-muted">s/d</span>
        <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" id="filter-sampai" class="form-control" style="max-width:160px;" title="Sampai tanggal"></div>
        <button type="button" id="filter-reset" class="btn btn-sm btn-default mb-1" title="Reset filter">&times;</button>
    </div>
    <div class="table-responsive"><table id="kunjungan_table" class="table table-bordered table-hover table-striped">
        <thead><tr><th>Tanggal</th><th>No. RM</th><th>Pasien</th><th>Pelayanan</th><th>Poli</th><th>Dokter</th><th>Ruangan</th><th>Status</th></tr></thead>
    </table></div>
    </div>
</div></div></div>
