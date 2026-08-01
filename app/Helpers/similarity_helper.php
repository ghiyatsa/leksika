<?php

/**
 * Similarity Helper
 *
 * Utilitas layer tampilan untuk menampilkan hasil similaritas.
 * Dipisahkan dari similarity/result.php untuk mencegah error redeklarasi
 * dan menjaga view tetap bebas logika.
 */

if (! function_exists('similarity_score_color')) {
    /**
     * Mengembalikan kelas warna Bootstrap (danger|warning|success)
     * berdasarkan skor vs ambang batas yang ditentukan.
     */
    function similarity_score_color(float $score, float $similarThreshold, float $reviewThreshold): string
    {
        if ($score >= $similarThreshold) return 'danger';
        if ($score >= $reviewThreshold)  return 'warning';
        return 'success';
    }
}

if (! function_exists('similarity_category_badge')) {
    /**
     * Mengembalikan kelas badge CSS untuk string kategori hasil.
     */
    function similarity_category_badge(string $category): string
    {
        return match ($category) {
            'Sangat Mirip'   => 'badge-danger',
            'Perlu Ditinjau' => 'badge-warning',
            default          => 'badge-success',
        };
    }
}

if (! function_exists('isActive')) {
    function isActive(string $path, string $current): string {
        return str_starts_with($current, $path) ? 'active' : '';
    }
}
