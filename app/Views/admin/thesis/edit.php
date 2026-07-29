<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Edit Judul Skripsi</h1>
        <p>Perbarui data judul skripsi #<?= $thesis['id'] ?></p>
    </div>
</div>

<div class="card">
    <form action="<?= base_url('admin/thesis/' . $thesis['id'] . '/update') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="student_id" class="form-label">Mahasiswa <span class="required">*</span></label>
                <select id="student_id" name="student_id" class="form-select" required>
                    <option value="">— Pilih Mahasiswa —</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= (old('student_id', $thesis['student_id']) == $s['id']) ? 'selected' : '' ?>>
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
                        <option value="<?= $c['id'] ?>"
                            <?= (old('category_id', $thesis['category_id']) == $c['id']) ? 'selected' : '' ?>>
                            <?= esc($c['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="title" class="form-label">Judul Skripsi <span class="required">*</span></label>
            <textarea id="title" name="title" class="form-control" rows="2" required><?= esc(old('title', $thesis['title'])) ?></textarea>
        </div>

        <div class="form-group">
            <label for="keyword" class="form-label">Kata Kunci (Keyword)</label>
            <input type="text" id="keyword" name="keyword" class="form-control"
                   value="<?= esc(old('keyword', $thesis['keyword'])) ?>">
        </div>

        <div class="form-group">
            <label for="abstract" class="form-label">Abstrak</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="4"><?= esc(old('abstract', $thesis['abstract'])) ?></textarea>
        </div>

        <div class="form-group">
            <label for="year" class="form-label">Tahun Skripsi</label>
            <input type="number" id="year" name="year" class="form-control"
                   min="2000" max="<?= date('Y') ?>"
                   value="<?= esc(old('year', $thesis['year'])) ?>">
        </div>

        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-success"><?= render_icon('save', ['style' => 'width: 14px; height: 14px; margin-right: 4px; stroke: currentColor;']) ?> Simpan Perubahan</button>
            <a href="<?= base_url('admin/thesis') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
