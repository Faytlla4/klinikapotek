<?php
	$checkSegment = $this->uri->segment(4);
	$areaUrl = SITE_AREA . '/master/spesialis';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" id='list' class="btn btn-flat btn-<?php echo $checkSegment == '' ? 'primary' : 'default'; ?>">
        Daftar Spesialis
    </a>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" id='create_new' class="btn btn-flat btn-<?php echo $checkSegment == 'create' ? 'primary' : 'default'; ?>">
        Tambah Spesialis
    </a>
</div>
