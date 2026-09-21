<ul>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pengadaan'); ?>" class="nav-link <?php echo $this->uri->segment(2) == 'apotek' && $this->uri->segment(3) == 'pengadaan' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pengadaan Obat</p>
        </a>
    </li>
</ul>
