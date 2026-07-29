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
            'avatar'   => 'permit_empty|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));

        $updateData = [
            'name'  => $name,
        ];

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

    public function changePassword(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if ($user === null) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login kembali.');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');

        if ($user['firebase_uid'] && empty($user['password'])) {
            return redirect()->back()->with('error', 'Akun ini menggunakan Google Login. Tidak dapat mengubah password di sini.');
        }

        if (!empty($user['password']) && !password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $firebase = service('firebaseAuth');

        if ($user['firebase_uid']) {
            $updated = $firebase->updateUserPassword($user['firebase_uid'], $new);
            if (!$updated) {
                return redirect()->back()->with('error', 'Gagal mengubah password di Firebase. Coba lagi.');
            }
        }

        $this->userModel->update($userId, [
            'password' => password_hash($new, PASSWORD_BCRYPT),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
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
