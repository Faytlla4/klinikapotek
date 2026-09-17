<?php if (validation_errors()): ?><div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo validation_errors(); ?></div><?php endif; ?>
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Form Jual Obat</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="row">
            <div class="col-md-4"><div class="form-group"><label for="jenis_penjualan">Jenis <span class="text-danger">*</span></label><select id="jenis_penjualan" name="jenis_penjualan" class="form-control"><option value="LANGSUNG">LANGSUNG</option><option value="RESEP">RESEP</option></select></div></div>
            <div class="col-md-4"><div class="form-group" id="wrap_resep" style="display:none;"><label for="id_resep">Resep</label><select id="id_resep" name="id_resep" class="form-control select2"><option value="">-- Pilih Resep --</option><?php foreach ($resep_list as $r): ?><option value="<?php echo $r->id_resep; ?>" data-pasien="<?php echo $r->id_pasien; ?>"><?php echo html_escape($r->nomor_resep . ' - ' . $r->nama_pasien); ?></option><?php endforeach; ?></select></div></div>
            <div class="col-md-4"><div class="form-group"><label for="id_pasien">Pasien</label><select id="id_pasien" name="id_pasien" class="form-control select2"><option value="">-- Umum --</option><?php foreach ($pasien_list as $p): ?><option value="<?php echo $p->id_pasien; ?>"><?php echo html_escape($p->no_rm . ' - ' . $p->nama); ?></option><?php endforeach; ?></select></div></div>
        </div>
        <h5>Item Obat</h5>
        <div id="item_rows">
            <div class="row item-row mb-2">
                <div class="col-md-6"><select name="id_obat[]" class="form-control select2" required><option value="">-- Pilih Obat --</option><?php foreach ($obat_list as $o): ?><option value="<?php echo $o->id_obat; ?>"><?php echo html_escape($o->nama_obat); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><input type="number" min="1" name="jumlah[]" class="form-control" placeholder="Jumlah" required></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger btn-block btn-del-row">Hapus</button></div>
            </div>
        </div>
        <button type="button" id="btn_add_row" class="btn btn-default btn-sm">Tambah Obat</button>
    </div><div class="card-footer"><button type="submit" name="save" value="1" class="btn btn-primary">Simpan Penjualan</button><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default float-right">Batal</a></div><?php echo form_close(); ?>
</div></div></div>
<script type="text/javascript">var daftarObat = <?php $o = array(); foreach ($obat_list as $x) { $o[] = array('id' => (int) $x->id_obat, 'nama' => $x->nama_obat); } echo json_encode($o); ?>;</script>
