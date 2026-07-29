<?php

namespace App\Models;

use CodeIgniter\Model;

class ThesisModel extends Model
{
    protected $table            = 'thesis_titles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['student_id', 'category_id', 'title', 'keyword', 'abstract', 'year', 'attachment_file', 'preprocessed_text'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Callbacks for automatic preprocessing
    protected $beforeInsert     = ['setPreprocessedText'];
    protected $beforeUpdate     = ['setPreprocessedText'];

    protected $validationRules = [
        'student_id'  => 'required|integer',
        'category_id' => 'required|integer',
        'title'       => 'required|min_length[10]',
        'year'        => 'permit_empty|integer|min_length[4]|max_length[4]',
    ];

    protected function setPreprocessedText(array $data): array
    {
        if (isset($data['data']['title'])) {
            $title = $data['data']['title'] ?? '';
            $keyword = $data['data']['keyword'] ?? '';
            
            $preprocessor = new \App\Libraries\TextPreprocessor();
            $tokens = $preprocessor->preprocess($title . ' ' . $keyword);
            
            $data['data']['preprocessed_text'] = implode(' ', $tokens);
        }
        return $data;
    }

    public function getWithRelations(string $search = '', int $perPage = 10, int $page = 1): array
    {
        $builder = $this->db->table('thesis_titles tt')
            ->select('tt.*, s.student_id AS nim, s.name AS student_name, tc.category_name')
            ->join('students s', 's.id = tt.student_id')
            ->join('topic_categories tc', 'tc.id = tt.category_id')
            ->orderBy('tt.created_at', 'DESC');

        if ($search !== '') {
            $builder->groupStart()
                ->like('tt.title', $search)
                ->orLike('tt.keyword', $search)
                ->orLike('s.name', $search)
                ->orLike('s.student_id', $search)
                ->orLike('tc.category_name', $search)
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

    public function getDetailById(int $id): array|null
    {
        return $this->db->table('thesis_titles tt')
            ->select('tt.*, s.student_id AS nim, s.name AS student_name, tc.category_name')
            ->join('students s', 's.id = tt.student_id')
            ->join('topic_categories tc', 'tc.id = tt.category_id')
            ->where('tt.id', $id)
            ->get()
            ->getRowArray();
    }

    public function getAllForSimilarity(): array
    {
        return $this->db->table('thesis_titles tt')
            ->select('tt.id, tt.title, tt.keyword, tt.preprocessed_text, s.student_id AS nim, s.name AS student_name, tc.category_name, tt.year')
            ->join('students s', 's.id = tt.student_id')
            ->join('topic_categories tc', 'tc.id = tt.category_id')
            ->get()
            ->getResultArray();
    }
}
