<?php
$role        = session()->get('role');
$currentUri  = uri_string();
$currentUri  = '/' . ltrim($currentUri, '/');
$userName    = session()->get('userName') ?? 'U';

function isActive(string $path, string $current): string {
    return str_starts_with($current, $path) ? 'active' : '';
}
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-logo-link">
            <div class="logo-icon"><?= render_icon('graduation-cap', ['style' => 'width: 24px; height: 24px; color: #fff;']) ?></div>
            <div class="sidebar-logo-meta">
                <div class="logo-title">Leksika</div>
                <div class="logo-sub">Orisinalitas Skripsi</div>
            </div>
        </a>
        <button id="sidebar-close" class="sidebar-close-btn" aria-label="Tutup menu navigasi">
            <?= render_icon('x') ?>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Menu utama">
        <div class="sidebar-section-label">Menu Utama</div>
        <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-link <?= isActive('/admin/dashboard', $currentUri) ?>">
            <span class="icon"><?= render_icon('home') ?></span> Dashboard
        </a>
        <?php endif; ?>
        <a href="<?= base_url('similarity') ?>" class="sidebar-link <?= isActive('/similarity', $currentUri) && !str_starts_with($currentUri, '/similarity/history') ? 'active' : '' ?>">
            <span class="icon"><?= render_icon('search') ?></span> Cek Kemiripan
        </a>
        <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('similarity/history') ?>" class="sidebar-link <?= isActive('/similarity/history', $currentUri) ?>">
            <span class="icon"><?= render_icon('file-check') ?></span> Riwayat Pengecekan
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <div class="sidebar-section-label">Kelola Data</div>
        <a href="<?= base_url('admin/thesis') ?>" class="sidebar-link <?= isActive('/admin/thesis', $currentUri) ?>">
            <span class="icon"><?= render_icon('book') ?></span> Data Skripsi
        </a>
        <a href="<?= base_url('admin/students') ?>" class="sidebar-link <?= isActive('/admin/students', $currentUri) ?>">
            <span class="icon"><?= render_icon('user') ?></span> Data Mahasiswa
        </a>
        <a href="<?= base_url('admin/categories') ?>" class="sidebar-link <?= isActive('/admin/categories', $currentUri) ?>">
            <span class="icon"><?= render_icon('tag') ?></span> Kategori Topik
        </a>

        <div class="sidebar-section-label">Administrasi</div>
        <a href="<?= base_url('admin/users') ?>" class="sidebar-link <?= isActive('/admin/users', $currentUri) ?>">
            <span class="icon"><?= render_icon('users') ?></span> Manajemen Akun
        </a>
        <a href="<?= base_url('admin/threshold') ?>" class="sidebar-link <?= isActive('/admin/threshold', $currentUri) ?>">
            <span class="icon"><?= render_icon('settings') ?></span> Pengaturan
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <?php $avatarSrc = get_avatar_src(session()->get() ?? []); ?>
        <a href="<?= base_url('profile') ?>" class="sidebar-user">
            <div class="avatar">
                <?php if ($avatarSrc): ?>
                    <img src="<?= $avatarSrc ?>" alt="Foto profil <?= esc($userName) ?>" referrerpolicy="no-referrer">
                <?php else: ?>
                    <?= strtoupper(substr($userName, 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= esc($userName) ?></div>
                <div class="sidebar-user-role"><?= esc($role) ?></div>
            </div>
        </a>
        <a href="<?= base_url('logout') ?>" class="btn btn-secondary btn-sm" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <?= render_icon('log-out', ['style' => 'width: 14px; height: 14px; stroke: currentColor;']) ?> Logout
        </a>
    </div>
</aside>
