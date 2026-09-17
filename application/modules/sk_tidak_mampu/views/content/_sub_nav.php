<?php
	$checkSegment = $this->uri->segment(4);
	$areaUrl = SITE_AREA . '/content/sk_tidak_mampu';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" id='list' class="btn btn-flat btn-<?php echo $checkSegment == '' ? 'primary' : 'default'; ?>">
        <?php echo lang('sk_tidak_mampu_list'); ?>
    </a>
    <?php if ($this->auth->has_permission('SK_Tidak_Mampu.Content.Create')): ?>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" id='create_new' class="btn btn-flat btn-<?php echo $checkSegment == 'create' ? 'primary' : 'default'; ?>">
        <?php echo lang('sk_tidak_mampu_new'); ?>
    </a>
    <?php endif;?>
</div>