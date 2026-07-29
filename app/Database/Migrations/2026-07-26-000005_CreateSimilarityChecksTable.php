<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimilarityChecksTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'input_title'   => ['type' => 'TEXT'],
            'input_keyword' => ['type' => 'TEXT', 'null' => true],
            'checked_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('similarity_checks');
    }

    public function down(): void
    {
        $this->forge->dropTable('similarity_checks');
    }
}
