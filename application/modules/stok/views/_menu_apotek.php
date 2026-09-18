<ul>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/stok'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'stok' && in_array($this->uri->segment(4), array('', 'index')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Stok Obat</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/stok/mutasi'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'stok' && $this->uri->segment(4) == 'mutasi' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Obat Masuk/Keluar</p>
        </a>
    </li>
</ul>

