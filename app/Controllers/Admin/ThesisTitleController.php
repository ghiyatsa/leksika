<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\ThesisModel;
use App\Models\TopicCategoryModel;

class ThesisTitleController extends BaseController
{
    private ThesisModel $thesisModel;

    public function __construct()
    {
        $this->thesisModel = new ThesisModel();
    }

    public function index(): string
    {
        $search  = $this->request->getGet('search') ?? '';
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $result  = $this->thesisModel->getWithRelations($search, $perPage, $page);

        return view('admin/thesis_titles/index', [
            'title'  => 'Kelola Dataset Judul',
            'result' => $result,
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        $studentModel  = new StudentModel();
        $categoryModel = new TopicCategoryModel();
        return view('admin/thesis_titles/create', [
            'title'      => 'Tambah Judul Skripsi',
            'students'   => $studentModel->orderBy('name')->findAll(),
            'categories' => $categoryModel->orderBy('category_name')->findAll(),
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'student_id'      => 'required|integer',
            'category_id'     => 'required|integer',
            'title'           => 'required|min_length[10]',
            'keyword'         => 'permit_empty',
            'abstract'        => 'permit_empty',
            'year'            => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->thesisModel->insert([
            'student_id'      => $this->request->getPost('student_id'),
            'category_id'     => $this->request->getPost('category_id'),
            'title'           => $this->request->getPost('title'),
            'keyword'         => $this->request->getPost('keyword'),
            'abstract'        => $this->request->getPost('abstract'),
            'year'            => $this->request->getPost('year') ?: null,
            'attachment_file' => null,
        ]);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil ditambahkan.');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }
        $studentModel  = new StudentModel();
        $categoryModel = new TopicCategoryModel();
        return view('admin/thesis_titles/edit', [
            'title'      => 'Edit Judul Skripsi',
            'thesis'     => $thesis,
            'students'   => $studentModel->orderBy('name')->findAll(),
            'categories' => $categoryModel->orderBy('category_name')->findAll(),
        ]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'student_id'      => 'required|integer',
            'category_id'     => 'required|integer',
            'title'           => 'required|min_length[10]',
            'keyword'         => 'permit_empty',
            'abstract'        => 'permit_empty',
            'year'            => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->thesisModel->update($id, [
            'student_id'      => $this->request->getPost('student_id'),
            'category_id'     => $this->request->getPost('category_id'),
            'title'           => $this->request->getPost('title'),
            'keyword'         => $this->request->getPost('keyword'),
            'abstract'        => $this->request->getPost('abstract'),
            'year'            => $this->request->getPost('year') ?: null,
        ]);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil diperbarui.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }

        // Delete attachment file if exists
        if ($thesis['attachment_file'] && file_exists(ROOTPATH . 'public/uploads/attachments/' . $thesis['attachment_file'])) {
            unlink(ROOTPATH . 'public/uploads/attachments/' . $thesis['attachment_file']);
        }

        $this->thesisModel->delete($id);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil dihapus.');
    }

    public function download(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis || ! $thesis['attachment_file']) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'File lampiran tidak tersedia.');
        }

        $filePath = ROOTPATH . 'public/uploads/attachments/' . $thesis['attachment_file'];
        if (! file_exists($filePath)) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'File tidak ditemukan di server.');
        }

        return $this->response->download($filePath, null);
    }
}
