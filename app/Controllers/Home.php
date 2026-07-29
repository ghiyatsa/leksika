<?php

namespace App\Controllers;

use App\Models\ThesisModel;
use App\Models\SimilarityCheckModel;
use App\Models\SimilarityCheckDetailModel;

class Home extends BaseController
{
    public function index()
    {
        $thesisModel = new ThesisModel();
        $checkModel  = new SimilarityCheckModel();

        $totalThesis   = $thesisModel->countAllResults();
        $totalChecks   = $checkModel->countAllResults();
        $avgHybrid     = (new SimilarityCheckDetailModel())
            ->select('AVG(hybrid_score) as avg_score')
            ->first()['avg_score'] ?? 0;

        $data = [
            'totalThesis' => $totalThesis,
            'totalChecks' => $totalChecks,
            'avgHybrid'   => round((float) $avgHybrid * 100, 1),
            'isLoggedIn'  => (bool) session()->get('isLoggedIn'),
        ];

        return view('landing', $data);
    }
}

