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
            <td>
                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-pembayaran')); ?>
                <input type="hidden" name="id_tagihan" value="<?php echo $t->id_tagihan; ?>">
                <input type="hidden" class="sisa-tagihan" value="<?php echo $sisa; ?>">
                <div class="input-group input-group-sm" style="max-width:220px;">
                    <input type="number" min="0" step="any" name="jumlah_bayar" class="form-control jumlah-bayar" value="<?php echo $sisa; ?>" required aria-label="Jumlah pembayaran">
                    <div class="input-group-append"><button type="submit" name="bayar" value="1" class="btn btn-success">Bayar</button></div>
                </div>
                <small class="text-success d-none kembalian-preview">Kembalian: <strong>Rp <span>0</span></strong></small>
                <?php echo form_close(); ?>
            </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div>
</div></div></div>
<script>
(function () {
    function formatRupiah(nominal) {
        return Math.round(nominal).toLocaleString('id-ID');
    }

    document.querySelectorAll('.form-pembayaran').forEach(function (form) {
        var input = form.querySelector('.jumlah-bayar');
        var sisa = parseFloat(form.querySelector('.sisa-tagihan').value) || 0;
        var preview = form.querySelector('.kembalian-preview');
        var nilai = preview.querySelector('span');

        input.addEventListener('input', function () {
            var kembalian = (parseFloat(input.value) || 0) - sisa;
            if (kembalian > 0) {
                nilai.textContent = formatRupiah(kembalian);
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }
        });
    });
}());
</script>
