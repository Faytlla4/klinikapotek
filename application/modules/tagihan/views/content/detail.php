<?php
$status = strtoupper($tagihan->status);
$badge = array('LUNAS' => 'success', 'BELUM_DIBAYAR' => 'warning', 'BATAL' => 'danger');
$badge = isset($badge[$status]) ? $badge[$status] : 'secondary';
$total = (float) $tagihan->total;
$sudah = (float) $tagihan->sudah_dibayar;
$sisa = max(0, (float) $tagihan->sisa);
$persen = $total > 0 ? min(100, round($sudah / $total * 100)) : ($status === 'LUNAS' ? 100 : 0);
$rp = function ($n) { return 'Rp ' . number_format((float) $n, 0, ',', '.'); };
$kembali = site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3));
$no_rm = isset($tagihan->no_rm) && $tagihan->no_rm ? $tagihan->no_rm : '-';
$nama_pasien = isset($tagihan->nama_pasien) && $tagihan->nama_pasien ? $tagihan->nama_pasien : 'Umum';
?>
<?php $this->load->view('transaksi/partials/_nota'); ?>
<style>
.tagihan-nota { border-top: 4px solid #059669; }
.tagihan-nomor { font-family: Consolas, Menlo, monospace; letter-spacing: .03em; }
.tagihan-stat { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; background: #fff; height: 100%; }
.tagihan-stat .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin-bottom: 4px; }
.tagihan-stat .val { font-size: 20px; font-weight: 800; color: #1f2937; margin: 0; font-variant-numeric: tabular-nums; }
.tagihan-stat.sisa .val { color: #b45309; }
.tagihan-stat.lunas .val { color: #059669; }
.tagihan-stat.total { background: #064e3b; border-color: #064e3b; }
.tagihan-stat.total .lbl { color: #a7f3d0; }
.tagihan-stat.total .val { color: #fff; }
.progress.tagihan-progress { height: 10px; border-radius: 6px; background: #e5e7eb; }
.progress.tagihan-progress .progress-bar { background: #059669; border-radius: 6px; }
.table.tagihan-item td, .table.tagihan-item th { vertical-align: middle; }
.table.tagihan-item tfoot th { border-top: 2px solid #064e3b; font-size: 15px; }
</style>
<div class="nota-screen">
<div class="row"><div class="col-12"><div class="card tagihan-nota">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-receipt mr-2 text-success"></i>Detail Tagihan</h3>
        <div class="card-tools tagihan-aksi">
            <?php if ($status === 'BELUM_DIBAYAR') : ?>
            <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan/pembayaran'); ?>" class="btn btn-sm btn-success"><i class="fas fa-cash-register mr-1"></i>Bayar</a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-sm btn-secondary"><i class="fas fa-print mr-1"></i>Cetak</button>
            <a href="<?php echo $kembali; ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div>
                <div class="tagihan-nomor h5 mb-1"><?php echo html_escape($tagihan->nomor_tagihan); ?></div>
                <div class="text-muted small">
                    <i class="far fa-calendar-alt mr-1"></i><?php echo html_escape($tagihan->tanggal_tagihan); ?>
                    <span class="mx-2">|</span><i class="far fa-user mr-1"></i><?php echo html_escape($no_rm . ' — ' . $nama_pasien); ?>
                </div>
            </div>
            <span class="badge badge-<?php echo $badge; ?> px-3 py-2" style="font-size:13px;"><?php echo html_escape($status); ?></span>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 mb-2"><div class="tagihan-stat total"><div class="lbl">Total Tagihan</div><p class="val"><?php echo $rp($total); ?></p></div></div>
            <div class="col-md-4 mb-2"><div class="tagihan-stat"><div class="lbl">Sudah Dibayar</div><p class="val"><?php echo $rp($sudah); ?></p></div></div>
            <div class="col-md-4 mb-2"><div class="tagihan-stat <?php echo $sisa > 0 ? 'sisa' : 'lunas'; ?>"><div class="lbl">Sisa Bayar</div><p class="val"><?php echo $sisa > 0 ? $rp($sisa) : 'Lunas'; ?></p></div></div>
        </div>
        <div class="mb-1 d-flex justify-content-between small text-muted"><span>Progress pembayaran</span><span><?php echo $persen; ?>%</span></div>
        <div class="progress tagihan-progress mb-4"><div class="progress-bar" role="progressbar" style="width: <?php echo $persen; ?>%"></div></div>
        <h6 class="font-weight-bold mb-2">Rincian Item</h6>
        <div class="table-responsive"><table class="table table-bordered tagihan-item">
            <thead class="thead-light"><tr><th style="width:40px;">#</th><th>Item</th><th>Jenis</th><th class="text-center">Jumlah</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>
            <?php $no = 1; foreach ($tagihan->items as $item) : ?>
                <tr><td class="text-muted"><?php echo $no++; ?></td><td><?php echo html_escape($item->nama_item); ?></td><td><span class="badge badge-light border"><?php echo html_escape($item->jenis_item); ?></span></td><td class="text-center"><?php echo html_escape($item->jumlah); ?></td><td class="text-right" style="font-variant-numeric: tabular-nums;"><?php echo $rp($item->subtotal); ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($tagihan->items)) : ?><tr><td colspan="5" class="text-center text-muted">Tidak ada item.</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><th colspan="4" class="text-right">Total</th><th class="text-right"><?php echo $rp($total); ?></th></tr></tfoot>
        </table></div>
    </div>
</div></div></div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Rincian Tagihan</p></div>
        <div class="nota-row"><span>Nomor</span><strong><?php echo html_escape($tagihan->nomor_tagihan); ?></strong></div>
        <div class="nota-row"><span>Tanggal</span><span><?php echo html_escape($tagihan->tanggal_tagihan); ?></span></div>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape($no_rm . ' — ' . $nama_pasien); ?></span></div>
        <div class="nota-row"><span>Status</span><strong><?php echo html_escape($status); ?></strong></div>
        <div class="nota-sep"></div>
        <?php foreach ($tagihan->items as $item) : ?>
        <div class="nota-row"><span><?php echo html_escape($item->nama_item); ?></span><span></span></div>
        <div class="nota-row"><span class="text-muted"><?php echo (int) $item->jumlah; ?> &times; Rp <?php echo number_format((float) $item->subtotal / max(1, (int) $item->jumlah), 0, ',', '.'); ?></span><span>Rp <?php echo number_format((float) $item->subtotal, 0, ',', '.'); ?></span></div>
        <?php endforeach; ?>
        <div class="nota-sep"></div>
        <div class="nota-row nota-total"><span>TOTAL</span><span><?php echo $rp($total); ?></span></div>
        <div class="nota-row"><span>Sudah Dibayar</span><span><?php echo $rp($sudah); ?></span></div>
        <div class="nota-row"><span>Sisa Bayar</span><span><?php echo $rp($sisa); ?></span></div>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>
