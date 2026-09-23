<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manajemen Pengguna</h3>

            <div class="card-tools">
                <a href="<?php echo site_url(SITE_AREA . '/settings/users/create'); ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        <div class="card-body">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="60">No.</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($user_list)): ?>

                            <?php $no = 1; ?>

                            <?php foreach ($user_list as $user): ?>

                                <tr>
                                    <td><?php echo $no++; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user->nama, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user->status, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user->nama_role ?: '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <a
                                            href="<?php echo site_url(SITE_AREA . '/settings/users/edit/' . (int) $user->id_user); ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            <i class="fa fa-edit"></i> Edit
                                        </a>

                                        <?php if ((int) $user->id_user !== (int) $this->auth->user_id()): ?>

                                            <form
                                                action="<?php echo site_url(SITE_AREA . '/settings/users/delete/' . (int) $user->id_user); ?>"
                                                method="post"
                                                style="display:inline;"
                                                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');"
                                            >

                                                <?php
                                                if (function_exists('csrf_token') && function_exists('csrf_hash')) {
                                                    echo form_hidden(csrf_token(), csrf_hash());
                                                }
                                                ?>

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>

                                            </form>

                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada data pengguna.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>