<ul>
<?php $seg2 = $this->uri->segment(2); $seg3 = $this->uri->segment(3); $seg4 = $this->uri->segment(4); ?>
    <li class="nav-item">
        <a href="<?php echo site_url('dashboard/pasien'); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>" class="nav-link <?php echo $seg3 == 'obat' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Daftar Obat</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>" class="nav-link <?php echo $seg3 == 'keranjang' || $seg3 == 'checkout' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Keranjang</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/online/checkout'); ?>" class="nav-link <?php echo $seg3 == 'checkout' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Checkout</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>" class="nav-link <?php echo $seg3 == 'pesanan' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pesanan Saya</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url('dashboard/edit_pribadi'); ?>" class="nav-link <?php echo $seg2 == 'edit_pribadi' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Data Pribadi</p>
        </a>
    </li>
</ul>
