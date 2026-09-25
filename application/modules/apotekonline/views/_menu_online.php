<ul>
<?php
$seg2 = $this->uri->segment(2);
$seg3 = $this->uri->segment(3);
$seg4 = $this->uri->segment(4);
?>

<li class="nav-item">
    <a href="<?php echo site_url('dashboard/pasien'); ?>"
       class="nav-link <?php echo $seg2 === 'dashboard' ? 'active' : ''; ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo site_url(SITE_AREA . '/online/obat'); ?>"
       class="nav-link <?php
           echo $seg2 === 'online'
               && $seg3 === 'obat'
               ? 'active'
               : '';
       ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Daftar Obat</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo site_url(SITE_AREA . '/online/keranjang'); ?>"
       class="nav-link <?php
           echo $seg2 === 'online'
               && in_array($seg3, array('keranjang', 'checkout'), true)
               ? 'active'
               : '';
       ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Keranjang</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo site_url(SITE_AREA . '/online/checkout'); ?>"
       class="nav-link <?php
           echo $seg2 === 'online'
               && $seg3 === 'checkout'
               ? 'active'
               : '';
       ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Checkout</p>
    </a>
</li>

<?php if ($this->auth->has_permission('Pembayaran.Transaksi.View')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan/pembayaran'); ?>"
           class="nav-link <?php
               echo $seg2 === 'transaksi'
                   && $seg3 === 'tagihan'
                   && $seg4 === 'pembayaran'
                   ? 'active'
                   : '';
           ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pembayaran</p>
        </a>
    </li>
<?php endif; ?>

<li class="nav-item">
    <a href="<?php echo site_url(SITE_AREA . '/online/pesanan'); ?>"
       class="nav-link <?php
           echo $seg2 === 'online'
               && $seg3 === 'pesanan'
               ? 'active'
               : '';
       ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Pesanan Saya</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo site_url('dashboard/edit_pribadi'); ?>"
       class="nav-link <?php
           echo $seg2 === 'edit_pribadi'
               ? 'active'
               : '';
       ?>">
        <i class="far fa-circle nav-icon"></i>
        <p>Data Pribadi</p>
    </a>
</li>

</ul>