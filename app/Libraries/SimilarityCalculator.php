<?php

namespace App\Libraries;

/**
 * SimilarityCalculator
 *
 * Implements the hybrid TF-IDF Cosine Similarity + Jaccard Similarity calculation.
 *
 * Mathematical Definitions
 * ─────────────────────────────────────────────────────────────────────
 *  TF(t, d)     = count(t in d) / total_terms(d)
 *  IDF(t)       = log(N / df(t))          where N  = total docs, df(t) = docs containing t
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
     * Build TF-IDF vectors for a collection of tokenised documents.
     *
     * @param  array $documents  [ docId => [token, token, ...], ... ]
     * @return array             [ docId => [term => tfidf_weight, ...], ... ]
     */
    public function computeTfIdf(array $documents): array
    {
        $N    = count($documents);
        $df   = [];   // document frequency per term
        $tfs  = [];   // raw TF per document

        // ── Pass 1: TF and DF ─────────────────────────────────────────
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
     * Cosine similarity between two sparse TF-IDF vectors.
     *
     * @param  array $vecA  [term => weight]
     * @param  array $vecB  [term => weight]
     * @return float        Score in [0, 1]
     */
    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (empty($vecA) || empty($vecB)) {
            return 0.0;
        }

        // Dot product
        $dotProduct = 0.0;
        foreach ($vecA as $term => $weightA) {
            if (isset($vecB[$term])) {
                $dotProduct += $weightA * $vecB[$term];
            }
        }

        // Magnitudes
        $magA = sqrt(array_sum(array_map(fn($w) => $w ** 2, $vecA)));
        $magB = sqrt(array_sum(array_map(fn($w) => $w ** 2, $vecB)));

        if ($magA == 0.0 || $magB == 0.0) {
            return 0.0;
        }

        return round(min($dotProduct / ($magA * $magB), 1.0), 6);
    }

    /**
     * Jaccard similarity between two sets of tokens.
     *
     * @param  array $tokensA  Preprocessed token array from document A
     * @param  array $tokensB  Preprocessed token array from document B
     * @return float           Score in [0, 1]
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

        if (count($union) === 0) {
            return 0.0;
        }

        return round(count($intersection) / count($union), 6);
    }

    /**
     * Combine cosine and jaccard into a single hybrid score.
     */
    public function hybridScore(float $cosine, float $jaccard, float $w1, float $w2): float
    {
        return round(($w1 * $cosine) + ($w2 * $jaccard), 6);
    }

    /**
     * Determine result category based on threshold settings.
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
     * Orchestrate the full similarity check.
     *
     * @param  string $inputTitle    The new title to check
     * @param  string $inputKeyword  The new title's keywords
     * @param  array  $thesisCollection  Array from ThesisModel::getAllForSimilarity()
     * @param  array  $threshold     ['cosine_weight', 'jaccard_weight', 'similar_threshold', 'review_threshold']
     * @param  TextPreprocessor $preprocessor
     * @return array  Sorted results array (highest hybrid_score first)
     */
    public function runCheck(
        string $inputTitle,
        string $inputKeyword,
        array $thesisCollection,
        array $threshold,
        TextPreprocessor $preprocessor
    ): array {
        $w1 = (float) $threshold['cosine_weight'];
        $w2 = (float) $threshold['jaccard_weight'];
        $st = (float) $threshold['similar_threshold'];
        $rt = (float) $threshold['review_threshold'];

        // ── Preprocessing ─────────────────────────────────────────────
        $inputText   = $inputTitle . ' ' . $inputKeyword;
        $inputTokens = $preprocessor->preprocess($inputText);

        // Build the full corpus for TF-IDF:
        // doc 0 = input, docs 1..N = existing thesis titles
        $corpus = [0 => $inputTokens];
        foreach ($thesisCollection as $thesis) {
            $prepText = $thesis['preprocessed_text'] ?? '';
            if ($prepText !== '') {
                $corpus[$thesis['id']] = explode(' ', $prepText);
            } else {
                $docText = ($thesis['title'] ?? '') . ' ' . ($thesis['keyword'] ?? '');
                $corpus[$thesis['id']] = $preprocessor->preprocess($docText);
            }
        }

        // ── TF-IDF vectors ────────────────────────────────────────────
        $tfIdfVectors = $this->computeTfIdf($corpus);
        $inputVector  = $tfIdfVectors[0] ?? [];

        // ── Compute per-document scores ───────────────────────────────
        $results = [];
        foreach ($thesisCollection as $thesis) {
            $thesisTokens  = $corpus[$thesis['id']] ?? [];
            $thesisVector  = $tfIdfVectors[$thesis['id']] ?? [];

            $cosineScore  = $this->cosineSimilarity($inputVector, $thesisVector);
            $jaccardScore = $this->jaccardSimilarity($inputTokens, $thesisTokens);
            $hybrid       = $this->hybridScore($cosineScore, $jaccardScore, $w1, $w2);
            $category     = $this->getResultCategory($hybrid, $st, $rt);

            $results[] = [
                'thesis_title_id'  => $thesis['id'],
                'thesis_title'     => $thesis['title'],
                'thesis_keyword'   => $thesis['keyword'],
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

        // Sort descending by hybrid_score
        usort($results, fn($a, $b) => $b['hybrid_score'] <=> $a['hybrid_score']);

        return $results;
    }
}
