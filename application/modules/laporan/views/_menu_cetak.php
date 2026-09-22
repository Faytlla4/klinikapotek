<ul>
<?php
$cetak_tabs = array(
    'kunjungan'   => 'Kunjungan',
    'transaksi'   => 'Transaksi',
    'antrian'     => 'Antrian',
    'pendaftaran' => 'Pendaftaran Pasien',
    'mutasi'      => 'Mutasi Obat',
    'backup'      => 'Backup Database',
);
$jenis_aktif = isset($jenis) ? $jenis : '';
foreach ($cetak_tabs as $key => $label): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/cetak/' . $key); ?>" class="nav-link <?php echo $jenis_aktif == $key ? 'active' : ''; ?>">
            <i class="fas fa-print nav-icon"></i>
            <p><?php echo $label; ?></p>
        </a>
    </li>
<?php endforeach; ?>
</ul>
