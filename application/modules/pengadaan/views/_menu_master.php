<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<ul>
<?php if ($this->auth->has_permission('Master.Supplier.Manage')): ?>
    <li class="nav-item">
        <a href="<?php echo site_url(SITE_AREA . '/master/pengadaan/supplier'); ?>" class="nav-link <?php echo $this->uri->segment(2) == 'master' && $this->uri->segment(3) == 'pengadaan' && $this->uri->segment(4) == 'supplier' ? 'active' : ''; ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Master Supplier</p>
        </a>
    </li>
<?php endif; ?>
</ul>