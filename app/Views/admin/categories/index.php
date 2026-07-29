<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Kategori Topik</h1>
        <p>Kelola kategori topik untuk klasifikasi judul skripsi</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-subtitle">Total <?= $result['total'] ?> kategori</div>
        <a href="<?= base_url('admin/categories/create') ?>" class="btn btn-primary"><?= render_icon('plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;']) ?> <span class="btn-text">Tambah</span></a>
    </div>
    <?php if (empty($result['data'])): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('tag', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p>Belum ada kategori.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <caption class="table-caption-sr">Daftar kategori topik skripsi</caption>
                <thead>
                    <tr><th scope="col">#</th><th scope="col">Nama Kategori</th><th scope="col">Deskripsi</th><th scope="col">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $i => $cat): ?>
                        <tr>
                            <td class="text-mono text-muted text-sm"><?= (($result['page'] - 1) * $result['perPage']) + $i + 1 ?></td>
                            <td><span class="badge badge-info"><?= esc($cat['category_name']) ?></span></td>
                            <td class="text-muted"><?= esc($cat['description'] ?: '—') ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="<?= base_url('admin/categories/' . $cat['id'] . '/edit') ?>" class="btn btn-warning btn-sm" aria-label="Edit kategori <?= esc($cat['category_name']) ?>"><?= render_icon('edit', ['style' => 'width: 14px; height: 14px; margin-right: 4px;']) ?> <span class="btn-text">Edit</span></a>
                                    <form action="<?= base_url('admin/categories/' . $cat['id'] . '/delete') ?>" method="POST" data-confirm="Hapus kategori ini?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus kategori <?= esc($cat['category_name']) ?>"><?= render_icon('trash', ['style' => 'width: 14px; height: 14px;']) ?></button>
                                    </form>
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
                    <a href="<?= base_url('admin/categories?page=' . $p . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn btn-sm <?= $p === $result['page'] ? 'btn-primary' : 'btn-secondary' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
