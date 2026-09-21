<style>
/* ponytail: keranjang pasien — item cards + total bar, selaras katalog obat */
.krj-item { border: 1px solid #d1fae5; border-radius: 10px; }
.krj-nama { font-weight: 700; color: #064e3b; }
.krj-harga { font-size: .85rem; color: #64748b; }
.krj-subtotal { font-weight: 800; color: #059669; font-size: 1.1rem; }
.krj-stepper .btn { min-width: 34px; }
.krj-total { font-size: 1.4rem; font-weight: 800; color: #059669; }
</style>
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Keranjang</h3></div>
    <div class="card-body">
        <?php if (empty($items)): ?>
        <p class="text-center text-muted">Keranjang kosong. <a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>">Belanja obat</a></p>
        <?php else: ?>
        <?php foreach ($items as $it): ?>
        <div class="krj-item p-3 mb-2"><div class="row align-items-center">
            <div class="col-12 col-md-5">
                <div class="krj-nama"><?php echo html_escape($it['nama_obat']); ?>
                <?php if ($it['wajib_resep']): ?> <span class="badge badge-warning">RESEP</span><?php endif; ?></div>
                <div class="krj-harga">Rp <?php echo number_format($it['harga'], 0, ',', '.'); ?> / <?php echo html_escape($it['satuan']); ?></div>
            </div>
            <div class="col-6 col-md-3">
                <?php echo form_open($this->uri->uri_string(), array('class' => 'form-inline krj-stepper')); ?>
                    <input type="hidden" name="id_obat" value="<?php echo $it['id_obat']; ?>">
                    <button type="submit" name="aksi" value="kurang" class="btn btn-sm btn-default mr-1" aria-label="Kurangi">-</button>
                    <strong class="mx-2"><?php echo $it['jumlah']; ?></strong>
                    <button type="submit" name="aksi" value="tambah" class="btn btn-sm btn-default ml-1" aria-label="Tambah">+</button>
                <?php echo form_close(); ?>
            </div>
            <div class="col-6 col-md-3 text-right">
                <div class="krj-subtotal">Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?></div>
            </div>
            <div class="col-12 col-md-1 text-right">
                <?php echo form_open($this->uri->uri_string()); ?>
                    <input type="hidden" name="id_obat" value="<?php echo $it['id_obat']; ?>">
                    <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-danger" aria-label="Hapus"><i class="fas fa-trash"></i></button>
                <?php echo form_close(); ?>
            </div>
        </div></div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (! empty($items)): ?><div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
        <div>Total: <span class="krj-total">Rp <?php echo number_format($total, 0, ',', '.'); ?></span></div>
        <a href="<?php echo site_url(SITE_AREA . '/online/checkout'); ?>" class="btn btn-primary mt-2 mt-sm-0">Checkout <i class="fas fa-arrow-right"></i></a>
    </div><?php endif; ?>
</div></div></div>
