<?php // Form permintaan baru: pilih obat + jumlah (+catatan); harga & stok terisi otomatis saat simpan. ?>
<?php if (validation_errors()): ?><div class="alert alert-danger"><?php echo validation_errors(); ?></div><?php endif; ?>
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Buat Permintaan Pengadaan</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="row">
            <div class="col-md-4"><div class="form-group"><label>Unit/Bagian</label><input type="text" name="unit" class="form-control" value="APOTEK" maxlength="100"></div></div>
            <div class="col-md-8"><div class="form-group"><label>Catatan</label><input type="text" name="catatan" class="form-control" maxlength="255" placeholder="Contoh: stok menipis, persiapan bulan depan"></div></div>
        </div>
        <h5>Item Obat</h5>
        <div id="minta_rows">
            <div class="row minta-row mb-2">
                <div class="col-md-5"><select name="items[0][id_obat]" class="form-control select2" required><option value="">-- Pilih Obat --</option><?php foreach ($obat_list as $o): ?><option value="<?php echo $o->id_obat; ?>"><?php echo html_escape($o->nama_obat); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><input type="number" min="1" name="items[0][jumlah_minta]" class="form-control" placeholder="Jumlah" required></div>
                <div class="col-md-3"><input type="text" name="items[0][catatan]" class="form-control" placeholder="Catatan item" maxlength="255"></div>
                <div class="col-md-1"><button type="button" class="btn btn-danger btn-del-row">×</button></div>
            </div>
        </div>
        <button type="button" id="btn_add_minta" class="btn btn-default btn-sm">Tambah Obat</button>
    </div>
    <div class="card-footer">
        <button type="submit" name="save" value="1" class="btn btn-primary">Simpan Draft</button>
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/permintaan'); ?>" class="btn btn-default float-right">Batal</a>
    </div><?php echo form_close(); ?>
</div></div></div>
<script>
(function () {
    var idx = 1;
    function baris() {
        var src = document.querySelector('.minta-row');
        var el = src.cloneNode(true);
        el.querySelectorAll('select, input').forEach(function (e) {
            e.name = e.name.replace(/items\[\d+\]/, 'items[' + idx + ']');
            if (e.tagName === 'INPUT') { e.value = ''; }
        });
        idx++;
        document.getElementById('minta_rows').appendChild(el);
    }
    document.getElementById('btn_add_minta').addEventListener('click', baris);
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-del-row')) {
            var rows = document.querySelectorAll('.minta-row');
            if (rows.length > 1) { e.target.closest('.minta-row').remove(); }
        }
    });
})();
</script>
