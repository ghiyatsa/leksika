<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['student_id', 'name'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'student_id' => 'required|max_length[20]',
        'name'       => 'required|min_length[3]|max_length[150]',
    ];

    public function search(string $keyword): array
    {
        return $this->groupStart()
                    ->like('student_id', $keyword)
                    ->orLike('name', $keyword)
                    ->groupEnd()
                    ->findAll();
    }

    public function getPaginatedStudents(string $search = '', int $perPage = 10, int $page = 1): array
    {
        $builder = $this->db->table('students')
            ->orderBy('name', 'ASC');

        if ($search !== '') {
            $builder->groupStart()
                ->like('student_id', $search)
                ->orLike('name', $search)
                ->groupEnd();
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
