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
<?php $cssSfx = ENVIRONMENT === 'production' ? '.min' : ''; ?>
    <link rel="stylesheet" href="<?= base_url('css/style' . $cssSfx . '.css') ?>?v=<?= filemtime(FCPATH . 'css/style' . $cssSfx . '.css') ?>">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.remove('preload');
        })();
    </script>
</head>

<body class="landing-body">
<a href="#main-content" class="skip-link">Langsung ke konten utama</a>

<?= $this->include('partials/landing_header', ['isLoggedIn' => $isLoggedIn]) ?>

<main id="main-content">
<!-- Hero Section -->
<header class="hero-section">
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
                <div style="margin-bottom: 12px;"><span class="badge badge-success">Semantik</span></div>
                <h3 class="method-title">Cosine Similarity</h3>
                <p class="method-desc">
                    Mengukur kemiripan arah kosinus antar vektor representasi kata. Memberikan bobot <strong>60%</strong> pada skor akhir karena kemampuannya mendeteksi kemiripan semantik.
                </p>
            </div>
            <div class="method-card">
                <div style="margin-bottom: 12px;"><span class="badge badge-warning">Leksikal</span></div>
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

</main>

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
    // Toggle Tema

    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }



    // Aksi Modal Palet Perintah

    const cmdPalette = document.getElementById('cmd-palette');
    const cmdInput = document.getElementById('cmd-input');
    const cmdOpenBtn = document.getElementById('cmd-open-btn');

    // ── Simulator Terminal (leksika-engine v2.0) ─────────────────────

    const simBody = document.getElementById('sim-body');
    if (simBody) {
        simBody.setAttribute('aria-live', 'polite');

        const sampleTitles = [
            "Klasifikasi Sentimen Ulasan Online Shop Menggunakan Naive Bayes",
            "Sistem Pemetaan Hasil Pertanian Menggunakan Algoritma K-Means",
            "Perancangan Sistem Pendataan Kehadiran Mahasiswa"
        ];

        const scenarios = [
            {
                cosine: 72.4, jaccard: 53.8, hybrid: 64.96,
                match: "Klasifikasi Sentimen Konsumen Online Shop Instagram Dengan Menggunakan Algoritma Naive Bayes Classifier",
                verdict: 'Tinggi — Perlu Revisi', cls: 'sim-verdict-high'
            },
            {
                cosine: 48.5, jaccard: 37.5, hybrid: 44.10,
                match: "Sistem Pemetaan Hasil Pertanian Kabupaten Bireuen Menggunakan Metode K-Means Clustering",
                verdict: 'Sedang — Perlu Cek Manual', cls: 'sim-verdict-med'
            },
            {
                cosine: 15.2, jaccard: 11.1, hybrid: 13.56,
                match: "Sistem Pendataan Kehadiran Mahasiswa Menggunakan Kamera Berbasis Website",
                verdict: 'Sangat Rendah — Lolos', cls: 'sim-verdict-low'
            },
        ];

        let currentIdx = 0;
        let activeTimers = [];

        const sleep = (ms) => new Promise(r => {
            const t = setTimeout(r, ms);
            activeTimers.push(t);
        });

        const addLine = (text, className = '') => {
            const p = document.createElement('p');
            p.className = `sim-line ${className}`;
            p.textContent = text;
            simBody.appendChild(p);
            simBody.scrollTop = simBody.scrollHeight;
            return p;
        };

        const typeText = (el, text, speed = 30) => new Promise(resolve => {
            el.textContent = '';
            el.classList.add('sim-typing');
            let i = 0;
            const fn = () => {
                if (i < text.length) {
                    el.textContent = text.slice(0, i + 1);
                    i++;
                    const t = setTimeout(fn, speed);
                    activeTimers.push(t);
                    simBody.scrollTop = simBody.scrollHeight;
                } else {
                    el.classList.remove('sim-typing');
                    resolve();
                }
            };
            fn();
        });

        const spinnerFrames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];

        const showSpinner = async (label, duration = 1000) => {
            const p = addLine('', 'sim-process');
            let frame = 0;
            const interval = setInterval(() => {
                p.textContent = `${spinnerFrames[frame % spinnerFrames.length]} ${label}`;
                frame++;
            }, 70);
            await sleep(duration);
            clearInterval(interval);
            p.textContent = `✓ ${label}`;
            p.className = 'sim-line sim-success';
        };

        const showProgress = async (label, duration = 1400) => {
            const p = addLine('', 'sim-process');
            const steps = 18;
            for (let i = 1; i <= steps; i++) {
                const filled = '█'.repeat(i);
                const empty = '░'.repeat(steps - i);
                const pct = Math.round((i / steps) * 100);
                p.textContent = `${label} [${filled}${empty}] ${pct}%`;
                await sleep(duration / steps);
            }
            p.textContent = `✓ ${label}`;
            p.className = 'sim-line sim-success';
        };

        async function runSimulation() {
            addLine('leksika-engine v2.0 — Hybrid Similarity Engine', 'sim-info');
            await sleep(500);
            addLine('Initializing NLP pipeline...', 'sim-info');
            await sleep(400);
            addLine('Loading corpus (4.827 judul skripsi)...', 'sim-info');
            await sleep(800);

            while (true) {
                const title = sampleTitles[currentIdx % sampleTitles.length];
                const sc = scenarios[currentIdx % scenarios.length];
                currentIdx++;

                simBody.innerHTML = '';

                const inputLine = addLine('', 'sim-input');
                await typeText(inputLine, `$ leksika "${title}"`, 25);

                await showSpinner('case_folding — Case Folding...', 700);
                await showSpinner('filtering — Filtering Stopwords...', 600);
                await showSpinner('stemming — Stemming (Sastrawi)...', 800);

                await showProgress('TF-IDF Vectorization', 1200);
                await showProgress('Cosine & Jaccard Similarity', 1000);

                await sleep(200);
                addLine(`  ↳ Terdekat: "${sc.match}"`, 'sim-subtext');
                await sleep(200);
                addLine(`  ↳ Cosine Similarity: ${sc.cosine}%`, 'sim-calc');
                await sleep(150);
                addLine(`  ↳ Jaccard Similarity: ${sc.jaccard}%`, 'sim-calc');
                await sleep(150);
                addLine(`  ↳ Hybrid Score: ${sc.hybrid}%`, 'sim-calc');
                await sleep(300);

                addLine(`[ HASIL ] ${sc.verdict}`, sc.cls);

                await sleep(4000);
            }
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
            // Reset visibilitas semua tautan cepat

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
