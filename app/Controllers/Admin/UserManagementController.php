<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserManagementController extends BaseController
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = model(UserModel::class);
    }

    public function index(): string
    {
        $search = $this->request->getGet('search') ?? '';
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $result = $this->model->getPaginatedUsers($search, 10, $page);

        return view('admin/users/index', [
            'title'  => 'Manajemen Akun Pengguna',
            'result' => $result,
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        return view('admin/users/create', ['title' => 'Tambah Pengguna']);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,user]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $name     = $this->request->getPost('name');

        $firebase = service('firebaseAuth');
        $uid      = $firebase->createUser($email, $password, $name);

        if (!$uid) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun Firebase. Email mungkin sudah terdaftar.');
        }

        $this->model->insert([
            'name'         => $name,
            'email'        => $email,
            'password'     => password_hash($password, PASSWORD_BCRYPT),
            'role'         => $this->request->getPost('role'),
            'firebase_uid' => $uid,
        ]);

        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil ditambahkan. Email verifikasi telah dikirim.');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->model->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/users/edit', ['title' => 'Edit Pengguna', 'user' => $user]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->model->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'     => 'required|in_list[admin,user]',
            'password' => 'permit_empty|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role'),
        ];

        $newEmail    = $this->request->getPost('email');
        $newPassword = $this->request->getPost('password');

        $firebase = service('firebaseAuth');

        if ($newEmail !== $user['email'] && $user['firebase_uid']) {
            $firebase->updateUserEmail($user['firebase_uid'], $newEmail);
        }

        if (!empty($newPassword) && $user['firebase_uid']) {
            $firebase->updateUserPassword($user['firebase_uid'], $newPassword);
        }

        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->model->update($id, $updateData);

        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($id == session()->get('userId')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->model->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Data tidak ditemukan.');
        }

        if ($user['firebase_uid']) {
            $firebase = service('firebaseAuth');
            $firebase->deleteUser($user['firebase_uid']);
        }

        $this->model->delete($id);
        return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil dihapus.');
    }
}
