<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7C5CFC">
    <meta name="description" content="Pengecekan kemiripan dan keaslian judul skripsi Teknik Informatika Universitas Malikussaleh secara instan.">
    <meta name="keywords" content="skripsi, kemiripan, plagiarism, universitas malikussaleh, teknik informatika">
    <title>Pengecekan Orisinalitas Skripsi — Leksika</title>
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
</head>

<body class="landing-body">
<a href="#main-content" class="skip-link">Langsung ke konten utama</a>

<?= $this->include('partials/landing_header', ['isLoggedIn' => $isLoggedIn]) ?>

<!-- Hero Section (Asymmetric Modern Minimal Macrostructure) -->
<header class="hero-section" id="main-content">
    <div class="hero-card-banner">
        <div class="hero-content">
            <h1 class="hero-title">Uji orisinalitas judul skripsi secara <em>instan</em>.</h1>
            <p class="hero-desc">
                Leksika menguji kemiripan judul skripsi Teknik Informatika Universitas Malikussaleh menggunakan pencocokan teks hybrid.
            </p>
            <div class="hero-ctas">
                <?php if ($isLoggedIn): ?>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary btn-lg">Buka Dashboard</a>
                    <?php else: ?>
                        <a href="<?= base_url('similarity') ?>" class="btn btn-primary btn-lg">Cek Kemiripan</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= base_url('similarity') ?>" class="btn btn-primary btn-lg">Mulai Sekarang</a>
                <?php endif; ?>
                <a href="#metodologi" class="btn btn-secondary btn-lg">Pelajari Metodologi</a>
            </div>
        </div>

        <!-- Connection Network Graph Visual (as shown in reference screenshots) -->
        <div class="hero-visual">
            <div class="process-simulator">
                <div class="simulator-header">
                    <div class="simulator-dots">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                    </div>
                    <span class="simulator-title">leksika-engine ~ terminal</span>
                </div>
                <div class="simulator-body" id="sim-body"></div>
            </div>
        </div>
    </div>
</header>

<!-- Statistics Row -->
<section class="stats-section" id="tentang">
    <div class="stats-container">
        <div class="stat-cell">
            <span class="stat-num"><?= number_format($totalThesis) ?></span>
            <span class="stat-label">Dataset Judul Skripsi</span>
        </div>
        <div class="stat-cell">
            <span class="stat-num"><?= number_format($totalChecks) ?></span>
            <span class="stat-label">Total Pengecekan Berjalan</span>
        </div>
        <div class="stat-cell">
            <span class="stat-num"><?= number_format($avgHybrid, 1) ?>%</span>
            <span class="stat-label">Rata-rata Skor Hybrid</span>
        </div>
    </div>
</section>

<!-- Methodology Section -->
<section class="methodology-section" id="metodologi">
    <div class="container">
        <h2 class="section-title">Bagaimana sistem <em>menganalisis</em>.</h2>
        
        <div class="methods-grid">
            <div class="method-card">
                <div style="margin-bottom: 12px;"><span class="badge badge-info">Filtering</span></div>
                <h3 class="method-title">TF-IDF Term Weighting</h3>
                <p class="method-desc">
                    Menganalisis kepentingan relatif dari kata kunci dalam judul skripsi terhadap seluruh korpus dataset untuk mengeliminasi kata umum yang kurang bernilai.
                </p>
            </div>
            <div class="method-card">
                <div style="margin-bottom: 12px;"><span class="badge badge-success" style="background: var(--success-bg); color: var(--success);">Semantik</span></div>
                <h3 class="method-title">Cosine Similarity</h3>
                <p class="method-desc">
                    Mengukur kemiripan arah kosinus antar vektor representasi kata. Memberikan bobot <strong>60%</strong> pada skor akhir karena kemampuannya mendeteksi kemiripan semantik.
                </p>
            </div>
            <div class="method-card">
                <div style="margin-bottom: 12px;"><span class="badge badge-warning" style="background: var(--warning-bg); color: var(--warning);">Leksikal</span></div>
                <h3 class="method-title">Jaccard Similarity</h3>
                <p class="method-desc">
                    Menghitung irisan set kata yang digunakan dalam dua judul. Memberikan bobot <strong>40%</strong> untuk menangkap kemiripan persis dari kata-kata yang diajukan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Strip -->
<section class="cta-section">
    <div class="cta-container">
        <h2 class="cta-title">Siap untuk <em>menguji</em> judul skripsi?</h2>
        <div class="cta-action">
            <?php if ($isLoggedIn): ?>
                <?php if (session()->get('role') === 'admin'): ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary btn-lg">Masuk ke Dashboard</a>
                <?php else: ?>
                    <a href="<?= base_url('similarity') ?>" class="btn btn-primary btn-lg">Mulai Pengecekan</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= base_url('similarity') ?>" class="btn btn-primary btn-lg">Mulai Sekarang</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="landing-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <?= render_icon('graduation-cap', ['style' => 'width: 20px; height: 20px; vertical-align: text-bottom; margin-right: 4px;']) ?> leksika
        </div>
        <p class="footer-copyright">
            &copy; <?= date('Y') ?> Universitas Malikussaleh. Dikembangkan oleh Program Studi Teknik Informatika.
        </p>
    </div>
</footer>

<!-- Command Palette Modal -->
<div id="cmd-palette" class="cmd-palette" role="dialog" aria-modal="true" aria-label="Navigasi cepat">
    <div class="cmd-box">
        <div class="cmd-input-wrap">
            <?= render_icon('search', ['style' => 'width: 18px; height: 18px; stroke: var(--text-muted);']) ?>
            <input type="text" id="cmd-input" class="cmd-input" placeholder="Cari navigasi cepat... (Tekan Esc untuk keluar)" autocomplete="off" aria-label="Cari navigasi" />
        </div>
        <div class="cmd-results" id="cmd-results">
            <a href="<?= base_url('similarity') ?>" class="cmd-item">
                <?= render_icon('search', ['style' => 'width: 16px; height: 16px;']) ?>
                <span>Cek Kemiripan Judul Baru</span>
                <span class="cmd-badge">Go to</span>
            </a>
            <?php if ($isLoggedIn && session()->get('role') === 'admin'): ?>
                <a href="<?= base_url('admin/similarity/history') ?>" class="cmd-item">
                    <?= render_icon('file-check', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Lihat Riwayat Pemeriksaan</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/thesis') ?>" class="cmd-item">
                    <?= render_icon('book', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Kelola Data Skripsi</span>
                    <span class="cmd-badge">Admin</span>
                </a>
                <a href="<?= base_url('admin/threshold') ?>" class="cmd-item">
                    <?= render_icon('settings', ['style' => 'width: 16px; height: 16px;']) ?>
                    <span>Pengaturan Sistem</span>
                    <span class="cmd-badge">Admin</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
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



    // Command Palette Modal Actions
    const cmdPalette = document.getElementById('cmd-palette');
    const cmdInput = document.getElementById('cmd-input');
    const cmdOpenBtn = document.getElementById('cmd-open-btn');

    // Simulator logic
    const simBody = document.getElementById('sim-body');
    simBody.setAttribute('aria-live', 'polite');
    if (simBody) {
        const sampleTitles = [
            "Sistem Informasi Pendataan Skripsi Berbasis Web",
            "Analisis Sentimen Ulasan Kuliner dengan Naive Bayes",
            "Penerapan Algoritma K-Means Untuk Pengelompokan Data"
        ];
        let currentTitleIdx = 0;

        function runSimulation() {
            simBody.innerHTML = '';
            const title = sampleTitles[currentTitleIdx];
            
            const logLines = [
                { text: `> Input: "${title}"`, delay: 0, class: 'sim-input' },
                { text: `> Preprocessing: [case_folding, filtering, stemming]`, delay: 1200, class: 'sim-process' },
                { text: `  ↳ Hasil: "${title.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(' ').slice(0, 4).join(' ')}..."`, delay: 1800, class: 'sim-subtext' },
                { text: `> Vektorisasi TF-IDF & Cosine Similarity...`, delay: 2800, class: 'sim-process' },
                { text: `  ↳ Cosine: 64.5% | Jaccard: 50.2%`, delay: 3800, class: 'sim-calc' },
                { text: `[HASIL] Kemiripan: 58.78% (Tinggi — Perlu Revisi)`, delay: 4800, class: 'sim-result-high' }
            ];

            if (currentTitleIdx === 1) {
                logLines[4].text = `  ↳ Cosine: 12.3% | Jaccard: 5.1%`;
                logLines[5].text = `[HASIL] Kemiripan: 9.42% (Sangat Rendah — Lolos)`;
                logLines[5].class = 'sim-result-low';
            }

            currentTitleIdx = (currentTitleIdx + 1) % sampleTitles.length;

            logLines.forEach(line => {
                setTimeout(() => {
                    const p = document.createElement('p');
                    p.className = `sim-line ${line.class || ''}`;
                    p.textContent = line.text;
                    simBody.appendChild(p);
                    simBody.scrollTop = simBody.scrollHeight;
                }, line.delay);
            });

            setTimeout(runSimulation, 8000);
        }
        runSimulation();
    }

    function togglePalette(show) {
        if (show) {
            cmdPalette.classList.add('active');
            cmdInput.focus();
        } else {
            cmdPalette.classList.remove('active');
            cmdInput.value = '';
            // Reset visibility of all quicklinks
            const items = document.querySelectorAll('.cmd-item');
            items.forEach(item => item.style.display = 'flex');
        }
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
            const isActive = cmdPalette.classList.contains('active');
            togglePalette(!isActive);
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
});
</script>
</body>
</html>
