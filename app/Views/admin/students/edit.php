<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-header">
    <div><h1>Edit Mahasiswa</h1></div>
</div>
<div class="card" style="max-width: 500px;">
    <form action="<?= base_url('admin/students/' . $student['id'] . '/update') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="student_id" class="form-label">NIM <span class="required">*</span></label>
            <input type="text" id="student_id" name="student_id" class="form-control"
                   value="<?= esc(old('student_id', $student['student_id'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="name" class="form-label">Nama Mahasiswa <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control"
                   value="<?= esc(old('name', $student['name'])) ?>" required>
        </div>
        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-success"><?= render_icon('save', ['style' => 'width: 14px; height: 14px; margin-right: 4px; stroke: currentColor;']) ?> Simpan Perubahan</button>
            <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
