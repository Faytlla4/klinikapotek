<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Obat</h3></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-inline mb-3">
            <input type="text" name="q" class="form-control mr-2" value="<?php echo html_escape($q); ?>" placeholder="Cari nama/jenis obat">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped">
            <thead><tr><th>Nama Obat</th><th>Jenis</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Jumlah</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($obat_list)): ?><tr><td colspan="7" class="text-center">Obat tidak ditemukan.</td></tr>
            <?php else: foreach ($obat_list as $o): ?>
            <?php $wajib = in_array($o->wajib_resep, array(true, 1, '1', 't', 'T'), true); ?>
            <tr>
                <td><?php echo html_escape($o->nama_obat); ?><?php if ($wajib): ?> <span class="badge badge-warning">RESEP</span><?php endif; ?></td>
                <td><?php echo html_escape($o->jenis_obat ?: '-'); ?></td>
                <td><?php echo html_escape($o->satuan); ?></td>
                <td>Rp <?php echo number_format((float) $o->harga, 0, ',', '.'); ?></td>
                <td><?php echo (int) $o->stok > 0 ? (int) $o->stok : '<span class="badge badge-danger">HABIS</span>'; ?></td>
                <td colspan="2">
                <?php if ((int) $o->stok <= 0): ?><span class="text-muted">Stok habis</span>
                <?php else: echo form_open($this->uri->uri_string()); ?>
                    <div class="form-inline">
                        <input type="hidden" name="id_obat" value="<?php echo $o->id_obat; ?>">
                        <?php if ($wajib): ?>
                        <select name="id_resep" class="form-control form-control-sm mr-2" required>
                            <option value="">-- Resep --</option>
                            <?php foreach (($resep_map[$o->id_obat] ?? array()) as $r): ?><option value="<?php echo $r->id_resep; ?>"><?php echo html_escape($r->nomor_resep . ' (' . $r->jml_resep . ')'); ?></option><?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                        <input type="number" min="1" max="<?php echo (int) $o->stok; ?>" name="jumlah" value="1" class="form-control form-control-sm mr-2" style="width:80px;" required>
                        <button type="submit" name="tambah" value="1" class="btn btn-sm btn-success">+ Keranjang</button>
                    </div>
                <?php echo form_close(); endif; ?>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
        <?php if (! empty($obat_list)): ?><p class="text-muted"><span class="badge badge-warning">RESEP</span> = wajib pilih resep dokter yang valid. Jumlah tidak boleh melebihi stok maupun resep.</p><?php endif; ?>
    </div>
</div></div></div>
