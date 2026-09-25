<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $backup_success = $this->session->flashdata('backup_success'); ?>
<?php $backup_error = $this->session->flashdata('backup_error'); ?>

<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database mr-2"></i>Backup Database
                </h3>
            </div>

            <div class="card-body">

                <?php if ($backup_success): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="alert"
                        >
                            &times;
                        </button>

                        <?php echo html_escape($backup_success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($backup_error): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="alert"
                        >
                            &times;
                        </button>

                        <?php echo html_escape($backup_error); ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>

                    Backup dibuat dengan
                    <strong>pg_dump</strong>
                    dan dikemas ke ZIP.

                    File disimpan pada server di
                    <code>application/archives/db</code>.
                </div>

                <?php echo form_open('admin/settings/backup/buat'); ?>

                    <button
                        type="submit"
                        class="btn btn-success mb-3"
                    >
                        <i class="fas fa-download mr-1"></i>
                        Buat Backup Sekarang
                    </button>

                <?php echo form_close(); ?>

                <form
                    method="get"
                    action="<?php echo site_url('admin/settings/backup'); ?>"
                    class="form-inline mb-2"
                >
                    <div class="input-group input-group-sm mr-2 mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>

                        <input
                            type="date"
                            name="dari"
                            class="form-control"
                            style="max-width:160px;"
                            value="<?php echo html_escape($f_dari); ?>"
                            onchange="this.form.submit()"
                        >
                    </div>

                    <span class="mr-2 mb-1 text-muted">
                        s/d
                    </span>

                    <div class="input-group input-group-sm mr-2 mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>

                        <input
                            type="date"
                            name="sampai"
                            class="form-control"
                            style="max-width:160px;"
                            value="<?php echo html_escape($f_sampai); ?>"
                            onchange="this.form.submit()"
                        >
                    </div>

                    <?php if ($f_dari !== '' || $f_sampai !== ''): ?>

                        <a
                            href="<?php echo site_url('admin/settings/backup'); ?>"
                            class="btn btn-sm btn-default mb-1"
                        >
                            Reset
                        </a>

                    <?php endif; ?>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal</th>
                                <th style="width:130px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($backup_list)): ?>

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center text-muted"
                                    >
                                        Belum ada backup.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($backup_list as $backup): ?>

                                    <tr>

                                        <td class="text-monospace">
                                            <?php
                                            echo html_escape(
                                                $backup['nama']
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            if ($backup['ukuran'] >= 1048576) {
                                                echo number_format(
                                                    $backup['ukuran'] / 1048576,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) . ' MB';
                                            } else {
                                                echo number_format(
                                                    max(
                                                        1,
                                                        round(
                                                            $backup['ukuran'] / 1024
                                                        )
                                                    ),
                                                    0,
                                                    ',',
                                                    '.'
                                                ) . ' KB';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo html_escape(
                                                $backup['tanggal']
                                            );
                                            ?>
                                        </td>

                                        <td>

                                            <a
                                                href="<?php echo site_url(
                                                    'admin/settings/backup/unduh/'
                                                    . $backup['nama']
                                                ); ?>"
                                                class="btn btn-sm btn-secondary mr-1"
                                                title="Unduh"
                                            >
                                                <i class="fas fa-download"></i>
                                            </a>

                                            <a
                                                href="<?php echo site_url(
                                                    'admin/settings/backup/hapus/'
                                                    . $backup['nama']
                                                ); ?>"
                                                class="btn btn-sm btn-danger"
                                                title="Hapus"
                                                onclick="return confirm('Hapus file backup ini?');"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>