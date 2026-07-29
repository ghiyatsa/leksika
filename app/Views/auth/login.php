<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7C5CFC">
    <meta name="description" content="Pengecekan Orisinalitas Judul Skripsi Teknik Informatika Universitas Malikussaleh">
    <meta name="keywords" content="skripsi, kemiripan, plagiarism, universitas malikussaleh, teknik informatika">
    <title>Masuk — Leksika</title>
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
        })();
    </script>
</head>
 
<body>
<a class="skip-link" href="#main-content">Langsung ke konten utama</a>
<div class="login-page">
    <main id="main-content" class="login-card">
        <div class="login-header">
            <div class="login-logo brand-icon-gradient">
                <?= render_icon('graduation-cap', ['style' => 'width: 24px; height: 24px; color: #fff;']) ?>
            </div>
        </div>

        <div id="alert-container">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= render_icon('alert-circle', ['style' => 'width: 18px; height: 18px; color: var(--danger); flex-shrink: 0; transform: translateY(2px);']) ?>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= render_icon('check-circle', ['style' => 'width: 18px; height: 18px; color: var(--success); flex-shrink: 0; transform: translateY(2px);']) ?>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div id="firebase-alerts"></div>

        <form id="login-form">
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
            <div class="form-group">
                <label for="password" class="form-label">Password <span class="required">*</span></label>
                <input
                    type="password"
                    id="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <button type="submit" class="btn btn-primary login-submit" id="btn-login">
                Masuk
            </button>
        </form>

        <div class="divider">
            <span>atau</span>
        </div>

        <button id="btn-google" class="btn btn-secondary btn-block">
            <svg width="18" height="18" viewBox="0 0 48 48" style="margin-right: 8px;">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.42-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.97-5.97z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Masuk dengan Google
        </button>

        <div class="login-footer">
            <p class="text-muted text-sm">Belum punya akun? <a href="<?= base_url('register') ?>">Daftar</a></p>
        </div>
    </main>
</div>

<script src="https://www.gstatic.com/firebasejs/11.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/11.7.1/firebase-auth-compat.js"></script>
<script>
const firebaseConfig = <?= json_encode($firebaseConfig) ?>;
firebase.initializeApp(firebaseConfig);

const alertContainer = document.getElementById('firebase-alerts');
const btnLogin = document.getElementById('btn-login');
const btnGoogle = document.getElementById('btn-google');

function showError(msg) {
    alertContainer.innerHTML = `
        <div class="alert alert-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2" style="flex-shrink: 0; transform: translateY(2px);">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>${msg}</span>
        </div>
    `;
}

function handleToken(idToken) {
    return fetch('<?= base_url('auth/firebaseLogin') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idToken }),
    }).then(r => r.json());
}

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    btnLogin.disabled = true;
    btnLogin.innerHTML = 'Memproses...';

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    firebase.auth().signInWithEmailAndPassword(email, password)
        .then(result => result.user.getIdToken(true))
        .then(handleToken)
        .then(data => {
            if (data.error) {
                showError(data.error);
                btnLogin.disabled = false;
                btnLogin.innerHTML = '<?= addslashes('') ?> Masuk';
                return;
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(err => {
            const code = err.code;
            if (code === 'auth/user-not-found' || code === 'auth/wrong-password' || code === 'auth/invalid-credential') {
                showError('Email atau password salah.');
            } else if (code === 'auth/invalid-email') {
                showError('Format email tidak valid.');
            } else if (code === 'auth/too-many-requests') {
                showError('Terlalu banyak percobaan. Coba lagi nanti.');
            } else {
                showError('Gagal masuk: ' + err.message);
            }
            btnLogin.disabled = false;
            btnLogin.innerHTML = '<?= addslashes('') ?> Masuk';
        });
});

btnGoogle.addEventListener('click', function() {
    btnGoogle.disabled = true;
    btnGoogle.innerHTML = 'Memproses...';

    const provider = new firebase.auth.GoogleAuthProvider();
    firebase.auth().signInWithPopup(provider)
        .then(result => result.user.getIdToken())
        .then(handleToken)
        .then(data => {
            if (data.error) {
                showError(data.error);
                btnGoogle.disabled = false;
                btnGoogle.innerHTML = `Masuk dengan Google`;
                return;
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(err => {
            if (err.code !== 'auth/popup-closed-by-user') {
                showError('Gagal masuk dengan Google: ' + err.message);
            }
            btnGoogle.disabled = false;
            btnGoogle.innerHTML = `Masuk dengan Google`;
        });
});
</script>

</body>
</html>
