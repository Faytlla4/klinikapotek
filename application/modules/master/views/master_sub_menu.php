<ul>
<?php if ($this->auth->has_permission('kelola_master_data')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/pelayanan'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'pelayanan' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Pelayanan</p>
        </a>
    </li>
<?php endif; ?>
<?php if ($this->auth->has_permission('kelola_master_data')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/spesialis'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'spesialis' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Spesialis</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/dokter'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'dokter' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Dokter</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/poli'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'poli' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Poli</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/ruangan'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'ruangan' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Ruangan</p>
        </a>
    </li>
<?php endif; ?>
<?php if ($this->auth->has_permission('kelola_master_data') || $this->auth->has_permission('kelola_stok_obat')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/obat'); ?>" class="nav-link <?php echo $this->uri->segment(3) == 'obat' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Obat</p>
        </a>
    </li>
<?php endif; ?>
</ul>
