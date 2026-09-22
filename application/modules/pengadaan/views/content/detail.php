<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice mr-1"></i> Informasi Pengadaan</h3>
            </div>
            <div class="card-body">
                <strong>Nomor Pengadaan</strong>
                <p class="text-muted"><?php echo html_escape($pengadaan->nomor_pengadaan); ?></p>
                <hr>
                <strong>Supplier</strong>
                <p class="text-muted">
                    <?php echo html_escape($pengadaan->nama_supplier); ?>
                    <?php if (!empty($pengadaan->no_hp)): ?>
                        (<?php echo html_escape($pengadaan->no_hp); ?>)
                    <?php endif; ?>
                </p>
                <?php if (!empty($pengadaan->alamat)): ?>
                    <p class="text-muted small"><?php echo html_escape($pengadaan->alamat); ?></p>
                <?php endif; ?>
                <hr>
                <strong>Tanggal Pesanan</strong>
                <p class="text-muted"><?php echo date('d-m-Y H:i', strtotime($pengadaan->tanggal_pesanan)); ?></p>
                <hr>
                <strong>Total Pesanan</strong>
                <p class="text-muted">Rp <?php echo number_format($pengadaan->total, 0, ',', '.'); ?></p>
                <hr>
                <strong>Status</strong>
                <p>
                    <?php
                    $status_upper = strtoupper($pengadaan->status);
                    if ($status_upper === 'SELESAI'): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> SELESAI / SUDAH DITERIMA</span>
                    <?php elseif ($status_upper === 'DITERIMA_SEBAGIAN'): ?>
                        <span class="badge badge-warning"><i class="fas fa-hourglass-half mr-1"></i> DITERIMA SEBAGIAN</span>
                    <?php elseif ($status_upper === 'DIBATALKAN'): ?>
                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> DIBATALKAN</span>
                    <?php else: ?>
                        <span class="badge badge-info"><i class="fas fa-clock mr-1"></i> <?php echo html_escape($pengadaan->status); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="card-footer">
                <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan'); ?>" class="btn btn-default btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-block mt-2">
                    <i class="fas fa-print mr-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <?php
        $is_selesai = in_array(strtoupper($pengadaan->status), array('SELESAI', 'DIBATALKAN'));
        ?>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Detail Pesanan & Form Penerimaan Obat</h3>
            </div>
            <?php if (!$is_selesai): ?>
                <?php echo form_open(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan/terima/' . $pengadaan->id_pengadaan); ?>
            <?php endif; ?>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Obat</th>
                            <th class="text-center">Dipesan</th>
                            <th class="text-center">Sudah Diterima</th>
                            <th class="text-center">Sisa Pesanan</th>
                            <th class="text-center" style="width: 360px;">Penerimaan Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $d): ?>
                            <tr>
                                <td>
                                    <strong><?php echo html_escape($d->nama_obat); ?></strong>
                                    <br><small class="text-muted">Kode: <?php echo html_escape($d->kode_obat); ?></small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary" style="font-size: 14px;"><?php echo (int) $d->jumlah_pesan; ?> <?php echo html_escape($d->satuan); ?></span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info" style="font-size: 14px;"><?php echo (int) $d->jumlah_sudah_terima; ?></span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-warning" style="font-size: 14px;"><?php echo (int) $d->sisa_pesanan; ?></span>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($is_selesai || $d->sisa_pesanan <= 0): ?>
                                        <span class="text-muted"><i class="fas fa-check text-success mr-1"></i> Lengkap</span>
                                    <?php else: ?>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="items[<?php echo $d->id_obat; ?>][jumlah_terima]"
                                                   class="form-control text-center"
                                                   value="<?php echo (int) $d->sisa_pesanan; ?>"
                                                   min="0"
                                                   max="<?php echo (int) $d->sisa_pesanan; ?>"
                                                   required>
                                            <input type="hidden" name="items[<?php echo $d->id_obat; ?>][kondisi]" value="Baik">
                                        </div>
                                        <input type="text" name="items[<?php echo $d->id_obat; ?>][nomor_batch]" class="form-control form-control-sm mt-1" placeholder="No. batch (opsional)" maxlength="100">
                                        <div class="input-group input-group-sm mt-1">
                                            <input type="number" name="items[<?php echo $d->id_obat; ?>][masa_simpan]" class="form-control" min="1" max="1200" placeholder="Masa simpan" required>
                                            <select name="items[<?php echo $d->id_obat; ?>][satuan_masa_simpan]" class="form-control" required>
                                                <option value="HARI">Hari</option>
                                                <option value="BULAN">Bulan</option>
                                                <option value="TAHUN">Tahun</option>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted">Tanggal kedaluwarsa dihitung dari tanggal penerimaan.</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!$is_selesai): ?>
                <div class="card-footer text-right">
                    <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/pengadaan'); ?>" class="btn btn-default mr-2">
                        Batal
                    </a>
                    <button type="submit" name="save_terima" value="1" class="btn btn-success btn-lg">
                        <i class="fas fa-check mr-1"></i> Simpan Penerimaan
                    </button>
                </div>
                <?php echo form_close(); ?>
            <?php else: ?>
                <div class="card-footer bg-light">
                    <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Pengadaan ini telah selesai / dibatalkan. Penerimaan obat tidak dapat diproses ulang.</span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($riwayat_penerimaan)): ?>
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Penerimaan</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No. Penerimaan</th>
                                <th>Tanggal</th>
                                <th>Obat</th>
                                <th>Jumlah Terima</th>
                                <th>Kondisi</th><th>Batch</th><th>Kedaluwarsa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat_penerimaan as $r): ?>
                                <tr>
                                    <td><?php echo html_escape($r->nomor_penerimaan); ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($r->tanggal_terima)); ?></td>
                                    <td><?php echo html_escape($r->nama_obat); ?></td>
                                    <td><strong>+<?php echo (int) $r->jumlah_terima; ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtoupper($r->kondisi) === 'BAIK' ? 'success' : 'danger'; ?>">
                                            <?php echo html_escape($r->kondisi); ?>
                                        </span>
                                    </td>
                                    <td><?php echo html_escape($r->nomor_batch ?: '-'); ?></td>
                                    <td><?php echo $r->tanggal_kadaluarsa ? date('d-m-Y', strtotime($r->tanggal_kadaluarsa)) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Bukti Pengadaan Obat</p></div>
        <div class="nota-row"><span>Nomor</span><strong><?php echo html_escape($pengadaan->nomor_pengadaan); ?></strong></div>
        <div class="nota-row"><span>Supplier</span><span><?php echo html_escape($pengadaan->nama_supplier); ?></span></div>
        <div class="nota-row"><span>Tanggal Pesan</span><span><?php echo date('d-m-Y H:i', strtotime($pengadaan->tanggal_pesanan)); ?></span></div>
        <div class="nota-row"><span>Status</span><strong><?php echo html_escape(strtoupper($pengadaan->status)); ?></strong></div>
        <div class="nota-sep"></div>
        <?php foreach ($details as $d): ?>
        <div class="nota-row"><span><?php echo html_escape($d->nama_obat); ?></span><span></span></div>
        <div class="nota-row"><span class="text-muted">Pesan <?php echo (int) $d->jumlah_pesan; ?> <?php echo html_escape($d->satuan); ?> &bull; Terima <?php echo (int) $d->jumlah_sudah_terima; ?></span><span>Sisa <?php echo (int) $d->sisa_pesanan; ?></span></div>
        <?php endforeach; ?>
        <div class="nota-sep"></div>
        <div class="nota-row nota-total"><span>TOTAL</span><span>Rp <?php echo number_format((float) $pengadaan->total, 0, ',', '.'); ?></span></div>
        <?php if (!empty($riwayat_penerimaan)): ?>
        <div class="nota-sep"></div>
        <?php foreach ($riwayat_penerimaan as $r): ?>
        <div class="nota-row"><span><?php echo html_escape($r->nomor_penerimaan . ' • ' . date('d-m-Y', strtotime($r->tanggal_terima))); ?></span><span>+<?php echo (int) $r->jumlah_terima; ?> <?php echo html_escape($r->nama_obat); ?></span></div>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>
