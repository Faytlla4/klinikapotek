<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Pesanan Online Pasien</h3></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3">
            <label class="mr-2" for="status">Status</label>
            <select id="status" name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">-- Semua --</option>
                <?php foreach (array('MENUNGGU','DIVERIFIKASI','DIPROSES','SIAP','SELESAI','BATAL') as $s): ?><option value="<?php echo $s; ?>"<?php echo $f_status === $s ? ' selected' : ''; ?>><?php echo $s; ?></option><?php endforeach; ?>
            </select>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Nomor</th><th>Tanggal</th><th>Pasien</th><th>Total</th><th>Status</th><th>Bayar</th></tr></thead>
            <tbody>
            <?php if (empty($pesanan_list)): ?><tr><td colspan="6" class="text-center">Tidak ada pesanan.</td></tr>
            <?php else: foreach ($pesanan_list as $p): ?><tr><td><a href="<?php echo site_url(SITE_AREA . '/apotek/pesanan-online/detail/' . $p->id_pesanan); ?>"><?php echo html_escape($p->nomor_pesanan); ?></a></td><td><?php echo html_escape($p->tanggal_pesanan); ?></td><td><?php echo html_escape($p->nama_pasien); ?></td><td>Rp <?php echo number_format((float) $p->total, 0, ',', '.'); ?></td><td><span class="badge badge-info"><?php echo html_escape($p->status); ?></span></td><td><span class="badge <?php echo $p->status_bayar === 'LUNAS' ? 'badge-success' : 'badge-warning'; ?>"><?php echo html_escape($p->status_bayar); ?></span></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
