<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Tambah Judul Skripsi</h1>
        <p>Tambahkan data judul baru ke dataset pembanding</p>
    </div>
</div>

<div class="card">
    <form action="<?= base_url('admin/thesis/store') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="student_id" class="form-label">Mahasiswa <span class="required">*</span></label>
                <select id="student_id" name="student_id" class="form-select" required>
                    <option value="">— Pilih Mahasiswa —</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= old('student_id') == $s['id'] ? 'selected' : '' ?>>
                            <?= esc($s['student_id']) ?> — <?= esc($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category_id" class="form-label">Kategori Topik <span class="required">*</span></label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">— Pilih Kategori —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= old('category_id') == $c['id'] ? 'selected' : '' ?>>
                            <?= esc($c['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="title" class="form-label">Judul Skripsi <span class="required">*</span></label>
            <textarea id="title" name="title" class="form-control" rows="2" required
                      placeholder="Tulis judul lengkap..."><?= esc(old('title')) ?></textarea>
        </div>

        <div class="form-group">
            <label for="keyword" class="form-label">Kata Kunci (Keyword)</label>
            <input type="text" id="keyword" name="keyword" class="form-control"
                   placeholder="sistem informasi, web, CodeIgniter, PHP"
                   value="<?= esc(old('keyword')) ?>">
            <div class="form-hint">Pisahkan dengan koma. Keyword meningkatkan akurasi pengecekan.</div>
        </div>

        <div class="form-group">
            <label for="abstract" class="form-label">Abstrak</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="4"
                      placeholder="Tulis abstrak singkat..."><?= esc(old('abstract')) ?></textarea>
        </div>

        <div class="form-group">
            <label for="year" class="form-label">Tahun Skripsi</label>
            <input type="number" id="year" name="year" class="form-control"
                   placeholder="2023" min="2000" max="<?= date('Y') ?>"
                   value="<?= esc(old('year', date('Y'))) ?>">
        </div>

        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-success"><?= render_icon('save', ['style' => 'width: 14px; height: 14px; margin-right: 4px; stroke: currentColor;']) ?> Simpan Data</button>
            <a href="<?= base_url('admin/thesis') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
