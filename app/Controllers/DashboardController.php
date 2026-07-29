<?php

namespace App\Controllers;

use App\Models\ThesisModel;
use App\Models\SimilarityCheckModel;
use App\Models\ThresholdSettingModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $db          = \Config\Database::connect();
        $thesisModel = new ThesisModel();
        $checkModel  = new SimilarityCheckModel();
        $userModel   = new UserModel();

        $totalThesis = $thesisModel->countAllResults();
        $totalChecks = $checkModel->countAllResults();
        $totalUsers  = $userModel->countAllResults();

        // Recent checks (last 6) for chart
        $recentChecks = $db->table('similarity_checks sc')
            ->select('sc.id, sc.input_title, sc.checked_at, u.name AS user_name')
            ->join('users u', 'u.id = sc.user_id')
            ->orderBy('sc.checked_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Category distribution for dashboard stat chart
        $topResults = $db->table('similarity_check_details scd')
            ->select('AVG(scd.hybrid_score) AS avg_hybrid, scd.result_category, COUNT(*) AS count')
            ->groupBy('scd.result_category')
            ->get()
            ->getResultArray();

        $thresholdModel = new ThresholdSettingModel();
        $settings       = $thresholdModel->getSettings();

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
