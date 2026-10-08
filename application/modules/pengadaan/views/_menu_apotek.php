<?php // Sidebar persediaan apotek: Permintaan, Pengadaan, Penerimaan (via PO), Retur, Stok, Mutasi. ?>
<ul>
<?php $seg2 = $this->uri->segment(2); $seg3 = $this->uri->segment(3); $seg4 = $this->uri->segment(4); ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan/permintaan'); ?>" class="nav-link <?php echo $seg2 == 'apotek' && $seg3 == 'pengadaan' && $seg4 == 'permintaan' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Permintaan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="nav-link <?php echo $seg2 == 'apotek' && $seg3 == 'pengadaan' && in_array($seg4, array('', 'index', 'detail', 'create')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pengadaan / PO</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan/retur'); ?>" class="nav-link <?php echo $seg2 == 'apotek' && $seg3 == 'pengadaan' && in_array($seg4, array('retur', 'retur_detail', 'retur_buat', 'penerimaan_detail')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Retur Pembelian</p>
        </a>
    </li>
</ul>
