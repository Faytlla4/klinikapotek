<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Pembayaran — Tagihan Belum Lunas</h3></div>
    <div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped">
        <thead><tr><th>Nomor</th><th>Pasien</th><th>Total</th><th>Sudah Dibayar</th><th>Sisa</th><th>Bayar</th></tr></thead>
        <tbody>
        <?php if (empty($tagihan_list)): ?><tr><td colspan="6" class="text-center">Semua tagihan sudah lunas.</td></tr>
        <?php else: foreach ($tagihan_list as $t): $sisa = (float) $t->total - (float) $t->sudah_dibayar; ?>
        <tr>
            <td><?php echo html_escape($t->nomor_tagihan); ?></td>
            <td><?php echo html_escape(($t->no_rm ? $t->no_rm . ' - ' : '') . $t->nama_pasien); ?></td>
            <td>Rp <?php echo number_format((float) $t->total, 0, ',', '.'); ?></td>
            <td>Rp <?php echo number_format((float) $t->sudah_dibayar, 0, ',', '.'); ?></td>
            <td><strong>Rp <?php echo number_format($sisa, 0, ',', '.'); ?></strong></td>
            <td><?php echo form_open($this->uri->uri_string()); ?><div class="input-group input-group-sm" style="max-width:220px;"><input type="hidden" name="id_tagihan" value="<?php echo $t->id_tagihan; ?>"><input type="number" min="0" step="any" name="jumlah_bayar" class="form-control" value="<?php echo $sisa; ?>" required><div class="input-group-append"><button type="submit" name="bayar" value="1" class="btn btn-success">Bayar</button></div></div><?php echo form_close(); ?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
