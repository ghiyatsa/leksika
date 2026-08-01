<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThesisTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title'             => ['type' => 'TEXT'],
            'preprocessed_text' => ['type' => 'TEXT', 'null' => true],
            'year'              => ['type' => 'YEAR', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('student_id', false, false, 'idx_student_id');
        $this->forge->addKey('category_id', false, false, 'idx_category_id');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'topic_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('thesis');
    }

    public function down(): void
    {
        $this->forge->dropTable('thesis');
    }
}
