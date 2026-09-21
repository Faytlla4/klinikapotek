<?php /* Partial tabel laporan cetak; dipakai halaman cetak + export xls. Variabel: $jenis, $rows. */ ?>
<?php if ($jenis === 'kunjungan') : ?>
<table border="1" cellspacing="0" cellpadding="4">
    <thead><tr><th>Tanggal</th><th>No. RM</th><th>Pasien</th><th>Pelayanan</th><th>Poli</th><th>Dokter</th><th>Status</th></tr></thead>
    <tbody>
    <?php if (empty($rows)) : ?><tr><td colspan="7" align="center">Tidak ada data.</td></tr>
    <?php else : foreach ($rows as $r) : ?><tr><td><?php echo html_escape($r->tanggal_kunjungan); ?></td><td><?php echo html_escape($r->no_rm); ?></td><td><?php echo html_escape($r->nama_pasien); ?></td><td><?php echo html_escape($r->nama_pelayanan ?: '-'); ?></td><td><?php echo html_escape($r->nama_poli ?: '-'); ?></td><td><?php echo html_escape($r->nama_dokter ?: '-'); ?></td><td><?php echo html_escape($r->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody>
</table>
<?php elseif ($jenis === 'transaksi') : ?>
<table border="1" cellspacing="0" cellpadding="4">
    <thead><tr><th>Nomor</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php if (empty($rows)) : ?><tr><td colspan="4" align="center">Tidak ada data.</td></tr>
    <?php else : $gt = 0; foreach ($rows as $r) : $gt += (float) $r->total; ?><tr><td><?php echo html_escape($r->nomor_transaksi); ?></td><td><?php echo html_escape($r->tanggal_transaksi); ?></td><td>Rp <?php echo number_format((float) $r->total, 0, ',', '.'); ?></td><td><?php echo html_escape($r->status); ?></td></tr><?php endforeach; ?>
    <tr><th colspan="2" align="right">Grand Total</th><th>Rp <?php echo number_format($gt, 0, ',', '.'); ?></th><th></th></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php elseif ($jenis === 'antrian') : ?>
<table border="1" cellspacing="0" cellpadding="4">
    <thead><tr><th>Nomor</th><th>Tanggal</th><th>Poli</th><th>Pasien</th><th>Status</th></tr></thead>
    <tbody>
    <?php if (empty($rows)) : ?><tr><td colspan="5" align="center">Tidak ada data.</td></tr>
    <?php else : foreach ($rows as $r) : ?><tr><td><?php echo html_escape($r->nomor_antrian); ?></td><td><?php echo html_escape($r->tanggal_antrian); ?></td><td><?php echo html_escape($r->nama_poli ?: '-'); ?></td><td><?php echo html_escape($r->nama_pasien); ?></td><td><?php echo html_escape($r->status); ?></td></tr><?php endforeach; endif; ?>
    </tbody>
</table>
<?php elseif ($jenis === 'pendaftaran') : ?>
<table border="1" cellspacing="0" cellpadding="4">
    <thead><tr><th>No. RM</th><th>Nama</th><th>NIK</th><th>Terdaftar</th></tr></thead>
    <tbody>
    <?php if (empty($rows)) : ?><tr><td colspan="4" align="center">Tidak ada data.</td></tr>
    <?php else : foreach ($rows as $r) : ?><tr><td><?php echo html_escape($r->no_rm); ?></td><td><?php echo html_escape($r->nama); ?></td><td><?php echo html_escape($r->nik ?: '-'); ?></td><td><?php echo html_escape($r->created_at); ?></td></tr><?php endforeach; endif; ?>
    </tbody>
</table>
<?php endif; ?>
