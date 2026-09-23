<?php
$nama_obat = '-';
foreach ($obat_list as $o) {
    if ((int) $id_obat === (int) $o->id_obat) {
        $nama_obat = $o->nama_obat;
        break;
    }
}
$tot_masuk = 0;
$tot_keluar = 0;
foreach ($riwayat as $r) {
    if ($r->jenis_mutasi === 'MASUK') {
        $tot_masuk += (int) $r->jumlah;
    } else {
        $tot_keluar += (int) $r->jumlah;
    }
}
$base = site_url($this->uri->uri_string());
?>
<?php $this->load->view('transaksi/partials/_nota'); ?>
<style>
.mutasi-filter { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
.mutasi-filter .select2-container { min-width: 220px; }
.mutasi-chip { border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px 14px; background: #fff; height: 100%; }
.mutasi-chip .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; }
.mutasi-chip .val { font-size: 19px; font-weight: 800; margin: 0; font-variant-numeric: tabular-nums; }
.mutasi-chip.masuk .val { color: #059669; }
.mutasi-chip.keluar .val { color: #dc2625; }
</style>
<div class="nota-screen">
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-exchange-alt mr-2 text-success"></i>Obat Masuk/Keluar</h3>
    </div>
    <div class="card-body">
        <form method="get" action="<?php echo $base; ?>" class="mutasi-filter form-inline mb-3">
            <div class="input-group input-group-sm mr-2 mb-1">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-pills"></i></span></div>
                <select id="id_obat" name="id_obat" class="form-control select2" data-placeholder="Pilih Obat..." onchange="this.form.submit()">
                    <option value=""></option>
                    <?php foreach ($obat_list as $o): ?><option value="<?php echo $o->id_obat; ?>"<?php echo (int) $id_obat === (int) $o->id_obat ? ' selected' : ''; ?>><?php echo html_escape($o->nama_obat); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="input-group input-group-sm mr-2 mb-1">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-filter"></i></span></div>
                <select name="jenis" class="form-control" onchange="this.form.submit()">
                    <option value=""<?php echo $f_jenis === '' ? ' selected' : ''; ?>>Semua Jenis</option>
                    <option value="MASUK"<?php echo $f_jenis === 'MASUK' ? ' selected' : ''; ?>>Masuk</option>
                    <option value="KELUAR"<?php echo $f_jenis === 'KELUAR' ? ' selected' : ''; ?>>Keluar</option>
                </select>
            </div>
            <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="dari" class="form-control" style="max-width:160px;" title="Dari tanggal" value="<?php echo html_escape($f_dari); ?>" onchange="this.form.submit()"></div>
            <span class="mr-2 mb-1 text-muted">s/d</span>
            <div class="input-group input-group-sm mr-2 mb-1"><div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div><input type="date" name="sampai" class="form-control" style="max-width:160px;" title="Sampai tanggal" value="<?php echo html_escape($f_sampai); ?>" onchange="this.form.submit()"></div>
            <?php if ($id_obat || $f_jenis !== '' || $f_dari !== '' || $f_sampai !== ''): ?><a href="<?php echo $base; ?>" class="btn btn-sm btn-default mb-1" title="Reset filter">&times;</a><?php endif; ?>
        </form>
        <?php if ($id_obat): ?>
        <div class="row mb-3">
            <div class="col-md-4 mb-2"><div class="mutasi-chip masuk"><div class="lbl">Total Masuk</div><p class="val">+<?php echo number_format($tot_masuk, 0, ',', '.'); ?></p></div></div>
            <div class="col-md-4 mb-2"><div class="mutasi-chip keluar"><div class="lbl">Total Keluar</div><p class="val">−<?php echo number_format($tot_keluar, 0, ',', '.'); ?></p></div></div>
            <div class="col-md-4 mb-2"><div class="mutasi-chip"><div class="lbl">Saldo Periode</div><p class="val"><?php echo ($tot_masuk - $tot_keluar >= 0 ? '+' : '') . number_format($tot_masuk - $tot_keluar, 0, ',', '.'); ?></p></div></div>
        </div>
        <?php endif; ?>
        <div class="table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
            <thead class="thead-light"><tr><th>Tanggal</th><th>Jenis</th><th class="text-center">Jumlah</th><th>Sumber</th><th>Keterangan</th><th style="width:84px;">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($id_obat)): ?><tr><td colspan="6" class="text-center text-muted">Pilih obat untuk melihat riwayat.</td></tr>
            <?php elseif (empty($riwayat)): ?><tr><td colspan="6" class="text-center text-muted">Belum ada mutasi.</td></tr>
            <?php else: foreach ($riwayat as $r): ?><tr><td><?php echo html_escape($r->tanggal); ?></td><td><span class="badge <?php echo $r->jenis_mutasi === 'MASUK' ? 'badge-success' : 'badge-danger'; ?>"><?php echo html_escape($r->jenis_mutasi); ?></span></td><td class="text-center"><?php echo (int) $r->jumlah; ?></td><td><?php echo html_escape($r->sumber ?: '-'); ?></td><td><?php echo html_escape($r->keterangan ?: '-'); ?></td><td class="text-center"><button type="button" class="btn btn-xs btn-primary btn-mutasi-detail" title="Lihat Detail" data-obat="<?php echo html_escape($nama_obat); ?>" data-tanggal="<?php echo html_escape($r->tanggal); ?>" data-jenis="<?php echo html_escape($r->jenis_mutasi); ?>" data-jumlah="<?php echo (int) $r->jumlah; ?>" data-sumber="<?php echo html_escape($r->sumber ?: '-'); ?>" data-referensi="<?php echo html_escape(isset($r->id_referensi) && $r->id_referensi ? '#' . $r->id_referensi : '-'); ?>" data-keterangan="<?php echo html_escape($r->keterangan ?: '-'); ?>"><i class="fas fa-eye"></i></button> <button type="button" class="btn btn-xs btn-secondary btn-mutasi-cetak" title="Cetak Bukti" data-obat="<?php echo html_escape($nama_obat); ?>" data-tanggal="<?php echo html_escape($r->tanggal); ?>" data-jenis="<?php echo html_escape($r->jenis_mutasi); ?>" data-jumlah="<?php echo (int) $r->jumlah; ?>" data-sumber="<?php echo html_escape($r->sumber ?: '-'); ?>" data-referensi="<?php echo html_escape(isset($r->id_referensi) && $r->id_referensi ? '#' . $r->id_referensi : '-'); ?>" data-keterangan="<?php echo html_escape($r->keterangan ?: '-'); ?>"><i class="fas fa-print"></i></button></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div></div></div>
</div>
<div class="modal fade" id="modal-mutasi" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-exchange-alt mr-2 text-success"></i>Detail Mutasi</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body"><dl class="row mb-0">
            <dt class="col-sm-4">Obat</dt><dd class="col-sm-8" id="m-obat">-</dd>
            <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8" id="m-tanggal">-</dd>
            <dt class="col-sm-4">Jenis</dt><dd class="col-sm-8" id="m-jenis">-</dd>
            <dt class="col-sm-4">Jumlah</dt><dd class="col-sm-8" id="m-jumlah">-</dd>
            <dt class="col-sm-4">Sumber</dt><dd class="col-sm-8" id="m-sumber">-</dd>
            <dt class="col-sm-4">Referensi</dt><dd class="col-sm-8" id="m-referensi">-</dd>
            <dt class="col-sm-4">Keterangan</dt><dd class="col-sm-8" id="m-keterangan">-</dd>
        </dl></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button> <button type="button" class="btn btn-secondary" id="btn-mutasi-cetak-modal"><i class="fas fa-print mr-1"></i>Cetak</button></div>
    </div></div>
</div>
<script>
// Inline script view dieksekusi sebelum jQuery dimuat (di akhir body) — tunggu DOM siap.
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') {
        return;
    }
    $(document).on('click', '.btn-mutasi-detail', function () {
        var b = $(this).data();
        window._mutasiTerakhir = b;
        $('#m-obat').text(b.obat);
        $('#m-tanggal').text(b.tanggal);
        $('#m-jenis').html('<span class="badge ' + (b.jenis === 'MASUK' ? 'badge-success' : 'badge-danger') + '">' + b.jenis + '</span>');
        $('#m-jumlah').text(b.jumlah);
        $('#m-sumber').text(b.sumber);
        $('#m-referensi').text(b.referensi);
        $('#m-keterangan').text(b.keterangan);
        $('#modal-mutasi').modal('show');
    });
    function escHtml(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function cetakMutasi(b) {
        var tanda = b.jenis === 'MASUK' ? '+' : '−';
        $('#nota-mutasi-cetak').html(
            '<div class="nota">' +
            '<div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Bukti Mutasi Obat</p></div>' +
            '<div class="nota-row"><span>Obat</span><strong>' + escHtml(b.obat) + '</strong></div>' +
            '<div class="nota-row"><span>Tanggal</span><span>' + escHtml(b.tanggal) + '</span></div>' +
            '<div class="nota-row"><span>Jenis</span><strong>' + escHtml(b.jenis) + '</strong></div>' +
            '<div class="nota-row"><span>Jumlah</span><span>' + tanda + escHtml(b.jumlah) + '</span></div>' +
            '<div class="nota-row"><span>Sumber</span><span>' + escHtml(b.sumber) + '</span></div>' +
            '<div class="nota-row"><span>Referensi</span><span>' + escHtml(b.referensi) + '</span></div>' +
            '<div class="nota-row"><span>Keterangan</span><span>' + escHtml(b.keterangan) + '</span></div>' +
            '<div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>' +
            '</div>'
        );
        window.print();
    }
    $(document).on('click', '.btn-mutasi-cetak', function () {
        cetakMutasi($(this).data());
    });
    $(document).on('click', '#btn-mutasi-cetak-modal', function () {
        if (window._mutasiTerakhir) {
            $('#modal-mutasi').modal('hide');
            setTimeout(function () { cetakMutasi(window._mutasiTerakhir); }, 300);
        }
    });
});
</script>
<div class="nota-print" id="nota-mutasi-cetak"></div>
