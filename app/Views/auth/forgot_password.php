<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7C5CFC">
    <title>Lupa Password — Leksika</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<?php $cssSfx = ENVIRONMENT === 'production' ? '.min' : ''; ?>
    <link rel="stylesheet" href="<?= base_url('css/style' . $cssSfx . '.css') ?>?v=<?= filemtime(FCPATH . 'css/style' . $cssSfx . '.css') ?>">
    <style>.login-card .form-group { margin-bottom: 14px; }</style>
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.remove('preload');
        })();
    </script>
</head>
 
<body>
<a class="skip-link" href="#main-content">Langsung ke konten utama</a>

<div class="login-page">
    <main class="login-card" id="main-content">
        <div class="login-header">
            <div class="login-logo brand-icon-gradient">
                <?= render_icon('brand', ['style' => 'width: 24px; height: 24px; color: #fff;']) ?>
            </div>
            <h1>Lupa Password</h1>
            <p class="text-muted text-sm">Masukkan email Anda, kami akan kirim tautan reset password.</p>
        </div>

        <div id="alert-container"></div>

        <form id="forgot-form">
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email <span class="required">*</span></label>
                <input
                    type="email"
                    id="email"
                    class="form-control"
                    placeholder="admin@leksika.com"
                    required
                    autocomplete="email"
                >
            </div>
            <button type="submit" class="btn btn-primary login-submit" id="btn-submit">
                Kirim Tautan Reset
            </button>
        </form>

        <div class="login-footer">
            <p class="text-muted text-sm"><a href="<?= base_url('login') ?>">Kembali ke halaman masuk</a></p>
        </div>
    </main>
</div>

<script>
document.getElementById('forgot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const email = document.getElementById('email').value;
    const container = document.getElementById('alert-container');

    btn.disabled = true;
    btn.innerHTML = 'Mengirim...';

    fetch('<?= base_url('auth/forgot-password') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ email }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            container.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            document.getElementById('email').value = '';
        } else {
            container.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
        btn.disabled = false;
        btn.innerHTML = 'Kirim Tautan Reset';
    })
    .catch(() => {
        container.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan. Silakan coba lagi.</div>`;
        btn.disabled = false;
        btn.innerHTML = 'Kirim Tautan Reset';
    });
});
</script>
</body>
</html>
