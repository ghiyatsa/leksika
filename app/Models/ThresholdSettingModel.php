<?php

namespace App\Models;

use CodeIgniter\Model;

class ThresholdSettingModel extends Model
{
    protected $table            = 'threshold_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['cosine_weight', 'jaccard_weight', 'similar_threshold', 'review_threshold', 'max_similarity_results'];
    protected $useTimestamps    = false;

    /**
     * Always returns the first (and only) settings row.
     */
    public function getSettings(): array
    {
        $row = $this->first();
        if ($row === null) {
            // Insert defaults if missing
            $defaults = [
                'cosine_weight'          => 0.60,
                'jaccard_weight'         => 0.40,
                'similar_threshold'      => 0.75,
                'review_threshold'       => 0.40,
                'max_similarity_results' => 5,
            ];
            $this->insert($defaults);
            return $defaults;
        }
        return $row;
    }

    /**
     * Update the single settings row.
     */
    public function updateSettings(array $data): bool
    {
        $row = $this->first();
        if ($row === null) {
            return (bool) $this->insert($data);
        }
        return $this->update($row['id'], $data);
    }
}
