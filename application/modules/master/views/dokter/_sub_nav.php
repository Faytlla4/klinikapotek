<?php $areaUrl = SITE_AREA . '/master/dokter'; ?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == '' ? 'primary' : 'default'; ?>">Daftar Dokter</a>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == 'create' ? 'primary' : 'default'; ?>">Tambah Dokter</a>
</div>
