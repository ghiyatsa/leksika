<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUuidToSimilarityChecks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('similarity_checks', [
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'id'
            ]
        ]);
        $this->forge->addUniqueKey('similarity_checks', 'uuid');
    }

    public function down()
    {
        $this->forge->dropColumn('similarity_checks', 'uuid');
    }
}
