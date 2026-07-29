<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPreprocessedTextToThesisTitles extends Migration
{
    public function up()
    {
        $fields = [
            'preprocessed_text' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'keyword',
            ]
        ];
        $this->forge->addColumn('thesis_titles', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('thesis_titles', 'preprocessed_text');
    }
}
