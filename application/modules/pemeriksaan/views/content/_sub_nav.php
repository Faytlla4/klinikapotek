<?php $areaUrl = SITE_AREA . '/content/pemeriksaan'; ?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == '' ? 'primary' : 'default'; ?>">Daftar Pemeriksaan</a>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" class="btn btn-flat btn-<?php echo $this->uri->segment(4) == 'create' ? 'primary' : 'default'; ?>">Tambah Pemeriksaan</a>
</div>
