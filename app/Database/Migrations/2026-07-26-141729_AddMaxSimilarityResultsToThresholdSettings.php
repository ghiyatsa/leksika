<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaxSimilarityResultsToThresholdSettings extends Migration
{
    public function up()
    {
        $fields = [
            'max_similarity_results' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 5,
                'after' => 'review_threshold',
            ]
        ];
        $this->forge->addColumn('threshold_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('threshold_settings', 'max_similarity_results');
    }
}
