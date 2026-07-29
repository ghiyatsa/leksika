<?php

/**
 * Similarity Helper
 *
 * View-layer utilities for similarity result display.
 * Extracted from similarity/result.php to prevent redeclaration errors
 * and keep views logic-free.
 */

if (! function_exists('similarity_score_color')) {
    /**
     * Returns Bootstrap-style color class (danger|warning|success)
     * based on score vs. threshold settings.
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
     * Returns CSS badge class for a result category string.
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

if (! function_exists('similarity_history_url')) {
    /**
     * Returns the correct history URL based on user role.
     */
    function similarity_history_url(): string
    {
        return base_url('similarity/history');
    }
}
