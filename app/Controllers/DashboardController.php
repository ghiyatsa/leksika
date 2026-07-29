<?php

namespace App\Controllers;

use App\Models\SimilarityCheckDetailModel;
use App\Models\SimilarityCheckModel;
use App\Models\ThesisModel;
use App\Models\ThresholdSettingModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    private ThesisModel $thesisModel;
    private SimilarityCheckModel $checkModel;
    private UserModel $userModel;
    private ThresholdSettingModel $thresholdModel;

    public function __construct()
    {
        $this->thesisModel    = model(ThesisModel::class);
        $this->checkModel     = model(SimilarityCheckModel::class);
        $this->userModel      = model(UserModel::class);
        $this->thresholdModel = model(ThresholdSettingModel::class);
    }

    public function index(): string
    {
        $totalThesis = $this->thesisModel->countAllResults();
        $totalChecks = $this->checkModel->countAllResults();
        $totalUsers  = $this->userModel->countAllResults();

        $recentChecks = $this->checkModel->getRecentChecks(6);
        $topResults   = model(SimilarityCheckDetailModel::class)->getCategoryStats();
        $settings     = $this->thresholdModel->getSettings();

        return view('dashboard/index', [
            'title'        => 'Dashboard',
            'totalThesis'  => $totalThesis,
            'totalChecks'  => $totalChecks,
            'totalUsers'   => $totalUsers,
            'recentChecks' => $recentChecks,
            'topResults'   => $topResults,
            'settings'     => $settings,
        ]);
    }
}
