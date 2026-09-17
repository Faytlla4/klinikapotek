<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pesanan <?php echo html_escape($pesanan->nomor_pesanan); ?></h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->tanggal_pesanan); ?></dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge badge-info"><?php echo html_escape($pesanan->status); ?></span></dd>
            <dt class="col-sm-3">Pembayaran</dt><dd class="col-sm-9"><span class="badge <?php echo $pesanan->status_bayar === 'LUNAS' ? 'badge-success' : 'badge-warning'; ?>"><?php echo html_escape($pesanan->status_bayar); ?></span></dd>
            <dt class="col-sm-3">Alamat Kirim</dt><dd class="col-sm-9"><?php echo html_escape($pesanan->alamat_kirim); ?></dd>
            <dt class="col-sm-3">Total</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $pesanan->total, 0, ',', '.'); ?></strong></dd>
        </dl>
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Obat</th><th>Satuan</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($pesanan->items as $it): ?><tr><td><?php echo html_escape($it->nama_obat); ?></td><td><?php echo html_escape($it->satuan); ?></td><td><?php echo (int) $it->jumlah; ?></td><td>Rp <?php echo number_format((float) $it->harga, 0, ',', '.'); ?></td><td>Rp <?php echo number_format((float) $it->subtotal, 0, ',', '.'); ?></td></tr><?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="btn btn-default">Kembali</a>
        <?php if ($pesanan->status === 'MENUNGGU'): echo form_open($this->uri->uri_string(), array('class' => 'd-inline float-right')); ?><button type="submit" name="batalkan" value="1" class="btn btn-danger" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button><?php echo form_close(); endif; ?>
    </div>
</div></div></div>
