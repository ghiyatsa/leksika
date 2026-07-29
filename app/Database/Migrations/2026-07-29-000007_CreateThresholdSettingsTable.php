<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThresholdSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'cosine_weight'          => ['type' => 'DECIMAL', 'constraint' => '4,2', 'default' => '0.60'],
            'jaccard_weight'         => ['type' => 'DECIMAL', 'constraint' => '4,2', 'default' => '0.40'],
            'similar_threshold'      => ['type' => 'DECIMAL', 'constraint' => '4,2', 'default' => '0.75'],
            'review_threshold'       => ['type' => 'DECIMAL', 'constraint' => '4,2', 'default' => '0.40'],
            'max_similarity_results' => ['type' => 'INT', 'constraint' => 11, 'default' => 5],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('threshold_settings');
    }

    public function down(): void
    {
        $this->forge->dropTable('threshold_settings');
    }
}
