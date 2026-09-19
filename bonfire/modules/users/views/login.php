<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="Logo">
        </div>
        <h2 class="login-title">Klinik & Apotek</h2>
        <p class="login-subtitle">Silakan masuk ke sistem</p>

        <?php echo Template::message(); ?>
        <?php if (!empty($error)): ?>
        <div class="login-alert">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url(LOGIN_URL); ?>" class="login-form">
            <div class="form-group">
                <label for="login">Username</label>
                <input type="text" id="login" name="login" placeholder="Username" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off" required>
            </div>
            <button type="submit" name="log-me-in" value="1" class="btn-login">Masuk</button>
            <div class="login-link">
                <a href="<?php echo site_url(); ?>">Kembali ke Home</a>
            </div>
        </form>
    </div>
</div>
