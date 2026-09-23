 <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  <?php
  $selected_role = set_value(
      'id_role',
      isset($user->id_role) ? $user->id_role : ''
  );

  $selected_status = set_value(
      'status',
      isset($user->status) ? $user->status : 'AKTIF'
  );
  ?>

  <?php if (validation_errors()): ?>
      <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo validation_errors(); ?>
      </div>
  <?php endif; ?>

  <?php if (!empty($save_error)): ?>
      <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo html_escape($save_error); ?>
      </div>
  <?php endif; ?>

  <div class="row">
      <div class="col-md-12">
          <div class="card card-primary">
              <div class="card-header">
                  <h3 class="card-title">
                      Form Edit User: <?php echo html_escape($user->username); ?>
                  </h3>
              </div>

              <?php echo form_open(SITE_AREA . '/settings/users/edit/' . (int)
              $user->id_user); ?>

                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="nama">Nama <span class="text-danger">*</
                                  span></label>
                                  <input
                                      id="nama"
                                      name="nama"
                                      type="text"
                                      class="form-control"
                                      required
                                      maxlength="150"
                                      value="<?php echo html_escape(set_value('nama',
                                      $user->nama)); ?>"
                                  >
                                  <?php echo form_error('nama'); ?>
                              </div>
                          </div>

                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="password">Password Baru</label>
                                  <input
                                      id="password"
                                      name="password"
                                      type="password"
                                      class="form-control"
                                      minlength="6"
                                      autocomplete="new-password"
                                  >
                                  <small class="text-muted">
                                      Kosongkan bila password tidak ingin diubah. Minimal
                                      6 karakter.
                                  </small>
                                  <?php echo form_error('password'); ?>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="id_role">Role <span class="text-danger">*</
                                  span></label>
                                  <select id="id_role" name="id_role" class="form-control
                                  select2" required>
                                      <option value="">-- Pilih Role --</option>

                                      <?php foreach ($role_list as $role): ?>
                                          <option
                                              value="<?php echo (int) $role->id_role; ?>"
                                              <?php echo ((string) $selected_role ===
                                              (string) $role->id_role) ? 'selected' :
                                              ''; ?>
                                          >
                                              <?php echo html_escape($role->nama_role); ?>
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
                                  <?php echo form_error('id_role'); ?>
                              </div>
                          </div>

                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="status">Status <span class="text-danger">*</
                                  span></label>
                                  <select id="status" name="status" class="form-control
                                  select2" required>
                                      <option value="AKTIF" <?php echo $selected_status
                                      === 'AKTIF' ? 'selected' : ''; ?>>
                                          AKTIF
                                      </option>
                                      <option value="NONAKTIF" <?php echo $selected_status
                                      === 'NONAKTIF' ? 'selected' : ''; ?>>
                                          NONAKTIF
                                      </option>
                                  </select>
                                  <?php echo form_error('status'); ?>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="card-footer">
                      <button type="submit" name="save" value="1" class="btn btn-primary">
                          Simpan Perubahan
                      </button>

                      <a href="<?php echo site_url(SITE_AREA . '/settings/users'); ?>"
                      class="btn btn-default float-right">
                          Batal
                      </a>
                  </div>

              <?php echo form_close(); ?>
          </div>
      </div>
  </div>