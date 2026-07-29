<?php

namespace App\Controllers;

use App\Models\SimilarityCheckDetailModel;
use App\Models\SimilarityCheckModel;
use App\Models\ThesisModel;

class Home extends BaseController
{
    private ThesisModel $thesisModel;
    private SimilarityCheckModel $checkModel;
    private SimilarityCheckDetailModel $detailModel;

    public function __construct()
    {
        $this->thesisModel = model(ThesisModel::class);
        $this->checkModel  = model(SimilarityCheckModel::class);
        $this->detailModel = model(SimilarityCheckDetailModel::class);
    }

    public function index()
    {
        $totalThesis   = $this->thesisModel->countAllResults();
        $totalChecks   = $this->checkModel->countAllResults();
        $avgHybrid     = $this->detailModel
            ->select('AVG(hybrid_score) as avg_score')
            ->first()['avg_score'] ?? 0;

        return view('landing', [
            'totalThesis' => $totalThesis,
            'totalChecks' => $totalChecks,
            'avgHybrid'   => round((float) $avgHybrid * 100, 1),
            'isLoggedIn'  => (bool) session()->get('isLoggedIn'),
        ]);
    }
}

