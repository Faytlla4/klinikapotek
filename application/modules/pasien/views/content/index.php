<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Pasien</h3></div>
    <div class="card-body table-responsive"><table id="pasien_table" class="table table-bordered table-hover table-striped" data-endpoint="<?php echo html_escape(site_url($this->uri->uri_string() . '/get_data')); ?>" data-edit-url="<?php echo html_escape(site_url($this->uri->uri_string() . '/edit')); ?>">
        <thead><tr><th>No. RM</th><th>NIK</th><th>Nama</th><th>Tanggal Lahir</th><th>Jenis Kelamin</th><th>Status</th><th>Aksi</th></tr></thead>
    </table></div>
</div></div></div>

