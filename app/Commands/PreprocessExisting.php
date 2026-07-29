<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ThesisModel;
use App\Libraries\TextPreprocessor;

class PreprocessExisting extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:preprocess-existing';
    protected $description = 'Preprocesses all existing thesis titles and populates the preprocessed_text column';

    public function run(array $params)
    {
        $thesisModel = new ThesisModel();
        
        // Disable beforeInsert and beforeUpdate callbacks to avoid infinite loop or extra overhead
        // actually, we don't have to disable it because we want to use the preprocessor anyway, but we will write it directly for speed.
        
        $preprocessor = new TextPreprocessor();
        $db = \Config\Database::connect();
        
        $records = $db->table('thesis')->select('id, title, keyword')->get()->getResultArray();
        $total = count($records);
        
        if ($total === 0) {
            CLI::write('No thesis titles found.', 'yellow');
            return;
        }

        CLI::write("Preprocessing {$total} records... This may take a few seconds.", 'yellow');
        
        $count = 0;
        foreach ($records as $row) {
            $inputText = ($row['title'] ?? '') . ' ' . ($row['keyword'] ?? '');
            $tokens = $preprocessor->preprocess($inputText);
            $prepText = implode(' ', $tokens);
            
            $db->table('thesis')
                ->where('id', $row['id'])
                ->update(['preprocessed_text' => $prepText]);
            
            $count++;
            if ($count % 50 === 0 || $count === $total) {
                CLI::write("Processed {$count}/{$total}...", 'blue');
            }
        }

        CLI::write('Preprocessing completed successfully!', 'green');
    }
}

