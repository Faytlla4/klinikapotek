<div class="card">
      <div class="card-header">
          <h3 class="card-title">Edit Nama Role</h3>
      </div>

      <form method="post"
            action="<?php echo site_url(SITE_AREA . '/settings/roles/edit/' . $role->id_role); ?>">

          <div class="card-body">
              <?php echo form_hidden(
                  $this->security->get_csrf_token_name(),
                  $this->security->get_csrf_hash()
              ); ?>

              <div class="form-group">
                  <label for="nama_role">Nama Role</label>

                  <input type="text"
                         id="nama_role"
                         name="nama_role"
                         class="form-control"
                         maxlength="100"
                         required
                         value="<?php echo html_escape($role->nama_role); ?>">

                  <small class="text-muted">
                      Perubahan hanya berlaku pada nama role.
                  </small>
              </div>
          </div>

          <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Simpan
              </button>

              <a href="<?php echo site_url(SITE_AREA . '/settings/roles'); ?>"
                 class="btn btn-secondary">
                  Batal
              </a>
          </div>
      </form>
  </div>