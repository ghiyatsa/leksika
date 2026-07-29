<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TopicCategoryModel;

class CategoryController extends BaseController
{
    private TopicCategoryModel $model;

    public function __construct()
    {
        $this->model = new TopicCategoryModel();
    }

    public function index(): string
    {
        return view('admin/categories/index', [
            'title'      => 'Kelola Kategori Topik',
            'categories' => $this->model->orderBy('category_name')->findAll(),
        ]);
    }

    public function create(): string
    {
        return view('admin/categories/create', ['title' => 'Tambah Kategori']);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'category_name' => 'required|min_length[3]|max_length[100]',
            'description'   => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'category_name' => $this->request->getPost('category_name'),
            'description'   => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $category = $this->model->find($id);
        if (! $category) {
            return redirect()->to(base_url('admin/categories'))->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/categories/edit', ['title' => 'Edit Kategori', 'category' => $category]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/categories'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'category_name' => 'required|min_length[3]|max_length[100]',
            'description'   => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'category_name' => $this->request->getPost('category_name'),
            'description'   => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/categories'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->delete($id);
        return redirect()->to(base_url('admin/categories'))->with('success', 'Kategori berhasil dihapus.');
    }
}
