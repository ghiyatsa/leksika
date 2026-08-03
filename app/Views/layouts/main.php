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
<?php $cssSfx = file_exists(FCPATH . 'css/style.min.css') ? '.min' : ''; ?>
    <link rel="stylesheet" href="<?= base_url('css/style' . $cssSfx . '.css') ?>?v=<?= filemtime(FCPATH . 'css/style' . $cssSfx . '.css') ?>">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <!-- Chart.js — loaded only by dashboard via section('head') -->
<?= $this->renderSection('head') ?>
</head>

<body>
<a href="#page-body" class="skip-link">Langsung ke konten utama</a>
<div class="app-wrapper">
    <!-- Sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <?= $this->include('partials/topbar') ?>

        <!-- Page Body -->
        <div class="page-body fade-in" id="page-body">
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
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?= render_icon('alert-circle', ['style' => 'width: 18px; height: 18px; color: var(--danger); flex-shrink: 0; transform: translateY(2px);']) ?>
                    <div>
                        <strong>Terdapat kesalahan:</strong>
                        <ul style="margin-top: 6px;">
                            <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<?php $jsSfx = file_exists(FCPATH . 'js/app.min.js') ? '.min' : ''; ?>
<script src="<?= base_url('js/app' . $jsSfx . '.js') ?>?v=<?= filemtime(FCPATH . 'js/app' . $jsSfx . '.js') ?>" defer></script>

<!-- Command Palette Modal -->
<div id="cmd-palette" class="cmd-palette" role="dialog" aria-modal="true" aria-label="Navigasi cepat" hidden>
    <div class="cmd-box">
        <div class="cmd-input-wrap">
            <?= render_icon('search', ['style' => 'width: 18px; height: 18px; stroke: var(--text-muted);']) ?>
            <input type="text" id="cmd-input" class="cmd-input" placeholder="Cari navigasi cepat... (Tekan Esc untuk keluar)" autocomplete="off" aria-label="Cari navigasi" />
        </div>
        <div class="cmd-results" id="cmd-results" role="listbox" aria-label="Hasil navigasi">
            <a href="<?= base_url('similarity') ?>" class="cmd-item" role="option">
                <?= render_icon('search', ['style' => 'width: 16px; height: 16px;']) ?>
                <span>Cek Kemiripan Judul Baru</span>
                <span class="cmd-badge">Go to</span>
            </a>
            <?php if (session()->get('role') === 'admin'): ?>
                <a href="<?= base_url('similarity/history') ?>" class="cmd-item" role="option">
                    <?= render_icon('file-check', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Lihat Riwayat Pemeriksaan</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/dashboard') ?>" class="cmd-item" role="option">
                    <?= render_icon('home', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Dashboard Utama</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/thesis') ?>" class="cmd-item" role="option">
                    <?= render_icon('book', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Kelola Data Skripsi</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/students') ?>" class="cmd-item" role="option">
                    <?= render_icon('user', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Kelola Data Mahasiswa</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/categories') ?>" class="cmd-item" role="option">
                    <?= render_icon('tag', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Kelola Kategori Topik</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/users') ?>" class="cmd-item" role="option">
                    <?= render_icon('users', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Manajemen Akun Pengguna</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/system-settings') ?>" class="cmd-item" role="option">
                    <?= render_icon('settings', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Pengaturan Sistem</span>
                    <span class="cmd-badge">Admin</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->renderSection('scripts') ?>
</body>
</html>

