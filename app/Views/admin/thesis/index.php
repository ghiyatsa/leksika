<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Dataset Judul Skripsi</h1>
        <p>Data judul skripsi yang digunakan sebagai pembanding dalam pengecekan kemiripan</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-3">
    <form method="GET" class="search-bar">
        <div class="search-input-wrap">
            <span class="search-icon"><?= render_icon('search', ['style' => 'width: 16px; height: 16px; stroke: var(--text-faint);']) ?></span>
            <input
                type="text"
                id="search-thesis"
                name="search"
                class="form-control"
                placeholder="Cari judul, mahasiswa, kategori..."
                value="<?= esc($search) ?>"
                aria-label="Cari judul, mahasiswa, atau kategori"
            >
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($search): ?>
            <a href="<?= base_url('admin/thesis') ?>" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-subtitle">
                <?php if ($search): ?>
                    Hasil pencarian "<strong><?= esc($search) ?></strong>" — <?= $result['total'] ?> data ditemukan
                <?php else: ?>
                    Total <?= $result['total'] ?> data
                <?php endif; ?>
            </div>
        </div>
        <a href="<?= base_url('admin/thesis/create') ?>" class="btn btn-primary"><?= render_icon('plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;']) ?> <span class="btn-text">Tambah</span></a>
    </div>

    <?php if (empty($result['data'])): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('book', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p><?= $search ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data judul skripsi.' ?></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <caption class="table-caption-sr">Daftar judul skripsi pembanding</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Judul Skripsi</th>
                        <th scope="col">Mahasiswa (NIM)</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Tahun</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $i => $thesis): ?>
                        <tr>
                            <td class="text-mono text-muted text-sm">
                                <?= (($result['page'] - 1) * $result['perPage']) + $i + 1 ?>
                            </td>
                            <td style="max-width: 320px;">
                                <div class="fw-bold" style="line-height: 1.4; margin-bottom: 4px;"><?= esc($thesis['title']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($thesis['student_name']) ?></div>
                                <div class="text-mono text-muted text-sm"><?= esc($thesis['nim']) ?></div>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= esc($thesis['category_name']) ?></span>
                            </td>
                            <td class="text-mono text-sm"><?= esc($thesis['year']) ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="<?= base_url('admin/thesis/' . $thesis['id'] . '/edit') ?>"
                                       class="btn btn-warning btn-sm" aria-label="Edit judul <?= esc($thesis['title']) ?>"><?= render_icon('edit', ['style' => 'width: 14px; height: 14px; margin-right: 4px;']) ?> <span class="btn-text">Edit</span></a>
                                    <form action="<?= base_url('admin/thesis/' . $thesis['id'] . '/delete') ?>"
                                          method="POST" data-confirm="Hapus judul ini?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus judul <?= esc($thesis['title']) ?>"><?= render_icon('trash', ['style' => 'width: 14px; height: 14px;']) ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php
        $totalPages = (int) ceil($result['total'] / $result['perPage']);
        $currentPage = $result['page'];
        ?>
        <?php if ($totalPages > 1): ?>
            <nav class="pagination" role="navigation" aria-label="Navigasi halaman data skripsi">
                <span class="page-info">Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
                <?php if ($currentPage > 1): ?>
                    <a href="?page=1&search=<?= urlencode($search) ?>" aria-label="Halaman pertama">«</a>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Halaman sebelumnya">‹</a>
                <?php else: ?>
                    <span class="disabled" aria-hidden="true">«</span>
                    <span class="disabled" aria-hidden="true">‹</span>
                <?php endif; ?>
                <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                    <?php if ($p === $currentPage): ?>
                        <span class="current" aria-current="page" aria-label="Halaman <?= $p ?>, halaman saat ini"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" aria-label="Halaman <?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Halaman selanjutnya">›</a>
                    <a href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>" aria-label="Halaman terakhir">»</a>
                <?php else: ?>
                    <span class="disabled" aria-hidden="true">›</span>
                    <span class="disabled" aria-hidden="true">»</span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
