<style>
/* ponytail: katalog obat pasien — flat cards selaras tema emerald */
.obat-grid .obat-card { border: 1px solid #d1fae5; border-radius: 10px; }
.obat-grid .obat-card.obat-habis { background: #f8fafc; }
.obat-grid .obat-nama { font-size: 1.05rem; font-weight: 700; color: #064e3b; margin-bottom: 2px; }
.obat-grid .obat-jenis { font-size: .8rem; color: #64748b; }
.obat-grid .obat-harga { font-size: 1.25rem; font-weight: 800; color: #059669; }
.obat-grid .obat-stok { font-size: .8rem; color: #64748b; }
.obat-resep-note {
    background: #fefce8; border: 1px solid #fde68a; border-left: 4px solid #f59e0b;
    border-radius: 8px; padding: 8px 10px; font-size: .82rem; color: #92400e;
}
.obat-resep-note strong { display: block; margin-bottom: 2px; }
</style>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Obat</h3></div>
    <div class="card-body">
        <form method="get" action="<?php echo site_url($this->uri->uri_string()); ?>" class="mb-3">
            <div class="input-group" style="max-width:420px;">
                <input type="text" name="q" class="form-control" value="<?php echo html_escape($q); ?>" placeholder="Cari nama/jenis obat">
                <div class="input-group-append"><button type="submit" class="btn btn-primary">Cari</button></div>
            </div>
        </form>
        <?php if (empty($obat_list)): ?>
        <p class="text-center text-muted">Obat tidak ditemukan.</p>
        <?php else: ?>
        <div class="row obat-grid">
        <?php foreach ($obat_list as $o): ?>
        <?php
            $wajib = in_array($o->wajib_resep, array(true, 1, '1', 't', 'T'), true);
            $habis = (int) $o->stok <= 0;
            $ada_resep = ! empty($resep_map[$o->id_obat]);
        ?>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-3">
            <div class="card obat-card h-100 <?php echo $habis ? 'obat-habis' : ''; ?>">
                <div class="card-body d-flex flex-column">
                    <div class="obat-nama"><?php echo html_escape($o->nama_obat); ?></div>
                    <div class="obat-jenis mb-2"><?php echo html_escape($o->jenis_obat ?: '-'); ?> &middot; <?php echo html_escape($o->satuan); ?></div>
                    <div class="mb-2">
                        <?php if ($wajib): ?><span class="badge badge-warning">RESEP</span><?php endif; ?>
                        <?php if ($habis): ?><span class="badge badge-danger">HABIS</span><?php endif; ?>
                    </div>
                    <div class="obat-harga">Rp <?php echo number_format((float) $o->harga, 0, ',', '.'); ?></div>
                    <div class="obat-stok mb-3">Stok: <?php echo (int) $o->stok; ?></div>
                    <div class="mt-auto">
                    <?php if ($habis): ?>
                        <span class="text-muted">Stok habis</span>
                    <?php elseif ($wajib && ! $ada_resep): ?>
                        <div class="obat-resep-note"><strong><i class="fas fa-prescription mr-1"></i>Perlu resep dokter</strong>Kamu belum punya resep yang valid untuk obat ini. Periksa ke dokter dulu — resepnya otomatis bisa dipakai di sini.</div>
                    <?php else: echo form_open($this->uri->uri_string()); ?>
                        <input type="hidden" name="id_obat" value="<?php echo $o->id_obat; ?>">
                        <?php if ($wajib): ?>
                        <div class="form-group mb-2">
                            <label class="sr-only" for="resep-<?php echo $o->id_obat; ?>">Resep</label>
                            <select id="resep-<?php echo $o->id_obat; ?>" name="id_resep" class="form-control form-control-sm" required>
                                <option value="">-- Pilih resep --</option>
                                <?php foreach (($resep_map[$o->id_obat] ?? array()) as $r): ?><option value="<?php echo $r->id_resep; ?>"><?php echo html_escape($r->nomor_resep . ' (' . $r->jml_resep . ')'); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="form-inline">
                            <label class="sr-only" for="jml-<?php echo $o->id_obat; ?>">Jumlah</label>
                            <input id="jml-<?php echo $o->id_obat; ?>" type="number" min="1" max="<?php echo (int) $o->stok; ?>" name="jumlah" value="1" class="form-control form-control-sm mr-2" style="width:70px;" required>
                            <button type="submit" name="tambah" value="1" class="btn btn-sm btn-success">+ Keranjang</button>
                        </div>
                    <?php echo form_close(); endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <p class="text-muted"><span class="badge badge-warning">RESEP</span> = wajib pilih resep dokter yang valid. Jumlah tidak boleh melebihi stok maupun resep.</p>
        <?php endif; ?>
    </div>
</div></div></div>
