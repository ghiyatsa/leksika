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
    private SimilarityCheckModel $checkModel;
    private SimilarityCheckDetailModel $detailModel;
    private ThesisModel $thesisModel;
    private ThresholdSettingModel $thresholdModel;
    private TextPreprocessor $preprocessor;
    private SimilarityCalculator $calculator;

    public function __construct()
    {
        $this->checkModel     = model(SimilarityCheckModel::class);
        $this->detailModel    = model(SimilarityCheckDetailModel::class);
        $this->thesisModel    = model(ThesisModel::class);
        $this->thresholdModel = model(ThresholdSettingModel::class);
        $this->preprocessor   = service('textPreprocessor');
        $this->calculator     = service('similarityCalculator');
    }

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

        $thesisCollection = $this->thesisModel->getAllForSimilarity();

        if (empty($thesisCollection)) {
            return redirect()->back()->with('error', 'Dataset judul masih kosong. Tambahkan data judul terlebih dahulu.');
        }

        $threshold = $this->thresholdModel->getSettings();

        $results = $this->calculator->runCheck($inputTitle, '', $thesisCollection, $threshold, $this->preprocessor);

        $maxResults = (int) ($threshold['max_similarity_results'] ?? 5);
        $results    = array_slice($results, 0, $maxResults);

        $uuid = generate_uuid();

        $db = \Config\Database::connect();
        $db->transStart();

        $checkId = $this->checkModel->insert([
            'user_id'     => session()->get('userId'),
            'uuid'        => $uuid,
            'input_title' => $inputTitle,
        ]);

        $details = array_map(fn ($r) => [
            'check_id'  => $checkId,
            'thesis_id' => $r['thesis_id'],
            'cosine_score'    => $r['cosine_score'],
            'jaccard_score'   => $r['jaccard_score'],
            'hybrid_score'    => $r['hybrid_score'],
            'result_category' => $r['result_category'],
        ], $results);

        $this->detailModel->insertBatch($details);

        $db->transComplete();

        return redirect()->to(base_url('similarity/' . $uuid));
    }

    public function result(string $uuid): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $check = $this->checkModel->getCheckByUuid($uuid);

        if ($check === null) {
            return redirect()->to(base_url('similarity/history'))
                ->with('error', 'Data pengecekan tidak ditemukan.');
        }

        if (session()->get('role') !== 'admin' && (int) $check['user_id'] !== (int) session()->get('userId')) {
            return redirect()->to(base_url('/'))->with('error', 'Akses ditolak.');
        }

        $details  = $this->detailModel->getDetailsByCheckId($check['id']);
        $threshold = $this->thresholdModel->getSettings();

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

        if ($dateFrom !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if ($dateTo !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }

        $checks    = $this->checkModel->getHistory($role === 'admin' ? null : (int) $userId, $dateFrom, $dateTo);
        $threshold = $this->thresholdModel->getSettings();

        return view('similarity/history', [
            'title'     => 'Riwayat Pengecekan',
            'checks'    => $checks,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'threshold' => $threshold,
        ]);
    }
}
