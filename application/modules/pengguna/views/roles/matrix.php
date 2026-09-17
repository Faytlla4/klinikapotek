<div class="row"><div class="col-12"><div class="card">
    <div class="card-header"><h3 class="card-title">Matriks Permission: <?php echo html_escape($role->nama_role); ?></h3>
    <div class="card-tools"><a href="<?php echo site_url(SITE_AREA . '/settings/roles'); ?>" class="btn btn-sm btn-default">Kembali</a></div></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body table-responsive"><table class="table table-bordered table-hover table-striped table-sm">
        <thead><tr><th style="width:40px;"><input type="checkbox" id="cek_semua" title="Pilih semua"></th><th>Permission</th><th>Modul</th></tr></thead>
        <tbody>
        <?php foreach ($perm_list as $p): ?><tr><td><input type="checkbox" name="perm[]" value="<?php echo $p->id_permission; ?>"<?php echo isset($punya[(int) $p->id_permission]) ? ' checked' : ''; ?>></td><td><?php echo html_escape($p->nama_permission); ?></td><td><?php echo html_escape($p->modul ?: '-'); ?></td></tr><?php endforeach; ?>
        </tbody>
    </table></div><div class="card-footer"><button type="submit" name="save" value="1" class="btn btn-primary">Simpan Matriks</button></div><?php echo form_close(); ?>
</div></div></div>
<script type="text/javascript">document.getElementById('cek_semua').addEventListener('change', function () { var c = this.checked; document.querySelectorAll('input[name="perm[]"]').forEach(function (el) { el.checked = c; }); });</script>
