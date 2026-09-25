<style>
.dashboard-pelayanan {
    --hijau: #159447;
    --hijau-gelap: #116b38;
    --hijau-muda: #eaf8ef;
    --hijau-border: #9bd3ad;
    --teks: #20352a;
    color: var(--teks);
}

.dashboard-pelayanan .hero {
    background: linear-gradient(135deg, #116b38, #1ca653);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 8px 20px rgba(17, 107, 56, .16);
}

.dashboard-pelayanan .hero h2 {
    margin: 0 0 5px;
    font-size: 1.55rem;
    font-weight: 700;
}

.dashboard-pelayanan .hero p {
    margin: 0;
    opacity: .88;
}

/* =========================================================
   STAT CARD
   ========================================================= */

.dashboard-pelayanan .stat-card {
    background: #fff;
    border: 1px solid var(--hijau-border);
    border-radius: 12px;
    min-height: 120px;
    padding: 18px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.dashboard-pelayanan .stat-card:hover {
    transform: translateY(-2px);
    border-color: var(--hijau);
    box-shadow: 0 8px 18px rgba(17, 107, 56, .10);
}

.dashboard-pelayanan .stat-label {
    color: #668070;
    font-size: .88rem;
    margin-bottom: 6px;
}

.dashboard-pelayanan .stat-value {
    color: var(--hijau-gelap);
    font-size: 1.85rem;
    line-height: 1;
    font-weight: 800;
}

.dashboard-pelayanan .stat-icon {
    position: absolute;
    right: 17px;
    top: 22px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--hijau-muda);
    color: var(--hijau);
    font-size: 1.15rem;
}

/* =========================================================
   QUICK LINK
   ========================================================= */

.dashboard-pelayanan .quick-link {
    display: flex;
    align-items: center;
    color: var(--teks);
    background: #fff;
    border: 1px solid var(--hijau-border);
    border-radius: 10px;
    padding: 13px 14px;
    margin-bottom: 10px;
    transition: all .2s ease;
}

.dashboard-pelayanan .quick-link:hover {
    color: var(--hijau-gelap);
    border-color: var(--hijau);
    background: #f7fcf8;
    text-decoration: none;
}

.dashboard-pelayanan .quick-link i {
    color: var(--hijau);
    width: 27px;
    font-size: 1rem;
}

/* =========================================================
   CONTENT CARD
   ========================================================= */

.dashboard-pelayanan .content-card {
    background: #fff;
    border: 1px solid var(--hijau-border);
    border-radius: 12px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(17, 107, 56, .04);
    transition: border-color .2s ease, box-shadow .2s ease;
}

.dashboard-pelayanan .content-card:hover {
    border-color: var(--hijau);
    box-shadow: 0 5px 14px rgba(17, 107, 56, .07);
}

.dashboard-pelayanan .content-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 17px 20px;
    border-bottom: 1px solid #cce6d4;
    background: #fbfefc;
}

.dashboard-pelayanan .content-card-header h3 {
    color: var(--hijau-gelap);
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0;
}

/* =========================================================
   QUEUE
   ========================================================= */

.dashboard-pelayanan .queue-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 20px;
    border-bottom: 1px solid #edf4ef;
}

.dashboard-pelayanan .queue-item:last-child {
    border-bottom: 0;
}

.dashboard-pelayanan .queue-number {
    color: var(--hijau);
    font-size: 1.35rem;
    font-weight: 800;
    min-width: 65px;
}

.dashboard-pelayanan .queue-name {
    flex: 1;
}

.dashboard-pelayanan .queue-name strong {
    display: block;
    color: #263d2e;
}

.dashboard-pelayanan .queue-name small {
    color: #789080;
}

/* =========================================================
   STATUS
   ========================================================= */

.dashboard-pelayanan .status {
    border-radius: 20px;
    padding: 5px 10px;
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}

.dashboard-pelayanan .status-menunggu {
    color: #8a6100;
    background: #fff5d6;
}

.dashboard-pelayanan .status-proses {
    color: #1267a3;
    background: #e5f3ff;
}

.dashboard-pelayanan .status-selesai {
    color: #14723d;
    background: #e6f7ec;
}

/* =========================================================
   EMPTY STATE
   ========================================================= */

.dashboard-pelayanan .empty-state {
    padding: 35px 20px;
    color: #7d9284;
    text-align: center;
}

.dashboard-pelayanan .empty-state i {
    display: block;
    color: #9acbaa;
    font-size: 2rem;
    margin-bottom: 10px;
}

/* =========================================================
   BUTTON
   ========================================================= */

.dashboard-pelayanan .btn-green {
    background: var(--hijau);
    border-color: var(--hijau);
    color: #fff;
    border-radius: 7px;
}

.dashboard-pelayanan .btn-green:hover {
    background: var(--hijau-gelap);
    border-color: var(--hijau-gelap);
    color: #fff;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 575.98px) {

    .dashboard-pelayanan .hero {
        padding: 20px;
    }

    .dashboard-pelayanan .hero h2 {
        font-size: 1.3rem;
    }

    .dashboard-pelayanan .queue-item {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .dashboard-pelayanan .queue-name {
        min-width: calc(100% - 85px);
    }

    .dashboard-pelayanan .status {
        margin-left: 65px;
    }
}
</style>


<div class="dashboard-pelayanan">

    <div class="hero">
        <h2>Dashboard Pelayanan</h2>
        <p>Ringkasan aktivitas pelayanan klinik hari ini.</p>
    </div>


    <!-- =====================================================
         STATISTIK
         ===================================================== -->

    <div class="row">

        <div class="col-md-3 col-6">
            <div class="stat-card">

                <div class="stat-label">
                    Pasien Aktif
                </div>

                <div class="stat-value">
                    <?php echo (int) $pasien_total; ?>
                </div>

                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>

            </div>
        </div>


        <div class="col-md-3 col-6">
            <div class="stat-card">

                <div class="stat-label">
                    Kunjungan Hari Ini
                </div>

                <div class="stat-value">
                    <?php echo (int) $kunjungan_hari_ini; ?>
                </div>

                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>

            </div>
        </div>


        <div class="col-md-3 col-6">
            <div class="stat-card">

                <div class="stat-label">
                    Antrian Menunggu
                </div>

                <div class="stat-value">
                    <?php echo (int) $antrian_menunggu; ?>
                </div>

                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>

            </div>
        </div>


        <div class="col-md-3 col-6">
            <div class="stat-card">

                <div class="stat-label">
                    Tagihan Belum Lunas
                </div>

                <div class="stat-value">
                    <?php echo (int) $tagihan_belum; ?>
                </div>

                <div class="stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>

            </div>
        </div>

    </div>


    <!-- =====================================================
         CONTENT
         ===================================================== -->

    <div class="row">

        <!-- =================================================
             ANTRIAN
             ================================================= -->

        <div class="col-lg-8">

            <div class="content-card">

                <div class="content-card-header">

                    <h3>
                        <i class="fas fa-list-ol mr-2"></i>
                        Antrian Hari Ini
                    </h3>

                    <a
                        href="<?php echo site_url(SITE_AREA . '/pelayanan/antrian'); ?>"
                        class="btn btn-sm btn-green"
                    >
                        Lihat Semua
                    </a>

                </div>


                <?php if (empty($antrian)): ?>

                    <div class="empty-state">

                        <i class="fas fa-calendar-check"></i>

                        Belum ada antrian hari ini.

                    </div>

                <?php else: ?>


                    <?php foreach ($antrian as $a): ?>

                        <?php

                        $status = strtoupper(trim($a->status));

                        $status_class = 'status-menunggu';

                        if ($status === 'SEDANG_DIPERIKSA') {

                            $status_class = 'status-proses';

                        } elseif ($status === 'SELESAI') {

                            $status_class = 'status-selesai';

                        }

                        ?>


                        <div class="queue-item">

                            <div class="queue-number">
                                <?php echo html_escape($a->nomor_antrian); ?>
                            </div>


                            <div class="queue-name">

                                <strong>
                                    <?php echo html_escape($a->nama_pasien); ?>
                                </strong>

                                <small>

                                    No. RM:
                                    <?php echo html_escape($a->no_rm); ?>


                                    <?php if (!empty($a->nama_poli)): ?>

                                        &middot;

                                        <?php echo html_escape($a->nama_poli); ?>

                                    <?php endif; ?>

                                </small>

                            </div>


                            <span class="status <?php echo $status_class; ?>">

                                <?php echo html_escape($a->status); ?>

                            </span>

                        </div>


                    <?php endforeach; ?>


                <?php endif; ?>

            </div>

        </div>


        <!-- =================================================
             SIDEBAR
             ================================================= -->

        <div class="col-lg-4">


            <!-- =============================================
                 AKSES CEPAT
                 ============================================= -->

            <div class="content-card">

                <div class="content-card-header">

                    <h3>
                        <i class="fas fa-bolt mr-2"></i>
                        Akses Cepat
                    </h3>

                </div>


                <div class="p-3">

                    <a
                        href="<?php echo site_url(SITE_AREA . '/pelayanan/pasien'); ?>"
                        class="quick-link"
                    >

                        <i class="fas fa-user-plus"></i>

                        <span>
                            Pendaftaran Pasien
                        </span>

                    </a>


                    <a
                        href="<?php echo site_url(SITE_AREA . '/pelayanan/kunjungan'); ?>"
                        class="quick-link"
                    >

                        <i class="fas fa-notes-medical"></i>

                        <span>
                            Data Kunjungan
                        </span>

                    </a>


                    <a
                        href="<?php echo site_url(SITE_AREA . '/pelayanan/antrian'); ?>"
                        class="quick-link"
                    >

                        <i class="fas fa-list-ol"></i>

                        <span>
                            Kelola Antrian
                        </span>

                    </a>


                    <a
                        href="<?php echo site_url(SITE_AREA . '/cetak/kunjungan'); ?>"
                        class="quick-link"
                    >

                        <i class="fas fa-print"></i>

                        <span>
                            Laporan Cetak
                        </span>

                    </a>

                </div>

            </div>


            <!-- =============================================
                 STATUS ANTRIAN
                 ============================================= -->

            <div class="content-card">

                <div class="content-card-header">

                    <h3>
                        <i class="fas fa-chart-pie mr-2"></i>
                        Status Antrian
                    </h3>

                </div>


                <div class="p-3">

                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Menunggu
                        </span>

                        <strong class="text-warning">
                            <?php echo (int) $antrian_menunggu; ?>
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Sedang Diperiksa
                        </span>

                        <strong class="text-info">
                            <?php echo (int) $antrian_diproses; ?>
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between">

                        <span>
                            Selesai
                        </span>

                        <strong class="text-success">
                            <?php echo (int) $antrian_selesai; ?>
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>