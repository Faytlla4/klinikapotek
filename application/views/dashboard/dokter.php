<style>
/* Override background area for dashboard content */
.content-wrapper {
    background-color: #F5F7F6 !important;
}

/* Base Dashboard Styles */
.dash-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #155E57;
    padding-bottom: 24px;
}

.dash-title {
    color: #155E57;
    font-weight: 700;
    font-size: 22px;
    letter-spacing: -0.2px;
}

/* Welcome Card */
.dash-welcome-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E4ECEB;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.dash-welcome-text h4 {
    font-size: 18px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 4px;
}

.dash-welcome-text p {
    font-size: 13.5px;
    color: #607D8B;
    margin-bottom: 0;
}

.dash-btn-ganti-dokter {
    background: #EAF5F3;
    color: #087F6C;
    border: 1px solid #C4E5E0;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.15s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
}

.dash-btn-ganti-dokter:hover {
    background: #087F6C;
    color: #FFFFFF;
    border-color: #087F6C;
}

/* Section Title */
.dash-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
}

.dash-section-title i {
    color: #087F6C;
    margin-right: 8px;
    font-size: 16px;
}

/* Summary Cards Styling */
.dash-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E4ECEB;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    padding: 18px 20px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.dash-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}

.dash-op-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-op-info .dash-op-value {
    font-size: 26px;
    font-weight: 700;
    color: #087F6C;
    line-height: 1.2;
}

.dash-op-info .dash-op-label {
    font-size: 13px;
    font-weight: 600;
    color: #607D8B;
    margin-top: 2px;
    margin-bottom: 0;
}

.dash-op-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #EAF5F3;
    color: #087F6C;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.dash-op-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
    padding-top: 10px;
    border-top: 1px solid #F0F4F4;
    font-size: 12px;
    font-weight: 600;
    color: #087F6C;
    text-decoration: none !important;
}

.dash-op-footer:hover {
    color: #055C4E;
}

/* Patient Queue Card Section */
.dash-queue-container {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E4ECEB;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}

.dash-queue-header {
    padding: 16px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid #E4ECEB;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-queue-title {
    font-size: 16px;
    font-weight: 700;
    color: #155E57;
    margin: 0;
    display: flex;
    align-items: center;
}

.dash-queue-title i {
    color: #087F6C;
    margin-right: 10px;
}

.dash-btn-primary-action {
    background: #087F6C;
    color: #FFFFFF !important;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    padding: 7px 14px;
    border-radius: 8px;
    transition: background 0.15s ease;
    border: none;
    display: inline-flex;
    align-items: center;
}

.dash-btn-primary-action:hover {
    background: #055C4E;
}

.dash-queue-body {
    padding: 20px;
}

/* Individual Queue Card */
.patient-queue-card {
    background: #FFFFFF;
    border: 1px solid #E4ECEB;
    border-left: 5px solid #087F6C;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    transition: all 0.15s ease;
}

.patient-queue-card:hover {
    border-color: #D3E4E1;
    border-left-color: #087F6C;
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
}

.queue-info-group {
    display: flex;
    align-items: center;
    gap: 16px;
}

.queue-number-badge {
    font-size: 22px;
    font-weight: 800;
    color: #087F6C;
    background: #EAF5F3;
    padding: 8px 14px;
    border-radius: 8px;
    min-width: 90px;
    text-align: center;
    line-height: 1.1;
    letter-spacing: -0.5px;
}

.queue-patient-detail .patient-name {
    font-size: 15px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 2px;
}

.queue-patient-detail .patient-meta {
    font-size: 13px;
    color: #607D8B;
}

.queue-action-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Status Pastel Badges */
.badge-status {
    font-weight: 700;
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    letter-spacing: 0.2px;
    display: inline-block;
}

.status-menunggu {
    background-color: #FFF3CD;
    color: #856404;
}

.status-dipanggil {
    background-color: #D0E1FD;
    color: #084298;
}

.status-sedang-diperiksa {
    background-color: #D1E7DD;
    color: #0F5132;
}

.status-selesai {
    background-color: #E9ECEF;
    color: #495057;
}

.status-dilewati {
    background-color: #E2E3E5;
    color: #383D41;
}

.status-batal {
    background-color: #F8D7DA;
    color: #842029;
}

/* Button Ubah Status */
.btn-ubah-status {
    background: #FFFFFF;
    color: #087F6C;
    border: 1px solid #087F6C;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.15s ease;
    cursor: pointer;
}

.btn-ubah-status:hover {
    background: #087F6C;
    color: #FFFFFF;
}

/* Empty State */
.dash-empty-state {
    padding: 40px 20px;
    text-align: center;
}

.dash-empty-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #EAF5F3;
    color: #087F6C;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
}

.dash-empty-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #155E57;
    margin-bottom: 2px;
}

.dash-empty-sub {
    font-size: 13px;
    color: #607D8B;
}
</style>

<div class="dash-container">
    <!-- PAGE TITLE -->
    <div class="mb-3">
        <h3 class="dash-title">Dashboard Dokter</h3>
    </div>

    <!-- WELCOME SECTION -->
    <div class="mb-4">
        <div class="dash-welcome-card">
            <div class="dash-welcome-text">
                <h4>Selamat datang, <?php echo html_escape($dokter->nama_dokter); ?></h4>
                <p>Berikut ringkasan aktivitas Anda hari ini.</p>
            </div>
            <div>
                <a href="<?php echo site_url('dokter-bertugas/ganti'); ?>" class="dash-btn-ganti-dokter">
                    <i class="fas fa-sync-alt mr-2"></i> Ganti Dokter
                </a>
            </div>
        </div>
    </div>

    <!-- 4 SUMMARY CARDS -->
    <div class="mb-4">
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $antrian_total; ?></div>
                            <div class="dash-op-label">Antrian Hari Ini</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-list-ol"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="dash-op-footer">
                        <span>Antrian Dokter</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $antrian_menunggu; ?></div>
                            <div class="dash-op-label">Pasien Menunggu</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/content/antrian'); ?>" class="dash-op-footer">
                        <span>Lihat Antrian</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12 mb-3 mb-sm-0">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $periksa_selesai; ?> / <?php echo (int) $periksa_hari_ini; ?></div>
                            <div class="dash-op-label">Pemeriksaan Selesai / Hari Ini</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan'); ?>" class="dash-op-footer">
                        <span>Pemeriksaan</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-card">
                    <div class="dash-op-card">
                        <div class="dash-op-info">
                            <div class="dash-op-value"><?php echo (int) $resep_hari_ini; ?></div>
                            <div class="dash-op-label">Resep Dibuat Hari Ini</div>
                        </div>
                        <div class="dash-op-icon">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                    </div>
                    <a href="<?php echo site_url(SITE_AREA . '/content/resep'); ?>" class="dash-op-footer">
                        <span>Resep</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ANTRIAN HARI INI (PATIENT QUEUE CARDS) -->
    <div class="dash-queue-container">
        <div class="dash-queue-header">
            <h3 class="dash-queue-title">
                <i class="fas fa-user-injured"></i> Antrian Hari Ini<?php echo ! empty($dokter) ? ' — ' . html_escape($dokter->nama_dokter) : ''; ?>
            </h3>
            <a href="<?php echo site_url(SITE_AREA . '/content/pemeriksaan/create'); ?>" class="dash-btn-primary-action">
                <i class="fas fa-plus mr-1.5"></i> Tambah Pemeriksaan
            </a>
        </div>
        <div class="dash-queue-body">
            <?php if (empty($antrian)): ?>
                <div class="dash-empty-state">
                    <div class="dash-empty-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="dash-empty-title">Tidak ada antrean hari ini.</div>
                    <div class="dash-empty-sub">Semua antrean untuk dokter bertugas telah selesai atau belum didaftarkan.</div>
                </div>
            <?php else: ?>
                <?php 
                $status_map = array(
                    'MENUNGGU'        => array('class' => 'status-menunggu', 'label' => 'MENUNGGU'),
                    'DIPANGGIL'        => array('class' => 'status-dipanggil', 'label' => 'DIPANGGIL'),
                    'SEDANG_DIPERIKSA' => array('class' => 'status-sedang-diperiksa', 'label' => 'SEDANG DIPERIKSA'),
                    'SELESAI'          => array('class' => 'status-selesai', 'label' => 'SELESAI'),
                    'DILEWATI'         => array('class' => 'status-dilewati', 'label' => 'DILEWATI'),
                    'BATAL'            => array('class' => 'status-batal', 'label' => 'BATAL'),
                );
                
                $alur_status = array(
                    'MENUNGGU'        => array('DIPANGGIL', 'BATAL'),
                    'DIPANGGIL'        => array('SEDANG_DIPERIKSA', 'DILEWATI', 'BATAL'),
                    'DILEWATI'         => array('DIPANGGIL', 'BATAL'),
                    'SEDANG_DIPERIKSA' => array('SELESAI'),
                    'SELESAI'          => array(),
                    'BATAL'            => array(),
                );
                ?>
                
                <?php foreach ($antrian as $a): ?>
                    <?php 
                    $st_info = isset($status_map[$a->status]) ? $status_map[$a->status] : array('class' => 'status-selesai', 'label' => $a->status);
                    $berikut = isset($alur_status[$a->status]) ? $alur_status[$a->status] : array();
                    ?>
                    <div class="patient-queue-card">
                        <div class="queue-info-group">
                            <div class="queue-number-badge">
                                <?php echo html_escape($a->nomor_antrian); ?>
                            </div>
                            <div class="queue-patient-detail">
                                <div class="patient-name"><?php echo html_escape($a->nama_pasien); ?></div>
                                <div class="patient-meta">
                                    <?php echo html_escape($a->no_rm); ?> &middot; <?php echo html_escape($a->nama_poli); ?>
                                </div>
                            </div>
                        </div>
                        <div class="queue-action-group">
                            <span class="badge-status <?php echo $st_info['class']; ?>">
                                <?php echo html_escape($st_info['label']); ?>
                            </span>
                            
                            <?php if (! empty($berikut)): ?>
                                <button type="button" class="btn-ubah-status btn-aksi-dashboard"
                                        data-id="<?php echo (int) $a->id_antrian; ?>"
                                        data-nama="<?php echo html_escape($a->nama_pasien); ?>"
                                        data-nomor="<?php echo html_escape($a->nomor_antrian); ?>"
                                        data-status="<?php echo html_escape($a->status); ?>">
                                    <i class="fas fa-exchange-alt mr-1"></i> Ubah Status
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Ubah Status Antrian Existing -->
<div class="modal fade" id="modal_status_dash" tabindex="-1" role="dialog" aria-labelledby="modalStatusDashLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: #FAFBFB; border-bottom: 1px solid #E4ECEB;">
                <h5 class="modal-title" id="modalStatusDashLabel" style="color: #155E57; font-weight: 700; font-size: 16px;">
                    <i class="fas fa-exchange-alt mr-1 text-secondary"></i> Ubah Status Antrian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <p class="mb-1" style="color: #607D8B;">Pasien: <strong id="modal_dash_nama_pasien" style="color: #155E57;">-</strong></p>
                <p class="mb-1" style="color: #607D8B;">Nomor Antrian: <strong id="modal_dash_nomor" style="color: #087F6C;">-</strong></p>
                <p class="mb-3" style="color: #607D8B;">Status Saat Ini: <strong id="modal_dash_status_lama" style="color: #155E57;">-</strong></p>
                <hr style="border-top: 1px solid #E4ECEB;">
                <p class="mb-2" style="color: #155E57; font-weight: 600;">Ubah ke:</p>
                <div id="modal_dash_btn_status" class="d-flex flex-wrap" style="gap: 8px;"></div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #E4ECEB; background: #FAFBFB;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctxBase = "<?php echo site_url(SITE_AREA . '/content/antrian'); ?>";
    
    var statusInfo = {
        'MENUNGGU':         { label: 'Menunggu' },
        'DIPANGGIL':        { label: 'Dipanggil' },
        'DILEWATI':         { label: 'Dilewati' },
        'SEDANG_DIPERIKSA': { label: 'Sedang Diperiksa' },
        'SELESAI':          { label: 'Selesai' },
        'BATAL':            { label: 'Batal' }
    };

    var alurStatus = {
        'MENUNGGU':         ['DIPANGGIL', 'BATAL'],
        'DIPANGGIL':        ['SEDANG_DIPERIKSA', 'DILEWATI', 'BATAL'],
        'DILEWATI':         ['DIPANGGIL', 'BATAL'],
        'SEDANG_DIPERIKSA': ['SELESAI'],
        'SELESAI':          [],
        'BATAL':            []
    };

    var btnClass = {
        'DIPANGGIL':        'btn-info',
        'SEDANG_DIPERIKSA': 'btn-success',
        'DILEWATI':         'btn-secondary',
        'SELESAI':          'btn-primary',
        'BATAL':            'btn-danger'
    };

    var btnLabel = {
        'DIPANGGIL':        '<i class="fas fa-bullhorn mr-1"></i> Panggil',
        'SEDANG_DIPERIKSA': '<i class="fas fa-stethoscope mr-1"></i> Mulai Periksa',
        'DILEWATI':         '<i class="fas fa-forward mr-1"></i> Lewati',
        'SELESAI':          '<i class="fas fa-check mr-1"></i> Selesai',
        'BATAL':            '<i class="fas fa-times mr-1"></i> Batalkan'
    };

    $('.btn-aksi-dashboard').on('click', function() {
        var id     = $(this).data('id');
        var nama   = $(this).data('nama');
        var nomor  = $(this).data('nomor');
        var status = $(this).data('status');
        var berikut = alurStatus[status] || [];

        $('#modal_dash_nama_pasien').text(nama);
        $('#modal_dash_nomor').text(nomor);
        $('#modal_dash_status_lama').text((statusInfo[status] || {}).label || status);

        var $btn = $('#modal_dash_btn_status').empty();
        berikut.forEach(function (st) {
            var cls   = btnClass[st] || 'btn-secondary';
            var label = btnLabel[st] || st;
            var onconfirm = st === 'BATAL' ? 'onsubmit="return confirm(\'Yakin ingin membatalkan antrian ini?\')"' : '';
            $btn.append(
                '<form method="post" action="' + ctxBase + '/ubah_status/' + id + '" style="display:inline" ' + onconfirm + '>'
                + '<input type="hidden" name="status" value="' + st + '">'
                + '<button type="submit" class="btn btn-sm ' + cls + '" style="border-radius: 6px; padding: 6px 14px;">' + label + '</button>'
                + '</form>'
            );
        });

        $('#modal_status_dash').modal('show');
    });
});
</script>
