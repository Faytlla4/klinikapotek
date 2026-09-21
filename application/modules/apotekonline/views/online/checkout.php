<style>
/* ponytail: checkout pasien — stepper + ringkasan + konfirmasi */
.co-steps { display: flex; gap: 0; margin-bottom: 16px; }
.co-steps .co-step { flex: 1; text-align: center; font-size: .82rem; color: #94a3b8; padding: 8px 4px; border-bottom: 3px solid #e2e8f0; }
.co-steps .co-step.done { color: #059669; border-color: #059669; }
.co-steps .co-step.now { color: #064e3b; font-weight: 700; border-color: #059669; }
.co-item { border: 1px solid #d1fae5; border-radius: 10px; }
.co-total { font-size: 1.4rem; font-weight: 800; color: #059669; }
</style>
<div class="co-steps">
    <div class="co-step done"><i class="fas fa-check mr-1"></i>Keranjang</div>
    <div class="co-step now">Data Pengiriman</div>
    <div class="co-step">Selesai</div>
</div>
<div class="row"><div class="col-md-7"><div class="card">
    <div class="card-header"><h3 class="card-title">Ringkasan Pesanan</h3></div>
    <div class="card-body">
        <?php foreach ($items as $it): ?>
        <div class="co-item p-2 mb-2 d-flex justify-content-between align-items-center flex-wrap">
            <div><strong><?php echo html_escape($it['nama_obat']); ?></strong><br><small class="text-muted"><?php echo $it['jumlah']; ?> &times; Rp <?php echo number_format($it['harga'], 0, ',', '.'); ?></small></div>
            <div class="font-weight-bold">Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?></div>
        </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="text-muted"><?php echo count($items); ?> jenis obat</span>
            <span>Total: <span class="co-total">Rp <?php echo number_format($total, 0, ',', '.'); ?></span></span>
        </div>
    </div>
</div></div>
<div class="col-md-5"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pengiriman &amp; Konfirmasi</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="form-group"><label for="alamat">Alamat Pengiriman <span class="text-danger">*</span></label><textarea id="alamat" name="alamat" class="form-control" rows="4" required><?php echo html_escape(set_value('alamat', $pasien->alamat ?: '')); ?></textarea><small class="text-muted">Alamat tersimpan sebagai snapshot pada pesanan.</small></div>
        <div class="form-group"><label for="no_hp">No. HP <span class="text-danger">*</span></label><input id="no_hp" name="no_hp" class="form-control" required value="<?php echo html_escape(set_value('no_hp', $pasien->no_hp ?: '')); ?>"><small class="text-muted">Untuk kurir menghubungi. Tersimpan ke data pasien.</small></div>
    </div><div class="card-footer"><button type="submit" name="checkout" value="1" class="btn btn-primary btn-block">Buat Pesanan</button><a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>" class="btn btn-default btn-block">Kembali ke Keranjang</a></div><?php echo form_close(); ?>
</div></div></div>
