<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ThresholdSettingModel;

class ThresholdController extends BaseController
{
    public function index(): string
    {
        $model     = new ThresholdSettingModel();
        $threshold = $model->getSettings();

        return view('admin/threshold/index', [
            'title'     => 'Pengaturan Sistem',
            'threshold' => $threshold,
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'cosine_weight'          => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'jaccard_weight'         => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'similar_threshold'      => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'review_threshold'       => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'max_similarity_results' => 'required|integer|greater_than_equal_to[1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cosineWeight    = (float) $this->request->getPost('cosine_weight');
        $jaccardWeight   = (float) $this->request->getPost('jaccard_weight');
        $similarThreshold = (float) $this->request->getPost('similar_threshold');
        $reviewThreshold  = (float) $this->request->getPost('review_threshold');

        // Validate weights sum
        if (abs(($cosineWeight + $jaccardWeight) - 1.0) > 0.001) {
            return redirect()->back()->withInput()->with('error', 'Total bobot Cosine + Jaccard harus sama dengan 1.0');
        }

        // Validate threshold logic: review must be less than similar
        if ($reviewThreshold >= $similarThreshold) {
            return redirect()->back()->withInput()->with('error', 'Threshold "Perlu Ditinjau" harus lebih kecil dari threshold "Sangat Mirip".');
        }

        $model = new ThresholdSettingModel();
        $model->updateSettings([
            'cosine_weight'          => $cosineWeight,
            'jaccard_weight'         => $jaccardWeight,
            'similar_threshold'      => $similarThreshold,
            'review_threshold'       => $reviewThreshold,
            'max_similarity_results' => (int) $this->request->getPost('max_similarity_results'),
        ]);

        return redirect()->to(base_url('admin/threshold'))->with('success', 'Pengaturan threshold berhasil disimpan.');
    }
}
