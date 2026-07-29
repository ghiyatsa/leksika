<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFirebaseUidToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'firebase_uid' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'unique'     => true,
                'after'      => 'id',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'firebase_uid');
    }
}
