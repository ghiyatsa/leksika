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

    public function getSettings(): array
    {
        $row = $this->first();
        if ($row === null) {
            return $this->defaults();
        }
        return $row;
    }

    public function initDefaultSettings(): void
    {
        if ($this->first() === null) {
            $this->insert($this->defaults());
        }
    }

    private function defaults(): array
    {
        return [
            'cosine_weight'          => 0.60,
            'jaccard_weight'         => 0.40,
            'similar_threshold'      => 0.75,
            'review_threshold'       => 0.40,
            'max_similarity_results' => 5,
        ];
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
