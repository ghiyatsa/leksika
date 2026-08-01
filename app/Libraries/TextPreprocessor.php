<?php

namespace App\Libraries;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

/**
 * TextPreprocessor
 *
 * Melakukan pipeline pra-pemrosesan teks bahasa Indonesia:
 *  1. Case folding
 *  2. Cleansing (hapus tanda baca, angka, karakter khusus)
 *  3. Tokenisasi
 *  4. Stopword removal (Sastrawi)
 *  5. Stemming (Sastrawi)
 */
class TextPreprocessor
{
    private $stemmer;
    private $stopWordRemover;

    public function __construct()
    {
        // Sastrawi memicu E_DEPRECATED di PHP 8.2 — ditekan sekali saat konstruktor.
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
     * Jalankan pipeline pra-pemrosesan lengkap pada sebuah string.
     *
     * @param  string $text Teks masukan mentah
     * @return array        Array token bersih yang sudah di-stem
     */
    public function preprocess(string $text): array
    {
        $prevHandler = set_error_handler(static function (int $errno): bool {
            return $errno === E_DEPRECATED || $errno === E_USER_DEPRECATED;
        });

        try {
            // 1. Case folding
            $text = mb_strtolower($text, 'UTF-8');

            // 2. Cleansing — hapus semua kecuali huruf dan spasi
            $text = preg_replace('/[^a-z\s]/u', ' ', $text);
            $text = preg_replace('/\s+/', ' ', trim($text));

            // 3. Stopword removal (Sastrawi bekerja pada string)
            $text = $this->stopWordRemover->remove($text);

            // 4. Stemming
            $text = $this->stemmer->stem($text);

            // 5. Tokenisasi — buang token satu karakter
            return array_values(array_filter(explode(' ', $text), fn ($t) => strlen($t) > 1));
        } finally {
            set_error_handler($prevHandler);
        }
    }

}
