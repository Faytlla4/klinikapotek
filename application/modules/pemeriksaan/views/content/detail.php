<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Detail Rekam Medis</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($kunjungan)): ?>
                <h5>Identitas Pasien &amp; Kunjungan</h5>
                <dl class="row">
                    <dt class="col-sm-3">Pasien</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_pasien . ' (' . $kunjungan->no_rm . ')'); ?></dd>
                    <dt class="col-sm-3">Tanggal Lahir / Jenis Kelamin</dt><dd class="col-sm-9"><?php echo html_escape(($kunjungan->tanggal_lahir ?: '-') . ' / ' . ($kunjungan->jenis_kelamin ?: '-')); ?></dd>
                    <dt class="col-sm-3">Kunjungan</dt><dd class="col-sm-9"><?php echo html_escape(($kunjungan->nomor_kunjungan ?? '-') . ' — ' . ($kunjungan->tanggal_kunjungan ?? '-')); ?></dd>
                    <dt class="col-sm-3">Dokter / Poli / Ruangan</dt><dd class="col-sm-9"><?php echo html_escape($kunjungan->nama_dokter . ' / ' . $kunjungan->nama_poli . ' / ' . ($kunjungan->nama_ruangan ?: '-')); ?></dd>
                    <dt class="col-sm-3">Antrian</dt><dd class="col-sm-9"><?php echo html_escape(($kunjungan->nomor_antrian ?: '-') . ' — ' . ($kunjungan->status_antrian ?: '-')); ?></dd>
                </dl><hr>
                <?php endif; ?>
                <dl class="row">
                    <dt class="col-sm-3">Keluhan</dt>
                    <dd class="col-sm-9"><?php echo nl2br(html_escape($pemeriksaan->keluhan ?: '-')); ?></dd>
                    
                    <dt class="col-sm-3">Hasil</dt>
                    <dd class="col-sm-9"><?php echo nl2br(html_escape($pemeriksaan->hasil_pemeriksaan ?: '-')); ?></dd>
                    
                    <dt class="col-sm-3">Catatan</dt>
                    <dd class="col-sm-9"><?php echo nl2br(html_escape($pemeriksaan->catatan_dokter ?: '-')); ?></dd>
                    
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9"><span class="badge badge-info"><?php echo html_escape($pemeriksaan->status); ?></span></dd>
                </dl>
                
                <hr>
                
<?php $this->load->view('transaksi/partials/_nota'); ?>
<div class="nota-screen">
<div class="row">
                    <div class="col-md-6">
                        <h4>Diagnosis</h4>
                        <?php if (empty($pemeriksaan->diagnosis)): ?>
                            <p class="text-muted"><i>Tidak ada diagnosis.</i></p>
                        <?php else: ?>
                            <ul>
                                <?php foreach($pemeriksaan->diagnosis as $d): ?>
                                    <li><?php echo html_escape($d->nama_diagnosis); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ($boleh_edit_rekam_medis): ?>
                        <?php echo form_open($this->uri->uri_string(), array('class' => 'mt-2')); ?>
                            <input type="hidden" name="aksi" value="diagnosis">
                            <div class="input-group input-group-sm"><input name="nama_diagnosis" class="form-control" maxlength="200" required placeholder="Tambah diagnosis"><div class="input-group-append"><button class="btn btn-primary">Simpan</button></div></div>
                            <input name="keterangan" class="form-control form-control-sm mt-1" placeholder="Keterangan (opsional)">
                        <?php echo form_close(); ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Tindakan</h4>
                        <?php if (empty($pemeriksaan->tindakan)): ?>
                            <p class="text-muted"><i>Tidak ada tindakan.</i></p>
                        <?php else: ?>
                            <ul>
                                <?php foreach($pemeriksaan->tindakan as $t): ?>
                                    <li><?php echo html_escape($t->nama_tindakan); ?> — Rp <?php echo number_format((float) $t->biaya, 0, ',', '.'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ($boleh_edit_rekam_medis): ?>
                        <?php echo form_open($this->uri->uri_string(), array('class' => 'mt-2')); ?>
                            <input type="hidden" name="aksi" value="tindakan">
                            <input name="nama_tindakan" class="form-control form-control-sm mb-1" maxlength="200" required placeholder="Nama tindakan">
                            <div class="input-group input-group-sm"><input type="number" min="0" step="any" name="biaya" class="form-control" required placeholder="Biaya"><div class="input-group-append"><button class="btn btn-primary">Simpan</button></div></div>
                            <input name="keterangan" class="form-control form-control-sm mt-1" placeholder="Keterangan (opsional)">
                        <?php echo form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <?php if (!empty($riwayat)): ?>
                <h4>Ringkasan Riwayat Pasien</h4>
                <div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Tanggal</th><th>Keluhan</th><th>Diagnosis</th><th>Tindakan</th><th>Status</th></tr></thead><tbody>
                <?php foreach ($riwayat as $h): ?><tr><td><?php echo html_escape($h->tanggal_pemeriksaan); ?></td><td><?php echo html_escape($h->keluhan ?: '-'); ?></td><td><?php echo html_escape(implode(', ', array_map(function ($x) { return $x->nama_diagnosis; }, $h->diagnosis)) ?: '-'); ?></td><td><?php echo html_escape(implode(', ', array_map(function ($x) { return $x->nama_tindakan; }, $h->tindakan)) ?: '-'); ?></td><td><?php echo html_escape($h->status); ?></td></tr><?php endforeach; ?>
                </tbody></table></div><hr>
                <?php endif; ?>

                <h4>Resep Obat</h4>
                <?php if (!empty($pemeriksaan->resep)): ?>
                    <!-- Tampilkan Detail Resep jika sudah ada -->
                    <?php foreach ($pemeriksaan->resep as $rs): ?>
                        <div class="alert alert-success">
                            <strong>Resep No: <?php echo html_escape($rs->nomor_resep); ?></strong><br>
                            Status: <?php echo html_escape($rs->status); ?><br>
                            Catatan: <?php echo html_escape($rs->catatan ?: '-'); ?>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th>Jumlah</th>
                                        <th>Dosis</th>
                                        <th>Aturan Pakai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($rs->detail)): ?>
                                        <?php foreach ($rs->detail as $rd): ?>
                                            <tr>
                                                <td><?php echo html_escape($rd->nama_obat); ?></td>
                                                <td><?php echo html_escape($rd->jumlah . ' ' . $rd->satuan); ?></td>
                                                <td><?php echo html_escape($rd->dosis ?: '-'); ?></td>
                                                <td><?php echo html_escape($rd->aturan_pakai ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">Tidak ada detail obat.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Form Tambah Resep jika belum ada -->
                    <div id="form-resep-container">
                        <form id="form-buat-resep">
                            <input type="hidden" name="id_pemeriksaan" value="<?php echo $pemeriksaan->id_pemeriksaan; ?>">
                            <input type="hidden" name="id_pasien" value="<?php echo isset($kunjungan) ? $kunjungan->id_pasien : 0; ?>">
                            <input type="hidden" name="id_dokter" value="<?php echo $pemeriksaan->id_dokter; ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="tabel-resep">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 35%;">Obat</th>
                                            <th style="width: 15%;">Jumlah</th>
                                            <th style="width: 20%;">Dosis</th>
                                            <th style="width: 25%;">Aturan Pakai</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="resep-items">
                                        <tr class="resep-row">
                                            <td>
                                                <select name="id_obat[]" class="form-control select2-obat" required>
                                                    <option value="">-- Pilih Obat --</option>
                                                    <?php if(isset($obat_list)): foreach($obat_list as $ob): ?>
                                                        <option value="<?php echo $ob->id_obat; ?>"><?php echo html_escape($ob->nama_obat . ' (' . $ob->satuan . ')'); ?></option>
                                                    <?php endforeach; endif; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="jumlah[]" class="form-control" min="1" required></td>
                                            <td><input type="text" name="dosis[]" class="form-control" placeholder="Cth: 500mg"></td>
                                            <td><input type="text" name="aturan_pakai[]" class="form-control" placeholder="Cth: 3 x 1 sesudah makan"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm btn-hapus-baris"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <button type="button" class="btn btn-success btn-sm mb-3" id="btn-tambah-baris"><i class="fa fa-plus"></i> Tambah Obat</button>
                            
                            <div class="form-group">
                                <label>Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="btn-simpan-resep">
                                <i class="fa fa-paper-plane"></i> Kirim Resep ke Apotek
                            </button>
                        </form>
                        <div id="resep-alert" class="mt-3"></div>
                    </div>
                    
                    <!-- Template Baris Baru (Hidden) -->
                    <table style="display: none;">
                        <tbody id="template-baris-resep">
                            <tr class="resep-row">
                                <td>
                                    <select name="id_obat[]" class="form-control select2-obat-template" required>
                                        <option value="">-- Pilih Obat --</option>
                                        <?php if(isset($obat_list)): foreach($obat_list as $ob): ?>
                                            <option value="<?php echo $ob->id_obat; ?>"><?php echo html_escape($ob->nama_obat . ' (' . $ob->satuan . ')'); ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="jumlah[]" class="form-control" min="1" required></td>
                                <td><input type="text" name="dosis[]" class="form-control" placeholder="Cth: 500mg"></td>
                                <td><input type="text" name="aturan_pakai[]" class="form-control" placeholder="Cth: 3 x 1 sesudah makan"></td>
                                <td><button type="button" class="btn btn-danger btn-sm btn-hapus-baris"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <a href="<?php echo site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3)); ?>" class="btn btn-default">Kembali</a>
                <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
                <?php if ($pemeriksaan->status === 'DIPROSES'): ?>
                    <?php echo form_open(site_url(SITE_AREA . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) . '/selesaikan/' . $pemeriksaan->id_pemeriksaan), array('class' => 'float-right')); ?>
                        <button type="submit" class="btn btn-success" onclick="return confirm('Selesaikan pemeriksaan dan susun tagihan pasien?');">
                            <i class="fas fa-check"></i> Selesaikan Pemeriksaan &amp; Buat Tagihan
                        </button>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<div class="nota-print">
    <div class="nota">
        <div class="nota-head"><h4>Klinik &amp; Apotek</h4><p>Ringkasan Rekam Medis</p></div>
        <?php if (!empty($kunjungan)): ?>
        <div class="nota-row"><span>Pasien</span><span><?php echo html_escape($kunjungan->nama_pasien . ' (' . $kunjungan->no_rm . ')'); ?></span></div>
        <div class="nota-row"><span>Kunjungan</span><span><?php echo html_escape(($kunjungan->nomor_kunjungan ?? '-') . ' • ' . ($kunjungan->tanggal_kunjungan ?? '-')); ?></span></div>
        <div class="nota-row"><span>Dokter / Poli</span><span><?php echo html_escape($kunjungan->nama_dokter . ' / ' . $kunjungan->nama_poli); ?></span></div>
        <div class="nota-sep"></div>
        <?php endif; ?>
        <div class="nota-row"><span>Keluhan</span><span><?php echo html_escape($pemeriksaan->keluhan ?: '-'); ?></span></div>
        <div class="nota-row"><span>Hasil</span><span><?php echo html_escape($pemeriksaan->hasil_pemeriksaan ?: '-'); ?></span></div>
        <?php if (!empty($pemeriksaan->diagnosis)): ?>
        <div class="nota-sep"></div>
        <?php foreach($pemeriksaan->diagnosis as $d): ?>
        <div class="nota-row"><span>Diagnosis</span><span><?php echo html_escape($d->nama_diagnosis); ?></span></div>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($pemeriksaan->tindakan)): ?>
        <div class="nota-sep"></div>
        <?php foreach($pemeriksaan->tindakan as $t): ?>
        <div class="nota-row"><span><?php echo html_escape($t->nama_tindakan); ?></span><span>Rp <?php echo number_format((float) $t->biaya, 0, ',', '.'); ?></span></div>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($pemeriksaan->resep)): ?>
        <div class="nota-sep"></div>
        <?php foreach($pemeriksaan->resep as $rs): ?>
        <div class="nota-row"><span>Resep <?php echo html_escape($rs->nomor_resep); ?></span><span><?php echo html_escape($rs->status); ?></span></div>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="nota-foot">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</div>
    </div>
</div>

<script>
// Script ini dijalankan untuk form resep dinamis
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined') {
        // Init Select2 for existing rows
        if ($.fn.select2) {
            $('.select2-obat').select2({ width: '100%' });
        }
        
        // Add row
        $('#btn-tambah-baris').click(function() {
            var newRow = $('#template-baris-resep').html();
            var $newRow = $(newRow);
            $('#resep-items').append($newRow);
            
            // Init select2 on the new row
            if ($.fn.select2) {
                $newRow.find('.select2-obat-template').removeClass('select2-obat-template').addClass('select2-obat').select2({ width: '100%' });
            }
        });
        
        // Remove row
        $(document).on('click', '.btn-hapus-baris', function() {
            if ($('#resep-items .resep-row').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 obat dalam resep.');
            }
        });
        
        // Form Submit via AJAX
        $('#form-buat-resep').submit(function(e) {
            e.preventDefault();
            
            var btn = $('#btn-simpan-resep');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
            $('#resep-alert').html('');
            
            // Build items array structure for the API
            var formData = $(this).serializeArray();
            var payload = {
                id_pemeriksaan: $('input[name="id_pemeriksaan"]').val(),
                id_pasien: $('input[name="id_pasien"]').val(),
                id_dokter: $('input[name="id_dokter"]').val(),
                catatan: $('textarea[name="catatan"]').val(),
                items: []
            };
            
            // Group the array fields manually
            var itemsMap = [];
            $('.resep-row').each(function(index) {
                var row = $(this);
                var idObat = row.find('select[name="id_obat[]"]').val();
                var jumlah = row.find('input[name="jumlah[]"]').val();
                var dosis = row.find('input[name="dosis[]"]').val();
                var aturan = row.find('input[name="aturan_pakai[]"]').val();
                
                if (idObat && jumlah) {
                    payload.items.push({
                        id_obat: idObat,
                        jumlah: jumlah,
                        dosis: dosis,
                        aturan_pakai: aturan
                    });
                }
            });
            
            // Cek apakah url API menggunakan prefix base_url
            // Gunakan path absolut '/resep/api/buat' atau dari CodeIgniter site_url
            $.ajax({
                url: '<?php echo site_url("resep/api/buat"); ?>',
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#resep-alert').html('<div class="alert alert-success">Resep berhasil dikirim ke apotek! Memuat ulang...</div>');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Kirim Resep ke Apotek');
                        $('#resep-alert').html('<div class="alert alert-danger">' + (response.error || 'Terjadi kesalahan') + '</div>');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Kirim Resep ke Apotek');
                    var err = 'Terjadi kesalahan server.';
                    try {
                        var res = JSON.parse(xhr.responseText);
                        if (res.error) err = res.error;
                    } catch (e) {}
                    $('#resep-alert').html('<div class="alert alert-danger">' + err + '</div>');
                }
            });
        });
    }
});
</script>
