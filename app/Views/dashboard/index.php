<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php
$sangat = 0;
$perlu = 0;
$aman = 0;
foreach ($topResults as $row) {
    if ($row['result_category'] === 'Sangat Mirip') $sangat = (int)$row['count'];
    if ($row['result_category'] === 'Perlu Ditinjau') $perlu = (int)$row['count'];
    if ($row['result_category'] === 'Aman') $aman = (int)$row['count'];
}
?>

<!-- Stat Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--accent-glow); color: var(--accent);"><?= render_icon('book') ?></div>
        <div class="stat-info">
            <div class="stat-label">Total Dokumen</div>
            <div class="stat-value"><?= number_format($totalThesis) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--info-bg); color: var(--info);"><?= render_icon('search') ?></div>
        <div class="stat-info">
            <div class="stat-label">Total Diperiksa</div>
            <div class="stat-value"><?= number_format($totalChecks) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success);"><?= render_icon('user') ?></div>
        <div class="stat-info">
            <div class="stat-label">Pengguna Aktif</div>
            <div class="stat-value"><?= number_format($totalUsers) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);"><?= render_icon('settings') ?></div>
        <div class="stat-info">
            <div class="stat-label">Batas Kemiripan</div>
            <div class="stat-value"><?= number_format($settings['similar_threshold'] * 100) ?>%</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2">
    <!-- Bar Chart -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Kategori Hasil</div>
                <div class="card-subtitle">Jumlah judul berdasarkan tingkat kemiripan</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="barChart" aria-label="Jumlah judul berdasarkan tingkat kemiripan" role="img"></canvas>
        </div>
    </div>

    <!-- Doughnut Chart -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Proporsi Status</div>
                <div class="card-subtitle">Persentase tingkat kemiripan judul</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="doughnutChart" aria-label="Proporsi status judul" role="img"></canvas>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    const categories = ['Aman', 'Perlu Ditinjau', 'Sangat Mirip'];
    const counts = [<?= $aman ?>, <?= $perlu ?>, <?= $sangat ?>];
    const totalChecks = <?= $totalChecks ?>;
    const fontDisplay = "'Plus Jakarta Sans', sans-serif";

    function getThemeColors() {
        const s = getComputedStyle(document.documentElement);
        return {
            text: s.getPropertyValue('--text-muted').trim() || '#94a3b8',
            textPrimary: s.getPropertyValue('--text-primary').trim() || '#f8fafc',
            grid: s.getPropertyValue('--border-light').trim() || '#1e293b',
            bgCard: s.getPropertyValue('--bg-card').trim() || '#161b22',
            aman: s.getPropertyValue('--success').trim() || '#10b981',
            perlu: s.getPropertyValue('--warning').trim() || '#f59e0b',
            sangat: s.getPropertyValue('--danger').trim() || '#ef4444'
        };
    }

    let colors = getThemeColors();

    const emptyStateHtml = `
        <div class="empty-state" role="status">
            <div class="empty-icon" aria-hidden="true">◎</div>
            <strong class="empty-title">Belum ada pemeriksaan</strong>
            <p>Hasil analisis akan muncul di sini setelah judul pertama diperiksa.</p>
            <a href="<?= base_url('similarity') ?>" class="btn btn-primary empty-state-action">
                <?= render_icon('plus', ['style' => 'width: 14px; height: 14px;']) ?>
                Mulai pengecekan pertama
            </a>
        </div>`;

    let barChartInstance = null;
    let doughnutChartInstance = null;

    // 1. Bar Chart (Left)
    const ctxBar = document.getElementById('barChart');
    if (ctxBar && totalChecks > 0) {
        barChartInstance = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Jumlah judul',
                    data: counts,
                    backgroundColor: [colors.aman, colors.perlu, colors.sangat],
                    borderRadius: 8,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: colors.text, font: { family: fontDisplay, size: 12 } },
                        grid: { color: colors.grid }
                    },
                    y: {
                        ticks: { color: colors.text, font: { family: fontDisplay, size: 12 }, precision: 0 },
                        grid: { color: colors.grid }
                    }
                }
            }
        });
    } else if (ctxBar) {
        ctxBar.parentElement.innerHTML = emptyStateHtml;
    }

    // 2. Doughnut Chart (Right) with Center Text
    const ctxDoughnut = document.getElementById('doughnutChart');
    if (ctxDoughnut && totalChecks > 0) {
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                const { ctx, chartArea: { top, left, width, height } } = chart;
                ctx.save();
                ctx.font = 'bold 11px JetBrains Mono';
                ctx.fillStyle = colors.text;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('TOTAL', left + width / 2, top + height / 2 - 10);
                
                ctx.font = "bold 20px 'Plus Jakarta Sans', sans-serif";
                ctx.fillStyle = colors.textPrimary;
                ctx.fillText(totalChecks, left + width / 2, top + height / 2 + 10);
                ctx.restore();
            }
        };

        doughnutChartInstance = new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: counts,
                    backgroundColor: [colors.aman, colors.perlu, colors.sangat],
                    borderColor: colors.bgCard,
                    borderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.text,
                            font: { family: fontDisplay, size: 12 },
                            padding: 16,
                        }
                    }
                }
            },
            plugins: [centerTextPlugin]
        });
    } else if (ctxDoughnut) {
        ctxDoughnut.parentElement.innerHTML = emptyStateHtml;
    }

    // Update function to apply new colors on theme toggle
    function updateCharts() {
        colors = getThemeColors();

        if (barChartInstance) {
            barChartInstance.options.scales.x.ticks.color = colors.text;
            barChartInstance.options.scales.x.grid.color = colors.grid;
            barChartInstance.options.scales.y.ticks.color = colors.text;
            barChartInstance.options.scales.y.grid.color = colors.grid;
            barChartInstance.update('none');
        }

        if (doughnutChartInstance) {
            doughnutChartInstance.options.plugins.legend.labels.color = colors.text;
            doughnutChartInstance.data.datasets[0].borderColor = colors.bgCard;
            doughnutChartInstance.update('none');
        }
    }

    // Observe changes to documentElement for theme changes (data-theme attribute)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-theme') {
                updateCharts();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });

})();
</script>
<?= $this->endSection() ?>
