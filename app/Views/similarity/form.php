<?= $this->extend(session()->get('role') === 'admin' ? 'layouts/main' : 'layouts/public') ?>
<?= $this->section('content') ?>

<div class="check-form-card">
    <div class="info-box" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px;">
        <?= render_icon('info', ['style' => 'width: 18px; height: 18px; flex-shrink: 0;']) ?>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            <span>Judul akan dicocokkan dengan dataset menggunakan:</span>
            <span class="badge" style="background: var(--success); color: white; font-size: 11px; font-weight: 600;">Cosine&nbsp;Similarity&nbsp;60%</span>
            <span class="badge" style="background: var(--warning); color: white; font-size: 11px; font-weight: 600;">Jaccard&nbsp;Similarity&nbsp;40%</span>
            <span style="font-size: 12.5px; color: var(--text-muted);">Skor tinggi = kemiripan kuat</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Form Pengecekan Kemiripan</div>
                <div class="card-subtitle">Masukkan judul skripsi yang ingin diperiksa</div>
            </div>
        </div>

        <div class="form-progress" id="form-progress">
            <div class="form-progress-track" id="form-progress-track"></div>
        </div>
        <div class="form-progress-label" id="form-progress-label"></div>

        <form action="<?= base_url('similarity/check') ?>" method="POST" id="similarity-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="input_title" class="form-label">
                    Judul Skripsi <span class="required">*</span>
                </label>
                <textarea
                    id="input_title"
                    name="input_title"
                    class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['input_title']) ? 'is-invalid' : '' ?>"
                    rows="3"
                    placeholder="Contoh: Rancang Bangun Sistem Informasi Perpustakaan Berbasis Web Menggunakan Framework CodeIgniter"
                    minlength="10"
                    maxlength="500"
                    required
                    aria-describedby="title-error char-count"
                ><?= esc(old('input_title')) ?></textarea>
                <div id="title-error" class="invalid-feedback" style="color: var(--danger); font-size: 12.5px; margin-top: 6px; font-weight: 600; <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['input_title'])) ? '' : 'display: none;' ?>">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['input_title'])): ?>
                        <?= esc(session()->getFlashdata('errors')['input_title']) ?>
                    <?php endif; ?>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                    <div class="form-hint" style="margin-top: 0;">Minimal 10, maksimal 500 karakter.</div>
                    <div id="char-count" class="text-sm text-muted text-mono" style="font-weight: 500;" aria-live="polite"><span id="current-count">0</span> / 500</div>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 8px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                    <?= render_icon('search') ?> Cek Kemiripan Sekarang
                </button>
                <button type="reset" class="btn btn-secondary btn-lg">
                    <?= render_icon('sync') ?> Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Method Info -->
    <div class="card methodology-card mt-4">
        <div class="card-header">
            <div class="card-title">Metodologi Perhitungan</div>
        </div>
        <div class="grid-3" style="gap: 16px;">
            <div class="method-box">
                <div style="font-weight: 700; color: var(--accent); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <?= render_icon('key', ['style' => 'width: 16px; height: 16px;']) ?> TF-IDF
                </div>
                <div class="text-muted text-sm">Menyaring kata penting dan mengabaikan kata umum (seperti "yang", "dan", "dengan") agar analisis lebih fokus.</div>
            </div>
            <div class="method-box">
                <div style="font-weight: 700; color: var(--success); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <?= render_icon('bar-chart', ['style' => 'width: 16px; height: 16px;']) ?> Cosine Similarity
                </div>
                <div class="text-muted text-sm">Mengukur kemiripan arah makna/konsep antara dua judul. Diberi kontribusi bobot sebesar <strong>60%</strong>.</div>
            </div>
            <div class="method-box">
                <div style="font-weight: 700; color: var(--warning); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <?= render_icon('check-circle', ['style' => 'width: 16px; height: 16px;']) ?> Jaccard Similarity
                </div>
                <div class="text-muted text-sm">Mengukur persentase jumlah kata yang sama persis antara dua judul. Diberi kontribusi bobot sebesar <strong>40%</strong>.</div>
            </div>
        </div>
        <div class="formula-box mt-3">
            <div>
                <strong style="color: var(--accent);">Skor Akhir Hybrid</strong>
                <span class="text-muted"> = </span>
                <span class="text-mono text-sm">(60% × Skor Cosine) + (40% × Skor Jaccard)</span>
            </div>
            <span class="badge badge-primary text-sm" style="background: var(--accent); color: white;">Weighted Fusion</span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('similarity-form');
    const titleInput = document.getElementById('input_title');
    const currentCount = document.getElementById('current-count');
    const submitBtn = document.getElementById('btn-submit');
    const resetBtn = form.querySelector('button[type="reset"]');
    const errorEl = document.getElementById('title-error');

    if (titleInput && currentCount) {
        const updateCount = () => {
            currentCount.textContent = titleInput.value.length;
        };
        titleInput.addEventListener('input', updateCount);
        updateCount();
    }

    let touched = false;
    titleInput.addEventListener('blur', () => { touched = true; validateField(); });
    titleInput.addEventListener('input', () => { if (touched) validateField(); });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const trimmed = titleInput.value.trim();

        if (trimmed.length < 10) {
            showFieldError('Judul harus minimal 10 karakter.');
            titleInput.focus();
            return;
        }

        if (trimmed !== titleInput.value) {
            titleInput.value = trimmed;
        }

        const progress = document.getElementById('form-progress');
        const track = document.getElementById('form-progress-track');
        const label = document.getElementById('form-progress-label');
        progress.classList.add('active');

        submitBtn.disabled = true;
        if (resetBtn) resetBtn.disabled = true;
        submitBtn.innerHTML = '<?= render_icon('spin', ['class' => 'anim-spin', 'style' => 'width: 14px; height: 14px; margin-right: 8px;']) ?>' + ' Membandingkan judul...';
        submitBtn.classList.add('loading');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
            });
            track.style.transform = 'scaleX(1)';
            label.textContent = 'Mengalihkan...';
            await new Promise(r => setTimeout(r, 200));
            window.location.href = res.url;
        } catch (err) {
            label.textContent = 'Gagal. Coba lagi.';
            submitBtn.disabled = false;
            if (resetBtn) resetBtn.disabled = false;
            submitBtn.innerHTML = '<?= render_icon('search') ?> Cek Kemiripan Sekarang';
            submitBtn.classList.remove('loading');
        }
    });

    function validateField() {
        const val = titleInput.value;
        if (val.length > 0 && val.length < 10) {
            showFieldError('Minimal 10 karakter. Kurang ' + (10 - val.length) + ' karakter lagi.');
        } else {
            clearFieldError();
        }
    }

    function showFieldError(msg) {
        if (errorEl) {
            errorEl.textContent = msg;
            errorEl.style.display = 'block';
            titleInput.classList.add('is-invalid');
        }
    }

    function clearFieldError() {
        if (errorEl) {
            errorEl.style.display = 'none';
            titleInput.classList.remove('is-invalid');
        }
    }
});
</script>
<?= $this->endSection() ?>
