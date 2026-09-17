<ul>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/pemeriksaan/pemeriksaan'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pemeriksaan' && in_array($this->uri->segment(4), array('', 'index')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Daftar Pemeriksaan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/pemeriksaan/pemeriksaan/create'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pemeriksaan' && $this->uri->segment(4) == 'create' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Tambah Pemeriksaan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/pemeriksaan/pemeriksaan'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pemeriksaan' && $this->uri->segment(4) == 'detail' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Rekam Medis</p>
        </a>
    </li>
</ul>

