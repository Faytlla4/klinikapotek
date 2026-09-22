<ul>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/pesanan-online'); ?>" class="nav-link <?php echo $this->uri->segment(2) == 'apotek' && $this->uri->segment(3) == 'pesanan-online' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pesanan Online</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/apotek/retur'); ?>" class="nav-link <?php echo $this->uri->segment(2) == 'apotek' && $this->uri->segment(3) == 'retur' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Retur Online</p>
        </a>
    </li>
</ul>
