<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleAvatarToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'google_avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'avatar',
            ],
        ]);

        $this->db->table('users')
            ->where('avatar LIKE', 'http%')
            ->set('google_avatar', 'avatar', false)
            ->set('avatar', null)
            ->update();
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'google_avatar');
    }
}
