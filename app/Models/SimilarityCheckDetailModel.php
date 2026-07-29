<?php

namespace App\Models;

use CodeIgniter\Model;

class SimilarityCheckDetailModel extends Model
{
    protected $table            = 'similarity_check_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['check_id', 'thesis_id', 'cosine_score', 'jaccard_score', 'hybrid_score', 'result_category'];
    protected $useTimestamps    = false;

    public function getDetailsByCheckId(int $checkId): array
    {
        return $this->db->table('similarity_check_details scd')
            ->select('scd.*, tt.title AS thesis_title, tt.keyword AS thesis_keyword, tt.year,
                      s.student_id AS nim, s.name AS student_name, tc.category_name')
            ->join('thesis tt', 'tt.id = scd.thesis_id')
            ->join('students s', 's.id = tt.student_id')
            ->join('topic_categories tc', 'tc.id = tt.category_id')
            ->where('scd.check_id', $checkId)
            ->orderBy('scd.hybrid_score', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getCategoryStats(): array
    {
        return $this->db->table('similarity_check_details scd')
            ->select('scd.result_category, COUNT(*) AS count')
            ->groupBy('scd.result_category')
            ->get()
            ->getResultArray();
    }
}
