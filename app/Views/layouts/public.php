<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7C5CFC">
    <meta name="description" content="Aplikasi Analisis Kemiripan Judul Skripsi Teknik Informatika Universitas Malikussaleh">
    <meta name="keywords" content="skripsi, kemiripan, plagiarism, universitas malikussaleh, teknik informatika">
    <title><?= esc($title ?? 'Pengecekan Kemiripan Skripsi') ?> — Leksika</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<?php $cssSfx = ENVIRONMENT === 'production' ? '.min' : ''; ?>
    <link rel="stylesheet" href="<?= base_url('css/style' . $cssSfx . '.css') ?>?v=<?= filemtime(FCPATH . 'css/style' . $cssSfx . '.css') ?>">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <?= $this->renderSection('head') ?>
</head>
<body>
<div class="public-wrapper">
    <!-- Navbar -->
    <?= $this->include('partials/landing_header') ?>

    <!-- Main Content -->
    <main class="public-main">
        <div class="public-container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= render_icon('check-circle', ['style' => 'width: 18px; height: 18px; color: var(--success); flex-shrink: 0; transform: translateY(2px);']) ?>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= render_icon('alert-circle', ['style' => 'width: 18px; height: 18px; color: var(--danger); flex-shrink: 0; transform: translateY(2px);']) ?>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>
            
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <div class="public-container">
            &copy; <?= date('Y') ?> Universitas Malikussaleh. Dikembangkan oleh Program Studi Teknik Informatika.
        </div>
    </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
