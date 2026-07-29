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
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>?v=1.1">
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
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
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

<!-- Chart.js CDN removed — loaded per-page in dashboard/index.php via section('head') -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Theme toggle
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            document.documentElement.classList.add('no-transitions');
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Force a reflow
            window.getComputedStyle(document.documentElement).opacity;
            
            setTimeout(() => {
                document.documentElement.classList.remove('no-transitions');
            }, 0);
        });
    }

    // Live Clock Widget Ticking — skip when hidden (mobile)
    const liveClock = document.getElementById('live-clock');
    if (liveClock) {
        setInterval(() => {
            if (liveClock.offsetParent === null) return; // hidden, skip
            const now = new Date();
            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hrs}.${mins}.${secs}`;
        }, 1000);
    }

    // Mobile sidebar navigation menu toggle
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');

    if (sidebar && sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
    }

    if (sidebar && sidebarClose) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    document.addEventListener('click', (e) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Command Palette Modal Actions
    const cmdPalette = document.getElementById('cmd-palette');
    const cmdInput = document.getElementById('cmd-input');
    const cmdOpenBtn = document.getElementById('cmd-open-btn');
    let cmdOpenerEl = null;

    function togglePalette(show) {
        if (!cmdPalette) return;
        if (show) {
            cmdOpenerEl = document.activeElement;
            cmdPalette.classList.add('active');
            cmdPalette.removeAttribute('hidden');
            if (cmdInput) cmdInput.focus();
        } else {
            cmdPalette.classList.remove('active');
            cmdPalette.setAttribute('hidden', '');
            if (cmdInput) cmdInput.value = '';
            const items = document.querySelectorAll('.cmd-item');
            items.forEach(item => item.style.display = 'flex');
            if (cmdOpenerEl) cmdOpenerEl.focus();
        }
    }

    // Focus trap inside command palette
    if (cmdPalette) {
        cmdPalette.setAttribute('hidden', '');
        cmdPalette.addEventListener('keydown', (e) => {
            if (!cmdPalette.classList.contains('active')) return;
            if (e.key !== 'Tab') return;
            const focusable = Array.from(cmdPalette.querySelectorAll(
                'input, button, a[href], [tabindex]:not([tabindex="-1"])'
            )).filter(el => !el.disabled);
            if (focusable.length === 0) return;
            const first = focusable[0];
            const last  = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });
    }

    if (cmdOpenBtn) {
        cmdOpenBtn.addEventListener('click', (e) => {
            e.preventDefault();
            togglePalette(true);
        });
    }

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (cmdPalette) {
                const isActive = cmdPalette.classList.contains('active');
                togglePalette(!isActive);
            }
        }
        if (e.key === 'Escape') {
            togglePalette(false);
        }
    });

    if (cmdPalette) {
        cmdPalette.addEventListener('click', (e) => {
            if (e.target === cmdPalette) {
                togglePalette(false);
            }
        });
    }

    if (cmdInput) {
        cmdInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.cmd-item');
            items.forEach(item => {
                const textVal = item.querySelector('span').textContent.toLowerCase();
                if (textVal.includes(val)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Global data-confirm pattern — replaces inline onsubmit="return confirm(...)"
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form.hasAttribute('data-confirm')) return;
        e.preventDefault();
        const msg = form.getAttribute('data-confirm');
        if (confirm(msg)) {
            form.removeAttribute('data-confirm');
            form.submit();
        }
    });
});
</script>

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
                <a href="<?= base_url('admin/threshold') ?>" class="cmd-item" role="option">
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

