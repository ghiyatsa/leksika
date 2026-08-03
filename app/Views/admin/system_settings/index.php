<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    align-items: start;
    margin-top: 20px;
}
.settings-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
@media (max-width: 992px) {
    .settings-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .settings-col {
        gap: 16px;
    }
}
</style>

<div class="page-header">
    <div>
        <h1>Pengaturan Sistem</h1>
        <p>Konfigurasi bobot kalkulasi kemiripan, ambang batas hasil, dan parameter operasional sistem</p>
    </div>
</div>

<div class="info-box mb-3">
    <?= render_icon('info', ['style' => 'width: 18px; height: 18px; margin-right: 8px; vertical-align: text-bottom; flex-shrink: 0;']) ?>
    <span>Semua perubahan konfigurasi akan diterapkan secara real-time untuk pengecekan judul skripsi berikutnya.</span>
</div>

<form action="<?= base_url('admin/system-settings/update') ?>" method="POST" id="threshold-form">
    <?= csrf_field() ?>

    <div class="settings-grid">
        <!-- Kolom Kiri -->
        <div class="settings-col">
            <!-- Section 1: Bobot Similarity -->
            <div class="card mb-3" style="margin-bottom: 0;">
                <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
                    <div class="card-title">Bobot Perhitungan Similarity</div>
                    <div class="card-subtitle" style="font-size: 13px; color: var(--text-muted);">Kontribusi masing-masing algoritma (total bobot harus bernilai 1.00)</div>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="cosine_weight" class="form-label">Bobot Cosine Similarity (w1)</label>
                        <input type="number" id="cosine_weight" name="cosine_weight"
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['cosine_weight']) ? 'is-invalid' : '' ?>" step="0.01" min="0.01" max="0.99"
                               value="<?= esc($threshold['cosine_weight']) ?>" required>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['cosine_weight'])): ?>
                            <div class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600;">
                                <?= esc(session()->getFlashdata('errors')['cosine_weight']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-hint">Rekomendasi: 0.60 (prioritas utama)</div>
                    </div>
                    <div class="form-group">
                        <label for="jaccard_weight" class="form-label">Bobot Jaccard Similarity (w2)</label>
                        <input type="number" id="jaccard_weight" name="jaccard_weight"
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['jaccard_weight']) ? 'is-invalid' : '' ?>" step="0.01" min="0.01" max="0.99"
                               value="<?= esc($threshold['jaccard_weight']) ?>" required>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['jaccard_weight'])): ?>
                            <div class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600;">
                                <?= esc(session()->getFlashdata('errors')['jaccard_weight']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-hint">Rekomendasi: 0.40</div>
                    </div>
                </div>
                <div id="weight-preview" style="padding: 8px 12px; background: var(--bg-elevated); border-radius: var(--radius-sm); font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); margin-top: 8px;">
                    Total bobot: <span id="weight-total" style="font-weight: 700; color: var(--success);">1.00</span>
                </div>
            </div>

            <!-- Section 3: Batas Jumlah Hasil -->
            <div class="card mb-3" style="margin-bottom: 0;">
                <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
                    <div class="card-title">Batas Jumlah Hasil Pencarian</div>
                    <div class="card-subtitle" style="font-size: 13px; color: var(--text-muted);">Membatasi jumlah rekomendasi skripsi termirip yang ditampilkan dan disimpan</div>
                </div>
                
                <div class="form-group" style="max-width: 280px; margin-bottom: 0;">
                    <label for="max_similarity_results" class="form-label">Maksimal Hasil Ditampilkan</label>
                    <input type="number" id="max_similarity_results" name="max_similarity_results"
                           class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['max_similarity_results']) ? 'is-invalid' : '' ?>" min="1" max="100"
                           value="<?= esc($threshold['max_similarity_results'] ?? 5) ?>" required>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['max_similarity_results'])): ?>
                        <div class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600;">
                            <?= esc(session()->getFlashdata('errors')['max_similarity_results']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-hint">Direkomendasikan: 5 (menjaga kecepatan performa kalkulasi)</div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="settings-col">
            <!-- Section 2: Ambang Batas Kategori Hasil -->
            <div class="card mb-3" style="margin-bottom: 0;">
                <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
                    <div class="card-title">Ambang Batas (Threshold) Kategori</div>
                    <div class="card-subtitle" style="font-size: 13px; color: var(--text-muted);">Batas nilai skor kemiripan untuk menentukan klasifikasi hasil pengecekan</div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="similar_threshold" class="form-label">
                            <span class="badge badge-danger" style="font-size: 10px;">Sangat Mirip</span>
                            ≥ Nilai
                        </label>
                        <input type="number" id="similar_threshold" name="similar_threshold"
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['similar_threshold']) ? 'is-invalid' : '' ?>" step="0.01" min="0.01" max="1.00"
                               value="<?= esc($threshold['similar_threshold']) ?>" required>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['similar_threshold'])): ?>
                            <div class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600;">
                                <?= esc(session()->getFlashdata('errors')['similar_threshold']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-hint">Default: 0.75 (skor hybrid ≥ nilai ini = Sangat Mirip)</div>
                    </div>
                    <div class="form-group">
                        <label for="review_threshold" class="form-label">
                            <span class="badge badge-warning" style="font-size: 10px;">Perlu Ditinjau</span>
                            ≥ Nilai
                        </label>
                        <input type="number" id="review_threshold" name="review_threshold"
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['review_threshold']) ? 'is-invalid' : '' ?>" step="0.01" min="0.01" max="1.00"
                               value="<?= esc($threshold['review_threshold']) ?>" required>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['review_threshold'])): ?>
                            <div class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600;">
                                <?= esc(session()->getFlashdata('errors')['review_threshold']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-hint">Default: 0.40 (nilai di bawah ini = Aman)</div>
                    </div>
                </div>

                <!-- Category Visualization -->
                <div style="margin-top: 16px; padding: 16px; background: var(--bg-elevated); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px; font-weight: 600;">Kategori Hasil Berdasarkan Threshold:</div>
                    <div style="display: flex; gap: 0; border-radius: var(--radius-sm); overflow: hidden; height: 28px;">
                        <div style="flex: <?= $threshold['review_threshold'] * 100 ?>; background: var(--success); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #fff;">Aman</div>
                        <div style="flex: <?= ($threshold['similar_threshold'] - $threshold['review_threshold']) * 100 ?>; background: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #fff;">Perlu Ditinjau</div>
                        <div style="flex: <?= (1 - $threshold['similar_threshold']) * 100 ?>; background: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #fff;">Sangat Mirip</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-faint); margin-top: 4px;" class="text-mono">
                        <span>0.00</span>
                        <span><?= number_format($threshold['review_threshold'], 2) ?></span>
                        <span><?= number_format($threshold['similar_threshold'], 2) ?></span>
                        <span>1.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2" style="margin-top: 24px;">
        <button type="submit" class="btn btn-success">
            <?= render_icon('save', ['style' => 'width: 16px; height: 16px; stroke: currentColor;']) ?> Simpan Pengaturan
        </button>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const cosineInput  = document.getElementById('cosine_weight');
const jaccardInput = document.getElementById('jaccard_weight');
const totalDisplay = document.getElementById('weight-total');

function updateTotal() {
    const total = parseFloat(cosineInput.value || 0) + parseFloat(jaccardInput.value || 0);
    totalDisplay.textContent = total.toFixed(2);
    const isValid = Math.abs(total - 1.0) < 0.001;
    totalDisplay.style.color = isValid ? 'var(--success)' : 'var(--danger)';
}

cosineInput.addEventListener('input', updateTotal);
jaccardInput.addEventListener('input', updateTotal);
updateTotal();
</script>
<?= $this->endSection() ?>