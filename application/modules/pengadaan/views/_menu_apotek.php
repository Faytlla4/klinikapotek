<ul>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pengadaan' && in_array($this->uri->segment(4), array('', 'index')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Daftar Pengadaan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan/create'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pengadaan' && $this->uri->segment(4) == 'create' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Tambah Pengadaan</p>
        </a>
    </li>
</ul>

