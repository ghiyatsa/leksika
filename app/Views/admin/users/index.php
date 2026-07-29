<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Manajemen Akun Pengguna</h1>
        <p>Kelola akun admin dan mahasiswa yang dapat mengakses sistem</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-subtitle">Total <?= $result['total'] ?> pengguna terdaftar</div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary"><?= render_icon('plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;']) ?> <span class="btn-text">Tambah</span></a>
    </div>

    <?php if (empty($result['data'])): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('users', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p>Belum ada pengguna.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <caption class="table-caption-sr">Daftar pengguna sistem</caption>
                <thead>
                    <tr><th scope="col">#</th><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Bergabung</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $i => $user): ?>
                        <tr>
                            <td class="text-mono text-muted text-sm"><?= (($result['page'] - 1) * $result['perPage']) + $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-center gap-2" style="align-items: center;">
                                    <div class="avatar avatar-sm" style="font-size: 11px;"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                                    <div class="fw-bold"><?= esc($user['name']) ?></div>
                                    <?php if ($user['id'] == session()->get('userId')): ?>
                                        <span class="badge badge-info text-sm" style="font-size: 10px; padding: 2px 8px;">Anda</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-muted"><?= esc($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                    <?= esc($user['role']) ?>
                                </span>
                            </td>
                            <td class="text-muted text-sm"><?= $user['created_at'] ? date('d M Y', strtotime($user['created_at'])) : '—' ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="<?= base_url('admin/users/' . $user['id'] . '/edit') ?>" class="btn btn-warning btn-sm" aria-label="Edit pengguna <?= esc($user['name']) ?>"><?= render_icon('edit', ['style' => 'width: 14px; height: 14px; margin-right: 4px;']) ?> <span class="btn-text">Edit</span></a>
                                    <?php if ($user['id'] != session()->get('userId')): ?>
                                        <form action="<?= base_url('admin/users/' . $user['id'] . '/delete') ?>" method="POST" data-confirm="Hapus pengguna ini?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus pengguna <?= esc($user['name']) ?>"><?= render_icon('trash', ['style' => 'width: 14px; height: 14px;']) ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($result['total'] > $result['perPage']): ?>
            <div class="pagination">
                <?php $totalPages = (int) ceil($result['total'] / $result['perPage']); ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= base_url('admin/users?page=' . $p . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn btn-sm <?= $p === $result['page'] ? 'btn-primary' : 'btn-secondary' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
