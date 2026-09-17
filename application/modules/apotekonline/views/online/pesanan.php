<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Pesanan Saya</h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>" class="btn btn-sm btn-primary">Belanja Obat</a></div></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>Tanggal</th><th>Item</th><th>Total</th><th>Status</th><th>Bayar</th></tr></thead>
        <tbody>
        <?php if (empty($pesanan_list)): ?><tr><td colspan="6" class="text-center">Belum ada pesanan.</td></tr>
        <?php else: foreach ($pesanan_list as $p): ?><tr><td><a href="<?php echo site_url(SITE_AREA . '/online/pesanan/detail/' . $p->id_pesanan); ?>"><?php echo html_escape($p->nomor_pesanan); ?></a></td><td><?php echo html_escape($p->tanggal_pesanan); ?></td><td><?php echo (int) $p->jml_item; ?></td><td>Rp <?php echo number_format((float) $p->total, 0, ',', '.'); ?></td><td><span class="badge badge-info"><?php echo html_escape($p->status); ?></span></td><td><span class="badge <?php echo $p->status_bayar === 'LUNAS' ? 'badge-success' : 'badge-warning'; ?>"><?php echo html_escape($p->status_bayar); ?></span></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
