<?= $this->extend(session()->get('role') === 'admin' ? 'layouts/main' : 'layouts/public') ?>
<?= $this->section('content') ?>

<div class="profile-card">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Profil Saya</div>
                <div class="card-subtitle">Kelola informasi pribadi dan kata sandi Anda</div>
            </div>
        </div>

        <form action="<?= base_url('profile/update') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Avatar Section -->
            <div class="avatar-preview-container">
                <div class="avatar-circle-lg" id="avatar-preview-container">
                    <?php $avatarSrc = get_avatar_src($user); if ($avatarSrc): ?>
                        <img src="<?= esc($avatarSrc, 'attr') ?>" alt="Foto profil <?= esc($user['name']) ?>" referrerpolicy="no-referrer">
                    <?php else: ?>
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="avatar-input" class="form-label" style="margin-bottom: 8px;">Foto Profil</label>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <div class="file-input-wrapper">
                            <button type="button" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 6px;">
                                <?= render_icon('camera', ['style' => 'width: 14px; height: 14px;']) ?> Pilih Foto
                            </button>
                            <input type="file" name="avatar" accept="image/*" id="avatar-input">
                        </div>
                        <?php if (!empty($user['avatar'])): ?>
                        <button type="button" class="btn btn-danger btn-sm" id="delete-avatar-btn" style="display: flex; align-items: center; gap: 6px;">
                            <?= render_icon('trash', ['style' => 'width: 14px; height: 14px;']) ?> Hapus
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="form-hint" style="margin-top: 6px;" id="avatar-filename">Maksimal 2MB. Format: JPG, JPEG, PNG, WEBP.</div>
                </div>
            </div>

            <!-- Profile Info Form -->
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="<?= esc(old('name', $user['name'])) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= esc($user['email']) ?>"
                    disabled
                    aria-disabled="true"
                >
                <div class="form-hint" style="margin-top:4px;">Email tidak dapat diubah. Hubungi admin jika perlu perubahan.</div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Kosongkan jika tidak ingin mengubah kata sandi"
                >
                <div class="form-hint">Minimal 6 karakter.</div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 16px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= render_icon('save') ?> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Section -->
<div class="card mt-4">
    <div class="card-header">
        <div>
            <div class="card-title">Ubah Kata Sandi</div>
            <div class="card-subtitle">Ganti kata sandi akun Anda</div>
        </div>
    </div>

    <form action="<?= base_url('profile/change-password') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="current_password" class="form-label">Password Saat Ini <span class="required">*</span></label>
            <input
                type="password"
                id="current_password"
                name="current_password"
                class="form-control"
                placeholder="Masukkan password saat ini"
                required
            >
        </div>

        <div class="form-group">
            <label for="new_password" class="form-label">Password Baru <span class="required">*</span></label>
            <input
                type="password"
                id="new_password"
                name="new_password"
                class="form-control"
                placeholder="Minimal 6 karakter"
                required
                minlength="6"
            >
            <div class="form-hint">Minimal 6 karakter.</div>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Konfirmasi Password <span class="required">*</span></label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                class="form-control"
                placeholder="Ketik ulang password baru"
                required
            >
        </div>

        <div style="display: flex; gap: 10px; margin-top: 16px;">
            <button type="submit" class="btn btn-primary btn-lg">
                <?= render_icon('lock') ?> Ubah Password
            </button>
        </div>
    </form>
</div>

<!-- Danger Zone -->
<div class="card danger-card mt-4">
    <div class="card-header">
        <div class="danger-zone-title">
            <?= render_icon('alert-triangle', ['style' => 'width: 20px; height: 20px;']) ?> Zona Bahaya
        </div>
    </div>
    <div class="danger-zone-box">
        <div style="flex: 1;">
            <div style="font-weight: 700; color: var(--text-primary); font-size: 14.5px; margin-bottom: 4px;">Hapus Akun Permanen</div>
            <div class="text-muted text-sm" style="line-height: 1.5;">
                Setelah Anda menghapus akun, semua data Anda, termasuk riwayat pemeriksaan kemiripan skripsi, akan dihapus secara permanen dan tidak dapat dipulihkan.
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-danger" id="delete-account-trigger">
                Hapus Akun
            </button>
            <form action="<?= base_url('profile/delete') ?>" method="POST" id="delete-account-form" style="display:none;">
                <?= csrf_field() ?>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Modal — Delete Account -->
<div class="confirm-modal-overlay" id="confirm-delete-account" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title-account" aria-describedby="confirm-body-account">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">
            <?= render_icon('alert-triangle', ['style' => 'width: 22px; height: 22px;']) ?>
        </div>
        <div class="confirm-modal-title" id="confirm-title-account">Hapus Akun Permanen?</div>
        <div class="confirm-modal-body" id="confirm-body-account">
            Tindakan ini akan menghapus akun Anda beserta seluruh riwayat pemeriksaan kemiripan secara permanen. <strong>Tidak dapat dibatalkan.</strong>
        </div>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-secondary" id="confirm-cancel-account">Batal</button>
            <button type="button" class="btn btn-danger" id="confirm-ok-account">Ya, Hapus Akun</button>
        </div>
    </div>
</div>

<!-- Confirm Modal — Delete Avatar -->
<div class="confirm-modal-overlay" id="confirm-delete-avatar" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title-avatar" aria-describedby="confirm-body-avatar">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">
            <?= render_icon('trash', ['style' => 'width: 22px; height: 22px;']) ?>
        </div>
        <div class="confirm-modal-title" id="confirm-title-avatar">Hapus Foto Profil?</div>
        <div class="confirm-modal-body" id="confirm-body-avatar">
            Foto profil Anda akan dihapus dan digantikan oleh inisial nama.
        </div>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-secondary" id="confirm-cancel-avatar">Batal</button>
            <button type="button" class="btn btn-danger" id="confirm-ok-avatar">Ya, Hapus Foto</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Avatar preview ──────────────────────────────────────────────
    const avatarInput = document.getElementById('avatar-input');
    const avatarFilename = document.getElementById('avatar-filename');

    if (avatarInput && avatarFilename) {
        avatarInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                avatarFilename.textContent = `File terpilih: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                avatarFilename.style.color = 'var(--text-primary)';

                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewContainer = document.getElementById('avatar-preview-container');
                    if (previewContainer) {
                        previewContainer.innerHTML = `<img src="${event.target.result}" alt="Preview foto profil baru">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ── Generic confirm modal helper ────────────────────────────────
    function openModal(id) {
        const overlay = document.getElementById(id);
        if (!overlay) return;
        overlay.classList.add('active');
        overlay.removeAttribute('hidden');
        const firstBtn = overlay.querySelector('button');
        if (firstBtn) firstBtn.focus();
    }

    function closeModal(id) {
        const overlay = document.getElementById(id);
        if (!overlay) return;
        overlay.classList.remove('active');
    }

    // ── Delete Account modal ────────────────────────────────────────
    const trigger = document.getElementById('delete-account-trigger');
    if (trigger) {
        trigger.addEventListener('click', () => openModal('confirm-delete-account'));
    }

    const cancelAccount = document.getElementById('confirm-cancel-account');
    if (cancelAccount) cancelAccount.addEventListener('click', () => closeModal('confirm-delete-account'));

    const okAccount = document.getElementById('confirm-ok-account');
    if (okAccount) {
        okAccount.addEventListener('click', () => {
            document.getElementById('delete-account-form').submit();
        });
    }

    // ── Delete Avatar modal ─────────────────────────────────────────
    const deleteAvatarBtn = document.getElementById('delete-avatar-btn');
    if (deleteAvatarBtn) {
        deleteAvatarBtn.addEventListener('click', () => openModal('confirm-delete-avatar'));
    }

    const cancelAvatar = document.getElementById('confirm-cancel-avatar');
    if (cancelAvatar) cancelAvatar.addEventListener('click', () => closeModal('confirm-delete-avatar'));

    const okAvatar = document.getElementById('confirm-ok-avatar');
    if (okAvatar) {
        okAvatar.addEventListener('click', async () => {
            closeModal('confirm-delete-avatar');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('profile/avatar/delete') ?>';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '<?= csrf_token() ?>';
            csrf.value = '<?= csrf_hash() ?>';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Close modals on Escape or backdrop click
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal('confirm-delete-account');
            closeModal('confirm-delete-avatar');
        }
    });
    document.querySelectorAll('.confirm-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });
});
</script>

<?= $this->endSection() ?>
