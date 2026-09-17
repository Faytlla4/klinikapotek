<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Obat Masuk/Keluar</h3></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3">
            <label class="mr-2" for="id_obat">Obat</label>
            <select id="id_obat" name="id_obat" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">-- Pilih Obat --</option>
                <?php foreach ($obat_list as $o): ?><option value="<?php echo $o->id_obat; ?>"<?php echo (int) $id_obat === (int) $o->id_obat ? ' selected' : ''; ?>><?php echo html_escape($o->nama_obat); ?></option><?php endforeach; ?>
            </select>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
            <thead><tr><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Sumber</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php if (empty($id_obat)): ?><tr><td colspan="5" class="text-center">Pilih obat untuk melihat riwayat.</td></tr>
            <?php elseif (empty($riwayat)): ?><tr><td colspan="5" class="text-center">Belum ada mutasi.</td></tr>
            <?php else: foreach ($riwayat as $r): ?><tr><td><?php echo html_escape($r->tanggal); ?></td><td><span class="badge <?php echo $r->jenis_mutasi === 'MASUK' ? 'badge-success' : 'badge-danger'; ?>"><?php echo html_escape($r->jenis_mutasi); ?></span></td><td><?php echo (int) $r->jumlah; ?></td><td><?php echo html_escape($r->sumber ?: '-'); ?></td><td><?php echo html_escape($r->keterangan ?: '-'); ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
