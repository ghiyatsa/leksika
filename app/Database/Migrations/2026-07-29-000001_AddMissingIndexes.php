<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingIndexes extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE similarity_checks ADD INDEX idx_user_id (user_id)');
        $this->db->query('ALTER TABLE similarity_check_details ADD INDEX idx_check_id (check_id)');
        $this->db->query('ALTER TABLE similarity_check_details ADD INDEX idx_thesis_title_id (thesis_title_id)');
        $this->db->query('ALTER TABLE thesis_titles ADD INDEX idx_student_id (student_id)');
        $this->db->query('ALTER TABLE thesis_titles ADD INDEX idx_category_id (category_id)');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE similarity_checks DROP INDEX idx_user_id');
        $this->db->query('ALTER TABLE similarity_check_details DROP INDEX idx_check_id');
        $this->db->query('ALTER TABLE similarity_check_details DROP INDEX idx_thesis_title_id');
        $this->db->query('ALTER TABLE thesis_titles DROP INDEX idx_student_id');
        $this->db->query('ALTER TABLE thesis_titles DROP INDEX idx_category_id');
    }
}
