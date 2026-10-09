<?php // Saran restock: obat di bawah minimum (di luar permintaan aktif); isi jumlah lalu jadi draft. ?>
<div class="row"><div class="col-12"><div class="card card-warning">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-lightbulb mr-1"></i>Saran Restock</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <p class="text-muted">Obat di bawah stok minimum yang belum masuk permintaan aktif. Ubah jumlah bila perlu, lalu buat draft — <strong>PO tidak dibuat otomatis.</strong></p>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Obat</th><th class="text-center">Stok</th><th class="text-center">Minimum</th><th style="width:130px">Jumlah Minta</th></tr></thead>
            <tbody>
            <?php if (empty($saran_list)): ?><tr><td colspan="4" class="text-center text-muted">Semua stok aman, tidak ada saran.</td></tr>
            <?php else: foreach ($saran_list as $s): ?><tr>
                <td><strong><?php echo html_escape($s->nama_obat); ?></strong><br><small class="text-muted"><?php echo html_escape($s->satuan); ?></small></td>
                <td class="text-center"><span class="badge badge-danger"><?php echo (int) $s->stok; ?></span></td>
                <td class="text-center"><?php echo (int) $s->stok_minimum; ?></td>
                <td><input type="number" min="0" name="items[<?php echo (int) $s->id_obat; ?>][jumlah]" class="form-control" value="<?php echo (int) $s->saran; ?>"></td>
            </tr><?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
    <?php if (! empty($saran_list)): ?>
    <div class="card-footer">
        <button type="submit" name="save" value="1" class="btn btn-primary" onclick="return confirm('Buat draft permintaan dari saran ini?')"><i class="fas fa-file-alt mr-1"></i>Buat Draft Permintaan</button>
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/permintaan'); ?>" class="btn btn-default float-right">Batal</a>
    </div>
    <?php endif; ?><?php echo form_close(); ?>
</div></div></div>
