<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> Profil Saya</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="save" value="1">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" value="<?php echo html_escape($pengguna->username); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo html_escape(set_value('nama', $pengguna->nama)); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin ubah">
                        </div>
                        <div class="form-group">
                            <label for="pass_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="pass_confirm" name="pass_confirm">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
