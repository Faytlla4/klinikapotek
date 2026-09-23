<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  <?php if (!empty($save_error)): ?>
      <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo html_escape($save_error); ?>
      </div>
  <?php endif; ?>

  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-header">
                  <h3 class="card-title">
                      Matriks Permission: <?php echo html_escape($role->nama_role); ?>
                  </h3>

                  <div class="card-tools">
                      <a href="<?php echo site_url('admin/settings/roles'); ?>" class="btn
                      btn-sm btn-default">
                          Kembali
                      </a>
                  </div>
              </div>

              <?php echo form_open('admin/settings/roles/matrix/' . (int)
              $role->id_role); ?>

                  <div class="card-body table-responsive">
                      <?php if (!empty($locked_permission_ids)): ?>
                          <div class="alert alert-info">
                              Permission inti role yang sedang Anda gunakan dikunci agar
                              akses Settings dan Matriks Role tidak terputus setelah
                              disimpan.
                          </div>
                      <?php endif; ?>

                      <table class="table table-bordered table-hover table-striped table-
                      sm">
                          <thead>
                              <tr>
                                  <th style="width: 40px;">
                                      <input type="checkbox" id="cek_semua" title="Pilih
                                      semua">
                                  </th>
                                  <th>Permission</th>
                                  <th>Modul</th>
                              </tr>
                          </thead>

                          <tbody>
                              <?php foreach ($perm_list as $permission): ?>
                                  <?php
                                  $id_permission = (int) $permission->id_permission;

                                  $is_checked = isset($punya[$id_permission]);
                                  $is_locked = in_array($id_permission,
                                  $locked_permission_ids);
                                  ?>

                                  <tr>
                                      <td>
                                          <?php if ($is_locked): ?>
                                              <input
                                                  type="hidden"
                                                  name="perm[]"
                                                  value="<?php echo $id_permission; ?>"
                                              >
                                          <?php endif; ?>

                                          <input
                                              type="checkbox"
                                              name="perm[]"
                                              value="<?php echo $id_permission; ?>"
                                              <?php echo $is_checked || $is_locked ?
                                              'checked' : ''; ?>
                                              <?php echo $is_locked ? 'disabled' : ''; ?>
                                          >
                                      </td>

                                      <td>
                                          <?php echo
                                          html_escape($permission->nama_permission); ?>

                                          <?php if ($is_locked): ?>
                                              <span class="badge badge-info">dikunci</
                                              span>
                                          <?php endif; ?>
                                      </td>

                                      <td>
                                          <?php echo html_escape($permission->modul ?
                                          $permission->modul : '-'); ?>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>

                  <div class="card-footer">
                      <button type="submit" name="save" value="1" class="btn btn-primary">
                          Simpan Matriks
                      </button>
                  </div>

              <?php echo form_close(); ?>
          </div>
      </div>
  </div>

  <script type="text/javascript">
  document.getElementById('cek_semua').addEventListener('change', function () {
      var checked = this.checked;

      document.querySelectorAll(
          'input[type="checkbox"][name="perm[]"]:not(:disabled)'
      ).forEach(function (element) {
          element.checked = checked;
      });
  });
  </script>