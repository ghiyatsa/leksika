<?php

namespace App\Models;

use CodeIgniter\Model;

class TopicCategoryModel extends Model
{
    protected $table            = 'topic_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['category_name', 'description'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'category_name' => 'required|min_length[3]|max_length[100]',
    ];

    public function getPaginatedCategories(string $search = '', int $perPage = 10, int $page = 1): array
    {
        $builder = $this->db->table('topic_categories')
            ->orderBy('category_name', 'ASC');

        if ($search !== '') {
            $builder->like('category_name', $search)
                ->orLike('description', $search);
        }

        $total   = $builder->countAllResults(false);
        $page    = max(1, $page);
        $offset  = ($page - 1) * $perPage;
        $results = $builder->limit($perPage, $offset)->get()->getResultArray();

        return [
            'data'    => $results,
            'total'   => $total,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }
}
