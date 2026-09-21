<ul>
<?php $seg2 = $this->uri->segment(2); ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan'); ?>" class="nav-link <?php echo $seg2 == 'transaksi' && $this->uri->segment(3) == 'tagihan' && in_array($this->uri->segment(4), array('', 'index')) ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Daftar Tagihan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/transaksi/tagihan/pembayaran'); ?>" class="nav-link <?php echo $seg2 == 'transaksi' && $this->uri->segment(3) == 'tagihan' && $this->uri->segment(4) == 'pembayaran' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Pembayaran</p>
        </a>
    </li>
</ul>

