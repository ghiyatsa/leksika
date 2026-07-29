<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\ThesisModel;
use App\Models\TopicCategoryModel;

class ThesisTitleController extends BaseController
{
    private ThesisModel $thesisModel;
    private StudentModel $studentModel;
    private TopicCategoryModel $categoryModel;

    public function __construct()
    {
        $this->thesisModel  = model(ThesisModel::class);
        $this->studentModel = model(StudentModel::class);
        $this->categoryModel = model(TopicCategoryModel::class);
    }

    public function index(): string
    {
        $search = $this->request->getGet('search') ?? '';
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $result = $this->thesisModel->getWithRelations($search, 10, $page);

        return view('admin/thesis/index', [
            'title'  => 'Kelola Dataset Judul',
            'result' => $result,
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        return view('admin/thesis/create', [
            'title'      => 'Tambah Judul Skripsi',
            'students'   => $this->studentModel->orderBy('name')->findAll(),
            'categories' => $this->categoryModel->orderBy('category_name')->findAll(),
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'student_id'  => 'required|integer',
            'category_id' => 'required|integer',
            'title'       => 'required|min_length[10]',
            'keyword'     => 'permit_empty',
            'abstract'    => 'permit_empty',
            'year'        => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->thesisModel->insert([
            'student_id'  => $this->request->getPost('student_id'),
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'keyword'     => $this->request->getPost('keyword'),
            'abstract'    => $this->request->getPost('abstract'),
            'year'        => $this->request->getPost('year') ?: null,
        ]);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil ditambahkan.');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/thesis/edit', [
            'title'      => 'Edit Judul Skripsi',
            'thesis'     => $thesis,
            'students'   => $this->studentModel->orderBy('name')->findAll(),
            'categories' => $this->categoryModel->orderBy('category_name')->findAll(),
        ]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'student_id'  => 'required|integer',
            'category_id' => 'required|integer',
            'title'       => 'required|min_length[10]',
            'keyword'     => 'permit_empty',
            'abstract'    => 'permit_empty',
            'year'        => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->thesisModel->update($id, [
            'student_id'  => $this->request->getPost('student_id'),
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'keyword'     => $this->request->getPost('keyword'),
            'abstract'    => $this->request->getPost('abstract'),
            'year'        => $this->request->getPost('year') ?: null,
        ]);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil diperbarui.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $thesis = $this->thesisModel->find($id);
        if (! $thesis) {
            return redirect()->to(base_url('admin/thesis'))->with('error', 'Data tidak ditemukan.');
        }

        $this->thesisModel->delete($id);

        return redirect()->to(base_url('admin/thesis'))->with('success', 'Data judul berhasil dihapus.');
    }
}
