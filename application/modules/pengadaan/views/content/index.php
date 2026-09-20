<div class="row"><div class="col-md-12"><div class="card card-primary collapsed-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus mr-1"></i> Tambah Pengadaan</h3>
        <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button></div>
    </div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body" style="display:none;">
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Supplier</label><select name="id_supplier" class="form-control select2" required><option value="">-- Pilih --</option><?php foreach($supplier_list as $s): ?><option value="<?php echo $s->id_supplier; ?>"><?php echo html_escape($s->nama_supplier); ?></option><?php endforeach; ?></select></div></div>
            <div class="col-md-3"><div class="form-group"><label>Obat</label><select name="id_obat" class="form-control select2" required><option value="">-- Pilih --</option><?php foreach($obat_list as $o): ?><option value="<?php echo $o->id_obat; ?>"><?php echo html_escape($o->nama_obat); ?></option><?php endforeach; ?></select></div></div>
            <div class="col-md-2"><div class="form-group"><label>Jumlah</label><input type="number" min="1" name="jumlah_pesan" class="form-control" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>Harga</label><input type="number" min="0" name="harga" class="form-control" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>&nbsp;</label><button name="save" type="submit" class="btn btn-primary btn-block">Simpan</button></div></div>
        </div>
    </div><?php echo form_close(); ?>
</div></div></div>

<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Pengadaan</h3></div>
    <div class="card-body table-responsive"><table id="pengadaan_table" class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>Tanggal</th><th>Supplier</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
    </table></div>
</div></div></div>
