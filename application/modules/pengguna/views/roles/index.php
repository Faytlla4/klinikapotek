<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manajemen Roles</h3>

        <div class="card-tools">
            <a href="<?php echo site_url(SITE_AREA . '/settings/roles/create'); ?>"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Role
            </a>
        </div>
    </div>

    <div class="card-body">

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo html_escape($this->session->flashdata('success')); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?php echo html_escape($this->session->flashdata('error')); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Role</th>
                        <th>Jumlah User</th>
                        <th>Jumlah Permission</th>
                        <th width="270">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($role_list)): ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                Belum ada data role.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>

                        <?php foreach ($role_list as $role): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>

                                <td>
                                    <strong>
                                        <?php echo html_escape($role->nama_role); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo (int) $role->jml_user; ?>
                                </td>

                                <td>
                                    <?php echo (int) $role->jml_perm; ?>
                                </td>

                                <td>
                                    <a href="<?php echo site_url(SITE_AREA . '/settings/roles/edit/' . $role->id_role); ?>"
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit Nama
                                    </a>

                                    <a href="<?php echo site_url(SITE_AREA . '/settings/roles/matrix/' . $role->id_role); ?>"
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-key"></i> Permission
                                    </a>

                                    <form method="post"
                                          action="<?php echo site_url(SITE_AREA . '/settings/roles/delete/' . $role->id_role); ?>"
                                          style="display:inline;"
                                          onsubmit="return confirm('Yakin ingin menghapus role ini?');">

                                        <?php echo form_hidden(
                                            $this->security->get_csrf_token_name(),
                                            $this->security->get_csrf_hash()
                                        ); ?>

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>