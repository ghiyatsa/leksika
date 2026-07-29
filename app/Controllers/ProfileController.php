<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if ($user === null) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login kembali.');
        }

        return view('profile/index', [
            'title' => 'Profil Saya',
            'user'  => $user,
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if ($user === null) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login kembali.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'password' => 'permit_empty|min_length[6]',
            'avatar'   => 'permit_empty|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name     = trim($this->request->getPost('name'));
        $password = $this->request->getPost('password');

        $updateData = [
            'name'  => $name,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $firebase = service('firebaseAuth');

        if (!empty($password) && $user['firebase_uid']) {
            $firebase->updateUserPassword($user['firebase_uid'], $password);
        }

        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            if (!empty($user['avatar'])) {
                $oldPath = FCPATH . 'uploads/avatars/' . $user['avatar'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newName = $avatarFile->getRandomName();
            $avatarFile->move(FCPATH . 'uploads/avatars', $newName);
            $updateData['avatar'] = $newName;
        }

        $this->userModel->update($userId, $updateData);

        $user = $this->userModel->find($userId);

        session()->set([
            'userName'         => $name,
            'userAvatar'       => $user['avatar'] ?? null,
            'userGoogleAvatar' => $user['google_avatar'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function deleteAvatar(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if ($user === null) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login kembali.');
        }

        if (!empty($user['avatar'])) {
            $oldPath = FCPATH . 'uploads/avatars/' . $user['avatar'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $this->userModel->update($userId, ['avatar' => null]);

        session()->set('userAvatar', null);

        return redirect()->back()->with('success', 'Avatar berhasil dihapus.');
    }

    public function deleteAccount(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if ($user === null) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login kembali.');
        }

        if (!empty($user['avatar'])) {
            $avatarPath = FCPATH . 'uploads/avatars/' . $user['avatar'];
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        if ($user['firebase_uid']) {
            $firebase = service('firebaseAuth');
            $firebase->deleteUser($user['firebase_uid']);
        }

        $this->userModel->delete($userId);
        session()->destroy();

        return redirect()->to(base_url('/'))->with('success', 'Akun Anda berhasil dihapus.');
    }
}
