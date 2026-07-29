<?= $this->extend(session()->get('role') === 'admin' ? 'layouts/main' : 'layouts/public') ?>
<?= $this->section('content') ?>

<!-- Filter -->
<div class="card history-filter-card mb-3" style="padding: 16px;">
    <form method="GET" action="<?= base_url('similarity/history') ?>" class="d-flex" style="gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0; min-width: 180px; flex: 1 1 0px;">
            <label class="form-label text-sm fw-semibold" style="margin-bottom: 6px; display: block;">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" style="width: 100%; height: 38px;" value="<?= esc($dateFrom) ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 180px; flex: 1 1 0px;">
            <label class="form-label text-sm fw-semibold" style="margin-bottom: 6px; display: block;">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" style="width: 100%; height: 38px;" value="<?= esc($dateTo) ?>">
        </div>
        <div class="d-flex" style="gap: 8px; align-items: center; min-height: 38px; flex: none; justify-content: flex-start;">
            <button type="submit" class="btn btn-primary btn-sm d-inline-flex" style="align-items: center; justify-content: center; gap: 4px; height: 38px; padding: 0 16px;">
                <?= render_icon('search', ['style' => 'width: 14px; height: 14px;']) ?> Filter
            </button>
            <a href="<?= base_url('similarity/history') ?>" class="btn btn-secondary btn-sm d-inline-flex" style="align-items: center; justify-content: center; height: 38px; padding: 0 16px;">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card history-page-card" style="overflow: hidden;">
    <?php if (empty($checks)): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('file-check', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p style="margin-bottom: 12px;">Belum ada riwayat pengecekan<?= ($dateFrom || $dateTo) ? ' pada filter yang dipilih' : '' ?>.</p>
            <?php if (!($dateFrom || $dateTo)): ?>
                <a href="<?= base_url('similarity') ?>" class="btn btn-primary btn-sm d-inline-flex" style="align-items: center; gap: 4px;"><?= render_icon('plus', ['style' => 'width: 14px; height: 14px;']) ?> Cek Judul Sekarang</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; display: block;">
            <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul</th>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <th scope="col">Pengguna</th>
                        <?php endif; ?>
                        <th scope="col">Kemiripan Tertinggi</th>
                        <th scope="col">Waktu Pengecekan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checks as $i => $check): ?>
                        <tr>
                            <td class="text-mono text-muted text-sm"><?= $i + 1 ?></td>
                            <td style="max-width: 400px;">
                                <div class="fw-bold td-truncate" style="word-break: break-word; overflow-wrap: break-word;"><?= esc($check['input_title']) ?></div>
                                <?php if ($check['input_keyword']): ?>
                                    <div class="text-muted text-sm" style="margin-top: 2px;">
                                        <?= render_icon('key', ['style' => 'width: 12px; height: 12px; margin-right: 4px; vertical-align: text-bottom;']) ?><?= esc(mb_substr($check['input_keyword'], 0, 60)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <?php if (session()->get('role') === 'admin'): ?>
                                <td>
                                    <div class="fw-bold"><?= esc($check['user_name']) ?></div>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php if ($check['max_hybrid_score'] !== null): ?>
                                    <?php
                                    $score = (float)$check['max_hybrid_score'];
                                    $badgeClass = 'badge-success';
                                    if ($score >= (float)$threshold['similar_threshold']) {
                                        $badgeClass = 'badge-danger';
                                    } elseif ($score >= (float)$threshold['review_threshold']) {
                                        $badgeClass = 'badge-warning';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= number_format($score * 100, 1) ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-sm text-mono">
                                <?= date('d F Y', strtotime($check['checked_at'])) ?><br>
                                <span><?= date('H:i:s', strtotime($check['checked_at'])) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('similarity/' . esc($check['uuid'] ?? $check['id'])) ?>" class="btn btn-secondary btn-sm d-inline-flex" style="align-items: center; gap: 4px;" aria-label="Lihat hasil <?= esc($check['input_title']) ?>">
                                    <?= render_icon('eye', ['style' => 'width: 14px; height: 14px;']) ?> <span class="btn-text">Lihat Hasil</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-muted text-sm mt-3">
            Total: <strong><?= count($checks) ?></strong> pengecekan
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
