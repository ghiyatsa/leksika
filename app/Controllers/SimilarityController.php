<?php

namespace App\Controllers;

use App\Libraries\SimilarityCalculator;
use App\Libraries\TextPreprocessor;
use App\Models\SimilarityCheckDetailModel;
use App\Models\SimilarityCheckModel;
use App\Models\ThesisModel;
use App\Models\ThresholdSettingModel;

class SimilarityController extends BaseController
{
    public function index(): string
    {
        return view('similarity/form', ['title' => 'Cek Kemiripan Judul']);
    }

    public function check(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'input_title' => 'required|min_length[10]|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $inputTitle = trim($this->request->getPost('input_title'));

        // Load dataset
        $thesisModel      = new ThesisModel();
        $thesisCollection = $thesisModel->getAllForSimilarity();

        if (empty($thesisCollection)) {
            return redirect()->back()->with('error', 'Dataset judul masih kosong. Tambahkan data judul terlebih dahulu.');
        }

        // Load threshold settings
        $thresholdModel = new ThresholdSettingModel();
        $threshold      = $thresholdModel->getSettings();

        // Run similarity
        $preprocessor = new TextPreprocessor();
        $calculator   = new SimilarityCalculator();
        $results      = $calculator->runCheck($inputTitle, '', $thesisCollection, $threshold, $preprocessor);

        // Limit results to max_similarity_results
        $maxResults = (int) ($threshold['max_similarity_results'] ?? 5);
        $results    = array_slice($results, 0, $maxResults);

        // Save check header
        $checkModel = new SimilarityCheckModel();
        $uuid       = generate_uuid();

        $checkId = $checkModel->insert([
            'user_id'       => session()->get('userId'),
            'uuid'          => $uuid,
            'input_title'   => $inputTitle,
            'input_keyword' => '',
            'checked_at'    => date('Y-m-d H:i:s'),
        ]);

        // Save check details
        $detailModel = new SimilarityCheckDetailModel();
        $details     = array_map(fn ($r) => [
            'check_id'        => $checkId,
            'thesis_title_id' => $r['thesis_title_id'],
            'cosine_score'    => $r['cosine_score'],
            'jaccard_score'   => $r['jaccard_score'],
            'hybrid_score'    => $r['hybrid_score'],
            'result_category' => $r['result_category'],
        ], $results);

        $detailModel->insertBatch($details);

        return redirect()->to(base_url('similarity/' . $uuid));
    }

    public function result(string $uuid): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $checkModel  = new SimilarityCheckModel();
        $detailModel = new SimilarityCheckDetailModel();

        $check = $checkModel->getCheckByUuid($uuid);

        if ($check === null) {
            return redirect()->to(base_url('similarity/history'))
                ->with('error', 'Data pengecekan tidak ditemukan.');
        }

        // Non-admin can only see their own checks
        if (session()->get('role') !== 'admin' && $check['user_id'] != session()->get('userId')) {
            return redirect()->to(base_url('/'))->with('error', 'Akses ditolak.');
        }

        $details        = $detailModel->getDetailsByCheckId($check['id']);
        $thresholdModel = new ThresholdSettingModel();
        $threshold      = $thresholdModel->getSettings();

        return view('similarity/result', [
            'title'     => 'Hasil Pengecekan',
            'check'     => $check,
            'details'   => $details,
            'threshold' => $threshold,
        ]);
    }

    public function history(): string
    {
        $role   = session()->get('role');
        $userId = session()->get('userId');

        $dateFrom = $this->request->getGet('date_from') ?? '';
        $dateTo   = $this->request->getGet('date_to') ?? '';

        // Validate date format
        if ($dateFrom !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if ($dateTo !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }

        $checkModel     = new SimilarityCheckModel();
        $thresholdModel = new ThresholdSettingModel();

        $checks    = $checkModel->getHistory($role === 'admin' ? null : (int) $userId, $dateFrom, $dateTo);
        $threshold = $thresholdModel->getSettings();

        return view('similarity/history', [
            'title'     => 'Riwayat Pengecekan',
            'checks'    => $checks,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'threshold' => $threshold,
        ]);
    }
}
