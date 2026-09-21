<ul>
<?php
$laporan_tabs = array(
    'kunjungan'      => 'Pelayanan',
    'pendapatan'     => 'Pendapatan',
    'penjualan_obat' => 'Penjualan Obat',
    'mutasi_stok'    => 'Obat Masuk/Keluar',
    'stok'           => 'Stok',
    'resep'          => 'Resep',
);
$report_aktif = isset($report) ? $report : $this->input->get('report');
foreach ($laporan_tabs as $key => $label): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/laporan/laporan?report=' . $key); ?>" class="nav-link <?php echo $report_aktif == $key ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p><?php echo $label; ?></p>
        </a>
    </li>
<?php endforeach; ?>
</ul>
