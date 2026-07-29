<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7C5CFC">
    <meta name="description" content="Pengecekan Orisinalitas Judul Skripsi Teknik Informatika Universitas Malikussaleh">
    <meta name="keywords" content="skripsi, kemiripan, plagiarism, universitas malikussaleh, teknik informatika">
    <title>Daftar — Leksika</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>?v=1.1">
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
            <h1>Leksika</h1>
            <p>Orisinalitas Skripsi</p>
        </div>

        <div id="firebase-alerts"></div>

        <form id="register-form">
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="name" class="form-control" placeholder="Nama Anda" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email <span class="required">*</span></label>
                <input type="email" id="email" class="form-control" placeholder="email@contoh.com" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="password-confirm" class="form-label">Konfirmasi Password <span class="required">*</span></label>
                <input type="password" id="password-confirm" class="form-control" placeholder="Ulangi password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary login-submit" id="btn-register">
                <?= render_icon('user-plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;']) ?> Buat Akun
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
            Daftar dengan Google
        </button>

        <div class="login-footer">
            <p class="text-muted text-sm">Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk</a></p>
        </div>
    </main>
</div>

<script src="https://www.gstatic.com/firebasejs/11.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/11.7.1/firebase-auth-compat.js"></script>
<script>
const firebaseConfig = <?= json_encode($firebaseConfig) ?>;
firebase.initializeApp(firebaseConfig);

const alertContainer = document.getElementById('firebase-alerts');
const btnRegister = document.getElementById('btn-register');
const btnGoogle = document.getElementById('btn-google');

function showSuccess(msg) {
    alertContainer.innerHTML = `
        <div class="alert alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="flex-shrink: 0; transform: translateY(2px);">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>${msg}</span>
        </div>
    `;
}

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

document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    btnRegister.disabled = true;
    btnRegister.innerHTML = 'Memproses...';

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password-confirm').value;

    if (password !== confirm) {
        showError('Konfirmasi password tidak cocok.');
        btnRegister.disabled = false;
        btnRegister.innerHTML = '<?= addslashes(render_icon('user-plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;'])) ?> Buat Akun';
        return;
    }

    firebase.auth().createUserWithEmailAndPassword(email, password)
        .then(result => {
            return result.user.updateProfile({ displayName: name }).then(() => result.user);
        })
        .then(user => user.sendEmailVerification())
        .then(() => {
            showSuccess('Akun berhasil dibuat! Email verifikasi telah dikirim ke <strong>' + escapeHtml(email) + '</strong>. Silakan cek email Anda.');
            document.getElementById('register-form').reset();
            btnRegister.disabled = false;
            btnRegister.innerHTML = '<?= addslashes(render_icon('user-plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;'])) ?> Buat Akun';
        })
        .catch(err => {
            const code = err.code;
            if (code === 'auth/email-already-in-use') {
                showError('Email sudah terdaftar. Gunakan email lain atau <a href="<?= base_url('login') ?>" style="color: var(--accent); font-weight: 600;">masuk</a>.');
            } else if (code === 'auth/weak-password') {
                showError('Password terlalu lemah. Minimal 6 karakter.');
            } else if (code === 'auth/invalid-email') {
                showError('Format email tidak valid.');
            } else {
                showError('Gagal mendaftar: ' + err.message);
            }
            btnRegister.disabled = false;
            btnRegister.innerHTML = '<?= addslashes(render_icon('user-plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;'])) ?> Buat Akun';
        });
});

btnGoogle.addEventListener('click', function() {
    btnGoogle.disabled = true;
    btnGoogle.innerHTML = 'Memproses...';

    const provider = new firebase.auth.GoogleAuthProvider();
    firebase.auth().signInWithPopup(provider)
        .then(result => result.user.getIdToken())
        .then(idToken => {
            return fetch('<?= base_url('auth/firebaseLogin') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ idToken }),
            }).then(r => r.json());
        })
        .then(data => {
            if (data.error) {
                showError(data.error);
                btnGoogle.disabled = false;
                btnGoogle.innerHTML = `Daftar dengan Google`;
                return;
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(err => {
            if (err.code !== 'auth/popup-closed-by-user') {
                showError('Gagal: ' + err.message);
            }
            btnGoogle.disabled = false;
            btnGoogle.innerHTML = `Daftar dengan Google`;
        });
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
</body>
</html>
