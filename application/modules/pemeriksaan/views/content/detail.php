<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Detail Rekam Medis</h3>
            </div>
            <div class="card-body">
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
                    </div>
                    <div class="col-md-6">
                        <h4>Tindakan</h4>
                        <?php if (empty($pemeriksaan->tindakan)): ?>
                            <p class="text-muted"><i>Tidak ada tindakan.</i></p>
                        <?php else: ?>
                            <ul>
                                <?php foreach($pemeriksaan->tindakan as $t): ?>
                                    <li><?php echo html_escape($t->nama_tindakan); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

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
            </div>
        </div>
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
