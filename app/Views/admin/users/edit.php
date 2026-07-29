<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-header">
    <div><h1>Edit Pengguna</h1></div>
</div>
<div class="card" style="max-width: 500px;">
    <form action="<?= base_url('admin/users/' . $user['id'] . '/update') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="name" class="form-label">Nama <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control"
                   value="<?= esc(old('name', $user['name'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control"
                   value="<?= esc(old('email', $user['email'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" id="password" name="password" class="form-control" minlength="6">
            <div class="form-hint">Kosongkan jika tidak ingin mengubah password.</div>
        </div>
        <div class="form-group">
            <label for="role" class="form-label">Role <span class="required">*</span></label>
            <select id="role" name="role" class="form-select" required
                    <?= $user['id'] == session()->get('userId') ? 'disabled' : '' ?>>
                <option value="user" <?= old('role', $user['role']) === 'user' ? 'selected' : '' ?>>User (Mahasiswa)</option>
                <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <?php if ($user['id'] == session()->get('userId')): ?>
                <input type="hidden" name="role" value="<?= esc($user['role']) ?>">
                <div class="form-hint text-warning"><?= render_icon('alert-circle', ['style' => 'width: 12px; height: 12px; margin-right: 4px; vertical-align: text-bottom;']) ?> Anda tidak dapat mengubah role akun sendiri.</div>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-success"><?= render_icon('save', ['style' => 'width: 14px; height: 14px; margin-right: 4px; stroke: currentColor;']) ?> Simpan Perubahan</button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
