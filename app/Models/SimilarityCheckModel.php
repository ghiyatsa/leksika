<?php

namespace App\Models;

use CodeIgniter\Model;

class SimilarityCheckModel extends Model
{
    protected $table            = 'similarity_checks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'uuid', 'input_title', 'checked_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'checked_at';
    protected $updatedField     = '';

    public function getHistory(?int $userId = null, string $dateFrom = '', string $dateTo = ''): array
    {
        $builder = $this->db->table('similarity_checks sc')
            ->select('sc.*, u.name AS user_name, u.role, (SELECT MAX(hybrid_score) FROM similarity_check_details WHERE check_id = sc.id) AS max_hybrid_score')
            ->join('users u', 'u.id = sc.user_id')
            ->orderBy('sc.checked_at', 'DESC');

        if ($userId !== null) {
            $builder->where('sc.user_id', $userId);
        }
        if ($dateFrom !== '') {
            $builder->where('DATE(sc.checked_at) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('DATE(sc.checked_at) <=', $dateTo);
        }

        return $builder->get()->getResultArray();
    }

    public function getCheckByUuid(string $uuid): array|null
    {
        return $this->db->table('similarity_checks sc')
            ->select('sc.*, u.name AS user_name')
            ->join('users u', 'u.id = sc.user_id')
            ->where('sc.uuid', $uuid)
            ->get()
            ->getRowArray();
    }

    public function getRecentChecks(int $limit = 6): array
    {
        return $this->db->table('similarity_checks sc')
            ->select('sc.id, sc.input_title, sc.checked_at, u.name AS user_name')
            ->join('users u', 'u.id = sc.user_id')
            ->orderBy('sc.checked_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
