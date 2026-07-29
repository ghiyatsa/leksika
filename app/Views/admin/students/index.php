<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Data Mahasiswa</h1>
        <p>Kelola data mahasiswa pemilik judul skripsi</p>
    </div>
</div>

<div class="card mb-3">
    <form method="GET" class="search-bar">
        <div class="search-input-wrap">
            <span class="search-icon"><?= render_icon('search', ['style' => 'width: 16px; height: 16px; stroke: var(--text-faint);']) ?></span>
            <input type="text" id="search-students" name="search" class="form-control" placeholder="Cari NIM atau nama..." aria-label="Cari NIM atau nama mahasiswa"
                   value="<?= esc($search) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($search): ?>
            <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-subtitle">
            <?php if ($search): ?>
                Hasil pencarian "<strong><?= esc($search) ?></strong>" — <?= $result['total'] ?> data ditemukan
            <?php else: ?>
                Total <?= $result['total'] ?> mahasiswa terdaftar
            <?php endif; ?>
        </div>
        <a href="<?= base_url('admin/students/create') ?>" class="btn btn-primary"><?= render_icon('plus', ['style' => 'width: 16px; height: 16px; margin-right: 4px;']) ?> <span class="btn-text">Tambah</span></a>
    </div>

    <?php if (empty($result['data'])): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= render_icon('user', ['style' => 'width: 48px; height: 48px; stroke: var(--text-faint);']) ?></div>
            <p><?= $search ? 'Tidak ada mahasiswa yang cocok.' : 'Belum ada data mahasiswa.' ?></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <caption class="table-caption-sr">Daftar mahasiswa terdaftar</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Nama Mahasiswa</th>
                        <th scope="col">Tanggal Ditambahkan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['data'] as $i => $s): ?>
                        <tr>
                            <td class="text-mono text-muted text-sm">
                                <?= (($result['page'] - 1) * $result['perPage']) + $i + 1 ?>
                            </td>
                            <td class="text-mono fw-bold"><?= esc($s['student_id']) ?></td>
                            <td><?= esc($s['name']) ?></td>
                            <td class="text-muted text-sm"><?= $s['created_at'] ? date('d M Y', strtotime($s['created_at'])) : '—' ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="<?= base_url('admin/students/' . $s['id'] . '/edit') ?>" class="btn btn-warning btn-sm" aria-label="Edit mahasiswa <?= esc($s['name']) ?>"><?= render_icon('edit', ['style' => 'width: 14px; height: 14px; margin-right: 4px;']) ?> <span class="btn-text">Edit</span></a>
                                    <form action="<?= base_url('admin/students/' . $s['id'] . '/delete') ?>" method="POST" data-confirm="Hapus mahasiswa ini? Data judul terkait juga akan terhapus.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus mahasiswa <?= esc($s['name']) ?>"><?= render_icon('trash', ['style' => 'width: 14px; height: 14px;']) ?></button>
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
            <nav class="pagination" role="navigation" aria-label="Navigasi halaman data mahasiswa">
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
