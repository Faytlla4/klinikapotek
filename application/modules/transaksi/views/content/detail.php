<div class="row"><div class="col-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Bukti Transaksi</h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nomor Transaksi</dt><dd class="col-sm-9"><?php echo html_escape($transaksi->nomor_transaksi); ?></dd>
            <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo html_escape($transaksi->tanggal_transaksi); ?></dd>
            <dt class="col-sm-3">Total</dt><dd class="col-sm-9"><strong>Rp <?php echo number_format((float) $transaksi->total, 0, ',', '.'); ?></strong></dd>
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <?php
                $cls = $transaksi->status === 'LUNAS' ? 'badge-success' : ($transaksi->status === 'BATAL' ? 'badge-danger' : 'badge-warning');
                ?>
                <span class="badge <?php echo $cls; ?> p-2"><?php echo html_escape($transaksi->status); ?></span>
            </dd>
        </dl>

        <h5 class="mt-3">Rincian Item</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Item</th><th>Jenis</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($transaksi->items as $item): ?>
            <tr>
                <td><?php echo html_escape($item->nama_item); ?></td>
                <td><span class="badge badge-info"><?php echo html_escape($item->jenis_item); ?></span></td>
                <td><?php echo (int) $item->jumlah; ?></td>
                <td>Rp <?php echo number_format((float) $item->harga, 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format((float) $item->subtotal, 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="4" class="text-right">Total</th><th>Rp <?php echo number_format((float) $transaksi->total, 0, ',', '.'); ?></th></tr>
            </tfoot>
        </table>

        <?php if (!empty($transaksi->pembayaran)): ?>
        <h5 class="mt-3">Pembayaran</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Tanggal</th><th>Jumlah Bayar</th><th>Kembalian</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($transaksi->pembayaran as $p): ?>
            <tr>
                <td><?php echo html_escape($p->tanggal_pembayaran); ?></td>
                <td>Rp <?php echo number_format((float) $p->jumlah_bayar, 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format((float) $p->kembalian, 0, ',', '.'); ?></td>
                <td><span class="badge badge-success"><?php echo html_escape($p->status); ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a>
        <button onclick="window.print()" class="btn btn-secondary float-right"><i class="fas fa-print"></i> Cetak</button>
    </div>
</div></div></div>
