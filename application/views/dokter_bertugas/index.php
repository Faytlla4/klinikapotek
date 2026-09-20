<div class="row justify-content-center"><div class="col-md-6"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pilih Dokter Bertugas</h3></div>
    <?php echo form_open('dokter-bertugas'); ?><div class="card-body"><p>Silakan pilih dokter yang sedang bertugas.</p>
        <div class="form-group"><label for="id_dokter">Dokter</label><select id="id_dokter" name="id_dokter" class="form-control select2" required><option value="">-- Pilih Dokter --</option><?php foreach ($dokter_list as $dokter): ?><option value="<?php echo (int) $dokter->id_dokter; ?>"><?php echo html_escape($dokter->nama_dokter); ?></option><?php endforeach; ?></select></div>
    </div><div class="card-footer"><button name="pilih" value="1" class="btn btn-primary">Lanjutkan</button></div><?php echo form_close(); ?>
</div></div></div>
