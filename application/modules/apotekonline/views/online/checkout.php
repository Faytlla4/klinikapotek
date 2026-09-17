<div class="row"><div class="col-md-7"><div class="card">
    <div class="card-header"><h3 class="card-title">Ringkasan Pesanan</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-striped">
        <thead><tr><th>Obat</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?><tr><td><?php echo html_escape($it['nama_obat']); ?></td><td><?php echo $it['jumlah']; ?></td><td>Rp <?php echo number_format($it['harga'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?></td></tr><?php endforeach; ?>
        <tr><th colspan="3" class="text-right">Total</th><th>Rp <?php echo number_format($total, 0, ',', '.'); ?></th></tr>
        </tbody>
    </table></div>
</div></div>
<div class="col-md-5"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pengiriman &amp; Konfirmasi</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group"><label for="alamat">Alamat Pengiriman <span class="text-danger">*</span></label><textarea id="alamat" name="alamat" class="form-control" rows="4" required><?php echo html_escape(set_value('alamat', $pasien->alamat ?: '')); ?></textarea><small class="text-muted">Alamat tersimpan sebagai snapshot pada pesanan.</small></div>
        <div class="form-group"><label>No. HP</label><input class="form-control" readonly value="<?php echo html_escape($pasien->no_hp ?: '-'); ?>"></div>
    </div><div class="card-footer"><button type="submit" name="checkout" value="1" class="btn btn-primary">Buat Pesanan</button><a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>" class="btn btn-default float-right">Kembali</a></div><?php echo form_close(); ?>
</div></div></div>
