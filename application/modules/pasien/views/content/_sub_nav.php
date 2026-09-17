<?php $areaUrl = SITE_AREA . '/content/pasien'; ?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == '' ? 'primary' : 'default'; ?>">Daftar Pasien</a>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == 'create' ? 'primary' : 'default'; ?>">Tambah Pasien</a>
</div>
