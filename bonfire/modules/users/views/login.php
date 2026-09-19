<div class="login-box">
    <div class="login-card">
        <div class="lc-logo">
            <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="Logo">
        </div>
        <h2 class="lc-title">Klinik &amp; Apotek</h2>
        <p class="lc-sub">Silakan masuk ke sistem</p>

        <?php echo Template::message(); ?>
        <?php if (!empty($error)): ?>
        <div class="lc-alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url(LOGIN_URL); ?>" class="lc-form">
            <div class="lc-field">
                <label for="login">Username</label>
                <input type="text" id="login" name="login" placeholder="Masukkan username" autocomplete="off" required>
            </div>
            <div class="lc-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="off" required>
            </div>
            <button type="submit" name="log-me-in" value="1" class="lc-btn">Masuk</button>
        </form>
        <div class="lc-back">
            <a href="<?php echo site_url(); ?>">Kembali ke Home</a>
        </div>
    </div>
</div>

<script>document.body.classList.add('login-page');</script>
