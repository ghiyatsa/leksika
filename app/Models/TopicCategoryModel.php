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
}
