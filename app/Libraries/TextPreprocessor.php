<?php

namespace App\Libraries;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

/**
 * TextPreprocessor
 *
 * Performs Indonesian text preprocessing pipeline:
 *  1. Case folding
 *  2. Cleansing (remove punctuation, numbers, special chars)
 *  3. Tokenisation
 *  4. Stopword removal (Sastrawi)
 *  5. Stemming (Sastrawi)
 */
class TextPreprocessor
{
    private $stemmer;
    private $stopWordRemover;

    public function __construct()
    {
        // Sastrawi triggers E_DEPRECATED on PHP 8.2 — suppress once at construction only.
        $prevHandler = set_error_handler(static function (int $errno): bool {
            return $errno === E_DEPRECATED || $errno === E_USER_DEPRECATED;
        });

        try {
            $stemmerFactory        = new StemmerFactory();
            $this->stemmer         = $stemmerFactory->createStemmer();

            $stopWordFactory       = new StopWordRemoverFactory();
            $this->stopWordRemover = $stopWordFactory->createStopWordRemover();
        } finally {
            set_error_handler($prevHandler);
        }
    }

    /**
     * Run the full preprocessing pipeline on a string.
     *
     * @param  string $text Raw input text
     * @return array        Array of clean stemmed tokens
     */
    public function preprocess(string $text): array
    {
        // 1. Case folding
        $text = mb_strtolower($text, 'UTF-8');

        // 2. Cleansing — remove everything except letters and spaces
        $text = preg_replace('/[^a-z\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        // 3. Stopword removal (Sastrawi operates on a string)
        $text = $this->stopWordRemover->remove($text);

        // 4. Stemming
        $text = $this->stemmer->stem($text);

        // 5. Tokenise — discard single-character tokens
        return array_values(array_filter(explode(' ', $text), fn ($t) => strlen($t) > 1));
    }

    /**
     * Preprocess and return tokens as a unique set.
     */
    public function preprocessToSet(string $text): array
    {
        return array_unique($this->preprocess($text));
    }
}
