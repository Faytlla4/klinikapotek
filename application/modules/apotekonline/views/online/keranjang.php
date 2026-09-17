<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Keranjang</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nama Obat</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($items)): ?><tr><td colspan="5" class="text-center">Keranjang kosong. <a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>">Belanja obat</a></td></tr>
        <?php else: foreach ($items as $it): ?>
        <tr>
            <td><?php echo html_escape($it['nama_obat']); ?><?php if ($it['wajib_resep']): ?> <span class="badge badge-warning">RESEP</span><?php endif; ?></td>
            <td>Rp <?php echo number_format($it['harga'], 0, ',', '.'); ?></td>
            <td>
                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-inline')); ?>
                    <input type="hidden" name="id_obat" value="<?php echo $it['id_obat']; ?>">
                    <button type="submit" name="aksi" value="kurang" class="btn btn-sm btn-default mr-1">-</button>
                    <strong class="mx-2"><?php echo $it['jumlah']; ?></strong>
                    <button type="submit" name="aksi" value="tambah" class="btn btn-sm btn-default ml-1">+</button>
                <?php echo form_close(); ?>
            </td>
            <td>Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?></td>
            <td>
                <?php echo form_open($this->uri->uri_string()); ?>
                    <input type="hidden" name="id_obat" value="<?php echo $it['id_obat']; ?>">
                    <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-danger">Hapus</button>
                <?php echo form_close(); ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr><th colspan="3" class="text-right">Total</th><th colspan="2">Rp <?php echo number_format($total, 0, ',', '.'); ?></th></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <?php if (! empty($items)): ?><div class="card-footer"><a href="<?php echo site_url(SITE_AREA . '/online/checkout'); ?>" class="btn btn-primary">Checkout</a></div><?php endif; ?>
</div></div></div>
