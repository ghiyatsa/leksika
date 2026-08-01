<?php

namespace App\Libraries;

/**
 * SimilarityCalculator
 *
 * Implementasi perhitungan hybrid TF-IDF Cosine Similarity + Jaccard Similarity.
 *
 * Definisi Matematis
 * ─────────────────────────────────────────────────────────────────────
 *  TF(t, d)     = count(t in d) / total_terms(d)
 *  IDF(t)       = log(N / df(t))          N  = total dokumen, df(t) = dokumen mengandung t
 *  TF-IDF(t,d)  = TF(t,d) × IDF(t)
 *
 *  Cosine(A,B)  = (A · B) / (‖A‖ × ‖B‖)
 *  Jaccard(A,B) = |A ∩ B| / |A ∪ B|
 *  Hybrid       = w1 × Cosine + w2 × Jaccard
 * ─────────────────────────────────────────────────────────────────────
 */
class SimilarityCalculator
{
    /**
     * Bangun vektor TF-IDF untuk kumpulan dokumen yang sudah di-tokenisasi.
     *
     * @param  array $documents  [ docId => [token, token, ...], ... ]
     * @return array             [ docId => [term => bobot_tfidf, ...], ... ]
     */
    public function computeTfIdf(array $documents): array
    {
        $N    = count($documents);
        $df   = [];   // frekuensi dokumen per term
        $tfs  = [];   // TF mentah per dokumen

        // ── Pass 1: TF dan DF ──────────────────────────────────────────
        foreach ($documents as $docId => $tokens) {
            $termCount = count($tokens);
            if ($termCount === 0) {
                $tfs[$docId] = [];
                continue;
            }
            $freq = array_count_values($tokens);
            foreach ($freq as $term => $count) {
                $tfs[$docId][$term] = $count / $termCount;   // TF
                $df[$term]          = ($df[$term] ?? 0) + 1; // DF
            }
        }

        // ── Pass 2: TF-IDF ────────────────────────────────────────────
        $vectors = [];
        foreach ($tfs as $docId => $termTf) {
            $vectors[$docId] = [];
            foreach ($termTf as $term => $tf) {
                $idf = log($N / ($df[$term] ?? 1));           // IDF
                $vectors[$docId][$term] = $tf * $idf;         // TF-IDF
            }
        }

        return $vectors;
    }

    /**
     * Similaritas kosinus antara dua vektor TF-IDF sparse.
     *
     * @param  array $vecA  [term => bobot]
     * @param  array $vecB  [term => bobot]
     * @return float        Skor dalam [0, 1]
     */
    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (empty($vecA) || empty($vecB)) {
            return 0.0;
        }

        // Produk dot
        $dotProduct = 0.0;
        foreach ($vecA as $term => $weightA) {
            if (isset($vecB[$term])) {
                $dotProduct += $weightA * $vecB[$term];
            }
        }

        // Magnitudo
        $magA = sqrt(array_sum(array_map(fn($w) => $w ** 2, $vecA)));
        $magB = sqrt(array_sum(array_map(fn($w) => $w ** 2, $vecB)));

        if ($magA == 0.0 || $magB == 0.0) {
            return 0.0;
        }

        return round(min($dotProduct / ($magA * $magB), 1.0), 6);
    }

    /**
     * Similaritas Jaccard antara dua himpunan token.
     *
     * @param  array $tokensA  Array token terproses dari dokumen A
     * @param  array $tokensB  Array token terproses dari dokumen B
     * @return float           Skor dalam [0, 1]
     */
    public function jaccardSimilarity(array $tokensA, array $tokensB): float
    {
        if (empty($tokensA) && empty($tokensB)) {
            return 1.0;
        }
        if (empty($tokensA) || empty($tokensB)) {
            return 0.0;
        }

        $setA        = array_unique($tokensA);
        $setB        = array_unique($tokensB);
        $intersection = array_intersect($setA, $setB);
        $union        = array_unique(array_merge($setA, $setB));

        return round(count($intersection) / count($union), 6);
    }

    /**
     * Gabungkan cosine dan jaccard menjadi satu skor hybrid.
     */
    public function hybridScore(float $cosine, float $jaccard, float $w1, float $w2): float
    {
        return round(($w1 * $cosine) + ($w2 * $jaccard), 6);
    }

    /**
     * Tentukan kategori hasil berdasarkan ambang batas.
     */
    public function getResultCategory(float $hybridScore, float $similarThreshold, float $reviewThreshold): string
    {
        if ($hybridScore >= $similarThreshold) {
            return 'Sangat Mirip';
        } elseif ($hybridScore >= $reviewThreshold) {
            return 'Perlu Ditinjau';
        } else {
            return 'Aman';
        }
    }

    /**
     * Jalankan pemeriksaan similaritas secara lengkap.
     *
     * @param  string $inputTitle        Judul baru yang akan diperiksa
     * @param  array  $thesisCollection  Array dari ThesisModel::getAllForSimilarity()
     * @param  array  $threshold         ['cosine_weight', 'jaccard_weight', 'similar_threshold', 'review_threshold']
     * @param  TextPreprocessor $preprocessor
     * @return array  Array hasil yang diurutkan (hybrid_score tertinggi pertama)
     */
    public function runCheck(
        string $inputTitle,
        array $thesisCollection,
        array $threshold,
        TextPreprocessor $preprocessor
    ): array {
        $w1 = (float) $threshold['cosine_weight'];
        $w2 = (float) $threshold['jaccard_weight'];
        $st = (float) $threshold['similar_threshold'];
        $rt = (float) $threshold['review_threshold'];

        // ── Pra-pemrosesan ────────────────────────────────────────────
        $inputText   = $inputTitle;
        $inputTokens = $preprocessor->preprocess($inputText);

        // Bangun korpus lengkap untuk TF-IDF:
        // doc 0 = input, docs 1..N = judul skripsi yang sudah ada
        $corpus = [0 => $inputTokens];
        foreach ($thesisCollection as $thesis) {
            $prepText = $thesis['preprocessed_text'] ?? '';
            if ($prepText !== '') {
                $corpus[$thesis['id']] = explode(' ', $prepText);
            } else {
                $docText = $thesis['title'] ?? '';
                $corpus[$thesis['id']] = $preprocessor->preprocess($docText);
            }
        }

        // ── Vektor TF-IDF ────────────────────────────────────────────
        $tfIdfVectors = $this->computeTfIdf($corpus);
        $inputVector  = $tfIdfVectors[0] ?? [];

        // ── Hitung skor per dokumen ───────────────────────────────────
        $results = [];
        foreach ($thesisCollection as $thesis) {
            $thesisTokens  = $corpus[$thesis['id']] ?? [];
            $thesisVector  = $tfIdfVectors[$thesis['id']] ?? [];

            $cosineScore  = $this->cosineSimilarity($inputVector, $thesisVector);
            $jaccardScore = $this->jaccardSimilarity($inputTokens, $thesisTokens);
            $hybrid       = $this->hybridScore($cosineScore, $jaccardScore, $w1, $w2);
            $category     = $this->getResultCategory($hybrid, $st, $rt);

            $results[] = [
                'thesis_id'        => $thesis['id'],
                'thesis_title'     => $thesis['title'],
                'nim'              => $thesis['nim'],
                'student_name'     => $thesis['student_name'],
                'category_name'    => $thesis['category_name'],
                'year'             => $thesis['year'],
                'cosine_score'     => $cosineScore,
                'jaccard_score'    => $jaccardScore,
                'hybrid_score'     => $hybrid,
                'result_category'  => $category,
            ];
        }

        // Urutkan menurun berdasarkan hybrid_score
        usort($results, fn($a, $b) => $b['hybrid_score'] <=> $a['hybrid_score']);

        return $results;
    }
}
