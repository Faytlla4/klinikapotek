<?php if (validation_errors()): ?><div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo validation_errors(); ?></div><?php endif; ?>
<div class="row"><div class="col-md-12"><div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Form Tambah Kunjungan</h3></div>
    <?php echo form_open($this->uri->uri_string()); ?><div class="card-body">
        <div class="row"><div class="col-md-6"><div class="form-group"><label for="id_pasien">Pasien <span class="text-danger">*</span></label><select id="id_pasien" name="id_pasien" class="form-control select2" required><option value="">-- Pilih Pasien --</option><?php foreach ($pasien_list as $p): ?><option value="<?php echo $p->id_pasien; ?>"><?php echo html_escape($p->no_rm . ' - ' . $p->nama); ?></option><?php endforeach; ?></select><?php echo form_error('id_pasien'); ?></div></div><div class="col-md-6">
        <div class="form-group"><label for="id_pelayanan">Pelayanan <span class="text-danger">*</span></label><select id="id_pelayanan" name="id_pelayanan" class="form-control select2" required><option value="">-- Pilih Pelayanan --</option><?php foreach ($pelayanan_list as $p): ?><option value="<?php echo $p->id_pelayanan; ?>"><?php echo html_escape($p->nama_pelayanan); ?></option><?php endforeach; ?></select><?php echo form_error('id_pelayanan'); ?></div></div></div>
        <div class="row"><div class="col-md-6"><div class="form-group"><label for="id_poli">Poli <span class="text-danger">*</span></label><select id="id_poli" name="id_poli" class="form-control select2" required><option value="">-- Pilih Poli --</option><?php foreach ($poli_list as $p): ?><option value="<?php echo $p->id_poli; ?>"><?php echo html_escape($p->nama_poli); ?></option><?php endforeach; ?></select><?php echo form_error('id_poli'); ?></div></div><div class="col-md-6">
        <div class="form-group"><label for="id_dokter">Dokter <span class="text-danger">*</span></label><select id="id_dokter" name="id_dokter" class="form-control select2" required><option value="">-- Pilih Dokter --</option><?php foreach ($dokter_list as $d): ?><option value="<?php echo $d->id_dokter; ?>"><?php echo html_escape($d->nama_dokter); ?></option><?php endforeach; ?></select><?php echo form_error('id_dokter'); ?></div></div></div>
        <div class="row"><div class="col-md-6"><div class="form-group"><label for="id_ruangan">Ruangan <span class="text-danger">*</span></label><select id="id_ruangan" name="id_ruangan" class="form-control select2" required><option value="">-- Pilih Ruangan --</option><?php foreach ($ruangan_list as $r): ?><option value="<?php echo $r->id_ruangan; ?>"><?php echo html_escape($r->nama_ruangan); ?></option><?php endforeach; ?></select><?php echo form_error('id_ruangan'); ?></div></div></div>
        <div class="alert alert-info">Status awal kunjungan: <strong>TERDAFTAR</strong>. Nomor antrian dibuat saat kunjungan disimpan.</div>
        <div id="riwayat_container" class="d-none mt-3">
            <h5 class="text-primary">Riwayat Kunjungan Terakhir</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelayanan</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div><div class="card-footer"><button type="submit" name="save" class="btn btn-primary">Simpan</button><a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default float-right">Batal</a></div><?php echo form_close(); ?>
</div></div></div>
