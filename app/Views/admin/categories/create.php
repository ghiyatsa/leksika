<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-header">
    <div><h1>Tambah Kategori</h1></div>
</div>
<div class="card" style="max-width: 500px;">
    <form action="<?= base_url('admin/categories/store') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="category_name" class="form-label">Nama Kategori <span class="required">*</span></label>
            <input type="text" id="category_name" name="category_name" class="form-control"
                   placeholder="Pengembangan Web" value="<?= esc(old('category_name')) ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea id="description" name="description" class="form-control" rows="3"
                      placeholder="Deskripsi singkat topik..."><?= esc(old('description')) ?></textarea>
        </div>
        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-success"><?= render_icon('save', ['style' => 'width: 14px; height: 14px; margin-right: 4px; stroke: currentColor;']) ?> Simpan</button>
            <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
