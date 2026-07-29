<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'password', 'role', 'avatar', 'google_avatar', 'firebase_uid'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email',
        'role'  => 'required|in_list[admin,user]',
    ];

    public function findByEmail(string $email): array|null
    {
        return $this->where('email', $email)->first();
    }

    public function findByFirebaseUid(string $uid): array|null
    {
        return $this->where('firebase_uid', $uid)->first();
    }

    public function getPaginatedUsers(string $search = '', int $perPage = 10, int $page = 1): array
    {
        $builder = $this->db->table('users')
            ->orderBy('name', 'ASC');

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
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
