<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThesisTitlesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title'           => ['type' => 'TEXT'],
            'keyword'         => ['type' => 'TEXT', 'null' => true],
            'abstract'        => ['type' => 'LONGTEXT', 'null' => true],
            'year'            => ['type' => 'YEAR', 'null' => true],
            'attachment_file' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'topic_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('thesis_titles');
    }

    public function down(): void
    {
        $this->forge->dropTable('thesis_titles');
    }
}
