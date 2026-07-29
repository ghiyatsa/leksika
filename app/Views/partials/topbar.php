<?php
$days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dayName = $days[date('w')];
$monthName = $months[date('n')];
$formattedDate = $dayName . ', ' . date('d') . ' ' . $monthName . ' ' . date('Y');
?>
<header class="topbar">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Buka menu navigasi">
            <?= render_icon('menu') ?>
        </button>
        <a href="<?= base_url('admin/dashboard') ?>" class="topbar-brand">
            <div class="topbar-brand-icon brand-icon-gradient">
                <?= render_icon('graduation-cap', ['style' => 'width: 18px; height: 18px; color: #fff;']) ?>
            </div>
            <div class="topbar-brand-meta">
                <div class="topbar-brand-name">Leksika</div>
                <div class="topbar-brand-sub">Orisinalitas Skripsi</div>
            </div>
        </a>
    </div>
    <div class="topbar-actions">
        <div class="topbar-widget">
            <?= render_icon('calendar', ['style' => 'width: 14px; height: 14px; stroke: var(--text-muted);']) ?>
            <span><?= esc($formattedDate) ?></span>
        </div>
        <div class="topbar-widget" style="margin-right: 4px;">
            <?= render_icon('clock', ['style' => 'width: 14px; height: 14px; stroke: var(--text-muted);']) ?>
            <span id="live-clock"><?= date('H.i.s') ?></span>
        </div>
        <button id="cmd-open-btn" class="nav-kbd-btn" aria-label="Buka command palette" style="padding: 4px 10px; height: 34px; margin-right: 4px;">
            <?= render_icon('search', ['style' => 'width: 14px; height: 14px; stroke: currentColor;']) ?>
            <span class="cmd-text">Cari</span>
            <span class="cmd-kbd-hint" style="padding: 1px 4px; font-size: 10px;">Ctrl+K</span>
        </button>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Ganti tema tampilan">
            <span class="sun-icon"><?= render_icon('sun') ?></span>
            <span class="moon-icon"><?= render_icon('moon') ?></span>
        </button>
    </div>
</header>
