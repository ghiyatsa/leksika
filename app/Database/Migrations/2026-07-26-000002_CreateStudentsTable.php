<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_id' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('students');
    }

    public function down(): void
    {
        $this->forge->dropTable('students');
    }
}
