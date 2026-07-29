<?= $this->extend(session()->get('role') === 'admin' ? 'layouts/main' : 'layouts/public') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 tabindex="-1" id="result-heading">Hasil Pengecekan</h1>
        <p><?= date('d F Y, H:i', strtotime($check['checked_at'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('similarity') ?>" class="btn btn-primary"><?= render_icon('plus', ['style' => 'width: 14px; height: 14px;']) ?> Cek Baru</a>
        <a href="<?= base_url('similarity/history') ?>" class="btn btn-secondary"><?= render_icon('back', ['style' => 'width: 14px; height: 14px;']) ?> Riwayat</a>
    </div>
</div>

<!-- Input Summary -->
<div class="card mb-3 animate-fade-in">
    <div class="card-header">
        <div class="card-title">Judul yang Diperiksa</div>
    </div>
    <div style="margin-bottom: 8px;">
        <div style="font-size: 18px; font-weight: 600; color: var(--text-primary); line-height: 1.5;">
            <?= esc($check['input_title']) ?>
        </div>
    </div>
    <div class="text-muted text-sm mt-2">
        Diperiksa oleh: <strong><?= esc($check['user_name']) ?></strong> ·
        Total pembanding: <strong><?= count($details) ?></strong> judul
    </div>
</div>

<!-- Summary Stats -->
<?php
$sangat  = count(array_filter($details, fn($d) => $d['result_category'] === 'Sangat Mirip'));
$perlu   = count(array_filter($details, fn($d) => $d['result_category'] === 'Perlu Ditinjau'));
$aman    = count(array_filter($details, fn($d) => $d['result_category'] === 'Aman'));
$topHybrid = $details[0]['hybrid_score'] ?? 0;
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger);"><?= render_icon('alert-triangle', ['style' => 'width: 24px; height: 24px;']) ?></div>
        <div class="stat-info">
            <div class="stat-label">Sangat Mirip</div>
            <div class="stat-value" style="color: var(--danger);"><?= $sangat ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning);"><?= render_icon('info', ['style' => 'width: 24px; height: 24px;']) ?></div>
        <div class="stat-info">
            <div class="stat-label">Perlu Ditinjau</div>
            <div class="stat-value" style="color: var(--warning);"><?= $perlu ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success);"><?= render_icon('check-circle', ['style' => 'width: 24px; height: 24px;']) ?></div>
        <div class="stat-info">
            <div class="stat-label">Aman</div>
            <div class="stat-value" style="color: var(--success);"><?= $aman ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue" style="background: var(--accent-light); color: var(--accent);"><?= render_icon('trending-up', ['style' => 'width: 24px; height: 24px;']) ?></div>
        <div class="stat-info">
            <div class="stat-label">Skor Tertinggi</div>
            <div class="stat-value" style="font-size: 26px; color: var(--accent);"><?= number_format($topHybrid * 100, 1) ?>%</div>
        </div>
    </div>
</div>



<?php
$similarPct = (float)$threshold['similar_threshold'] * 100;
$reviewPct = (float)$threshold['review_threshold'] * 100;
$cosinePct = (float)$threshold['cosine_weight'] * 100;
$jaccardPct = (float)$threshold['jaccard_weight'] * 100;
?>
<div class="animate-fade-in" style="animation-delay: 0.12s; display: flex; flex-wrap: wrap; gap: 16px; align-items: center; padding: 16px 20px; background: var(--bg-surface); border: 1.5px solid var(--border-light); border-radius: var(--radius-md); margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span class="badge" style="background: var(--badge-safe-fill); color: white; font-size: 11px; font-weight: 600; letter-spacing: 0.3px;">Aman</span>
        <span style="font-size: 13px; font-weight: 500;">&lt; <?= $reviewPct ?>%</span>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <span class="badge" style="background: var(--badge-warn-fill); color: white; font-size: 11px; font-weight: 600; letter-spacing: 0.3px;">Perlu Ditinjau</span>
        <span style="font-size: 13px; font-weight: 500;"><?= $reviewPct ?>%–<?= $similarPct - 1 ?>%</span>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <span class="badge" style="background: var(--badge-danger-fill); color: white; font-size: 11px; font-weight: 600; letter-spacing: 0.3px;">Sangat Mirip</span>
        <span style="font-size: 13px; font-weight: 500;">&ge; <?= $similarPct ?>%</span>
    </div>
    <div style="display: flex; align-items: center; gap: 6px; margin-left: auto; font-size: 12.5px; color: var(--text-muted);">
        <?= render_icon('settings', ['style' => 'width: 14px; height: 14px;']) ?>
        <span>Cosine&nbsp;<?= $cosinePct ?>% + Jaccard&nbsp;<?= $jaccardPct ?>%</span>
    </div>
</div>

<!-- Results Table -->
<div class="card animate-fade-in" style="animation-delay: 0.15s;">
    <div class="card-header">
        <div>
            <div class="card-title">Tabel Hasil Seluruh Pembanding</div>
            <div class="card-subtitle">Diurutkan berdasarkan skor hybrid tertinggi</div>
        </div>
    </div>

    <?php if (empty($details)): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('file-check', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p>Tidak ada data pembanding yang cocok dengan judul ini. Pastikan dataset telah diisi atau coba dengan judul lain.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul Skripsi (Pembanding)</th>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">Tahun</th>
                        <th scope="col" title="Kemiripan makna/konsep antar judul (bobot 60%)">Cosine</th>
                        <th scope="col" title="Kemiripan kata persis antar judul (bobot 40%)">Jaccard</th>
                        <th scope="col" title="Skor akhir gabungan Cosine + Jaccard">Hybrid</th>
                        <th scope="col" title="Kategori berdasarkan batas threshold">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $i => $row): ?>
                        <?php
                        $cosineColor  = similarity_score_color((float)$row['cosine_score'],  (float)$threshold['similar_threshold'], (float)$threshold['review_threshold']);
                        $jaccardColor = similarity_score_color((float)$row['jaccard_score'], (float)$threshold['similar_threshold'], (float)$threshold['review_threshold']);
                        $hybridColor  = similarity_score_color((float)$row['hybrid_score'],  (float)$threshold['similar_threshold'], (float)$threshold['review_threshold']);
                        ?>
                        <tr>
                            <td class="text-muted text-sm text-mono"><?= $i + 1 ?></td>
                            <td style="max-width: 320px;">
                                <div style="font-weight: 600; line-height: 1.4; word-break: break-word; overflow-wrap: break-word;"><?= esc($row['thesis_title']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($row['student_name']) ?></div>
                                <div class="text-muted text-sm text-mono"><?= esc($row['nim']) ?></div>
                            </td>
                            <td class="text-mono text-sm"><?= esc($row['year']) ?></td>
                            <td>
<span class="score-val text-<?= $cosineColor ?>"><?= number_format($row['cosine_score'], 4) ?><?php if ($cosineColor === 'danger'): ?> <span class="sr-only">Tinggi</span><?php elseif ($cosineColor === 'warning'): ?> <span class="sr-only">Sedang</span><?php else: ?> <span class="sr-only">Rendah</span><?php endif; ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="score-val text-<?= $jaccardColor ?>"><?= number_format($row['jaccard_score'], 4) ?><?php if ($jaccardColor === 'danger'): ?> <span class="sr-only">Tinggi</span><?php elseif ($jaccardColor === 'warning'): ?> <span class="sr-only">Sedang</span><?php else: ?> <span class="sr-only">Rendah</span><?php endif; ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="score-val text-<?= $hybridColor ?>" style="font-size: 15px; font-weight: 700;"><?= number_format($row['hybrid_score'], 4) ?><?php if ($hybridColor === 'danger'): ?> <span class="sr-only">Tinggi</span><?php elseif ($hybridColor === 'warning'): ?> <span class="sr-only">Sedang</span><?php else: ?> <span class="sr-only">Rendah</span><?php endif; ?></span>
                            </td>
                            <td>
                                <span class="badge <?= similarity_category_badge($row['result_category']) ?>">
                                    <?= esc($row['result_category']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const heading = document.getElementById('result-heading');
    if (heading) heading.focus();
});
</script>
<?= $this->endSection() ?>

