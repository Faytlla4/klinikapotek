<ul>
<?php
$seg2 = $this->uri->segment(2);
$seg3 = $this->uri->segment(3);
$seg4 = $this->uri->segment(4);
?>

<?php if ($this->auth->has_permission('Tagihan.Transaksi.View')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan'); ?>"
           class="nav-link <?php
               echo $seg2 === 'transaksi'
                   && $seg3 === 'tagihan'
                   && in_array($seg4, array('', 'index'), true)
                   ? 'active'
                   : '';
           ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Daftar Tagihan</p>
        </a>
    </li>
<?php endif; ?>

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

</ul>