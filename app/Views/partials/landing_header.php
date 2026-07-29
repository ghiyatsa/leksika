<nav class="landing-nav" aria-label="Navigasi utama">
    <div class="nav-container">
        <a href="<?= base_url('/') ?>" class="nav-brand-wrapper">
            <div class="nav-brand-icon">
                <?= render_icon('graduation-cap', ['style' => 'width: 18px; height: 18px; color: #fff;']) ?>
            </div>
            <div class="nav-brand-text">
                <span class="nav-brand-name">Leksika</span>
                <span class="nav-brand-sub">Orisinalitas Skripsi</span>
            </div>
        </a>
        <div class="nav-links">
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle theme">
                <span class="sun-icon"><?= render_icon('sun') ?></span>
                <span class="moon-icon"><?= render_icon('moon') ?></span>
            </button>
            <?php if (session()->get('isLoggedIn')): ?>
                <div class="user-dropdown">
                    <button class="avatar-btn" id="avatarDropdownBtn" aria-label="Menu pengguna" aria-haspopup="true" aria-expanded="false">
                        <?php $avatarSrc = get_avatar_src(session()->get() ?? []); if ($avatarSrc): ?>
                            <img src="<?= esc($avatarSrc, 'attr') ?>" alt="Avatar" referrerpolicy="no-referrer" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <?= strtoupper(substr(session()->get('userName') ?? 'U', 0, 1)) ?>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu" id="avatarDropdownMenu">
                        <div class="dropdown-header">
                            <div class="user-name"><?= esc(session()->get('userName')) ?></div>
                            <div class="user-role"><?= esc(session()->get('userEmail')) ?></div>
                        </div>
                        <hr class="dropdown-divider">
                        <a href="<?= base_url('profile') ?>" class="dropdown-item">
                            <?= render_icon('user', ['style' => 'width: 14px; height: 14px;']) ?> Profil Saya
                        </a>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <a href="<?= base_url('admin/dashboard') ?>" class="dropdown-item">
                                <?= render_icon('home', ['style' => 'width: 14px; height: 14px;']) ?> Dashboard
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url(session()->get('role') === 'admin' ? 'admin/similarity/history' : 'similarity/history') ?>" class="dropdown-item">
                            <?= render_icon('file-check', ['style' => 'width: 14px; height: 14px;']) ?> Riwayat
                        </a>
                        <hr class="dropdown-divider">
                        <form method="POST" action="<?= base_url('logout') ?>" style="margin: 0;">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item text-danger" style="width: 100%; border: none; background: none; cursor: pointer; font: inherit; padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                                <?= render_icon('log-out', ['style' => 'width: 14px; height: 14px;']) ?> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn btn-primary btn-nav">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdownBtn = document.getElementById('avatarDropdownBtn');
    const dropdownMenu = document.getElementById('avatarDropdownMenu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = dropdownMenu.classList.toggle('active');
            dropdownBtn.setAttribute('aria-expanded', isActive);
        });

        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('active');
                dropdownBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

});
</script>
