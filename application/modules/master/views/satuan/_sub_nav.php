<ul class="nav nav-pills">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->uri->segment(3) == '' ? 'active' : '' ?>" href="<?php echo site_url(SITE_AREA .'/master/satuan') ?>">Daftar Satuan</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->uri->segment(3) == 'create' ? 'active' : '' ?>" href="<?php echo site_url(SITE_AREA .'/master/satuan/create') ?>">Tambah Satuan</a>
	</li>
</ul>
