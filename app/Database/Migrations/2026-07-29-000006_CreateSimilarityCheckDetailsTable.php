<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimilarityCheckDetailsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'check_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'thesis_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cosine_score'    => ['type' => 'DECIMAL', 'constraint' => '10,6', 'default' => '0.000000'],
            'jaccard_score'   => ['type' => 'DECIMAL', 'constraint' => '10,6', 'default' => '0.000000'],
            'hybrid_score'    => ['type' => 'DECIMAL', 'constraint' => '10,6', 'default' => '0.000000'],
            'result_category' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('check_id', false, false, 'idx_check_id');
        $this->forge->addKey('thesis_id', false, false, 'idx_thesis_id');
        $this->forge->addForeignKey('check_id', 'similarity_checks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('thesis_id', 'thesis', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('similarity_check_details');
    }

    public function down(): void
    {
        $this->forge->dropTable('similarity_check_details');
    }
}
