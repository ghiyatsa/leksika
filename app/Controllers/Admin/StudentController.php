<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;

class StudentController extends BaseController
{
    private StudentModel $model;

    public function __construct()
    {
        $this->model = new StudentModel();
    }

    public function index(): string
    {
        $search  = $this->request->getGet('search') ?? '';
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $result  = $this->model->getPaginatedStudents($search, $perPage, $page);

        return view('admin/students/index', [
            'title'  => 'Kelola Data Mahasiswa',
            'result' => $result,
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        return view('admin/students/create', ['title' => 'Tambah Mahasiswa']);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'student_id' => 'required|max_length[20]|is_unique[students.student_id]',
            'name'       => 'required|min_length[3]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'student_id' => $this->request->getPost('student_id'),
            'name'       => $this->request->getPost('name'),
        ]);

        return redirect()->to(base_url('admin/students'))->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $student = $this->model->find($id);
        if (! $student) {
            return redirect()->to(base_url('admin/students'))->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/students/edit', ['title' => 'Edit Mahasiswa', 'student' => $student]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $student = $this->model->find($id);
        if (! $student) {
            return redirect()->to(base_url('admin/students'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'student_id' => "required|max_length[20]|is_unique[students.student_id,id,{$id}]",
            'name'       => 'required|min_length[3]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'student_id' => $this->request->getPost('student_id'),
            'name'       => $this->request->getPost('name'),
        ]);

        return redirect()->to(base_url('admin/students'))->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/students'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->delete($id);
        return redirect()->to(base_url('admin/students'))->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
