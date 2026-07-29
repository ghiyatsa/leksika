<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimilarityChecksTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid'        => ['type' => 'VARCHAR', 'constraint' => 36, 'null' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'input_title' => ['type' => 'TEXT'],
            'checked_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey('user_id', false, false, 'idx_user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('similarity_checks');
    }

    public function down(): void
    {
        $this->forge->dropTable('similarity_checks');
    }
}
