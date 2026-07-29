<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'firebase_uid' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role'         => ['type' => 'ENUM', 'constraint' => ['admin', 'user'], 'default' => 'user'],
            'avatar'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'google_avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('firebase_uid');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');
    }

    public function down(): void
    {
        $this->forge->dropTable('users');
    }
}
