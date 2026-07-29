<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('isLoggedIn')) {
            $target = session()->get('role') === 'admin' ? 'admin/dashboard' : '/';
            return redirect()->to(base_url($target));
        }

        $firebaseConfig = config('Firebase');

        return view('auth/login', [
            'firebaseConfig' => $firebaseConfig->clientConfig(),
        ]);
    }

    public function firebaseLogin(): ResponseInterface
    {
        $idToken = $this->request->getJsonVar('idToken');

        if (!$idToken) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Token tidak ditemukan.',
            ]);
        }

        $firebase = service('firebaseAuth');
        $decoded  = $firebase->verifyIdToken($idToken);

        if (!$decoded) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Token tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $user = $firebase->getOrCreateLocalUser($decoded);

        if (!($decoded->email_verified ?? false) && empty($user['firebase_uid'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Email belum diverifikasi. Silakan periksa email Anda.',
            ]);
        }

        session()->set([
            'isLoggedIn'       => true,
            'userId'           => $user['id'],
            'userName'         => $user['name'],
            'userEmail'        => $user['email'],
            'role'             => $user['role'],
            'userAvatar'       => $user['avatar'] ?? null,
            'userGoogleAvatar' => $user['google_avatar'] ?? null,
        ]);

        $redirect = $user['role'] === 'admin' ? 'admin/dashboard' : '/';

        return $this->response->setJSON([
            'redirect' => base_url($redirect),
            'name'     => $user['name'],
        ]);
    }

    public function register(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('isLoggedIn')) {
            $target = session()->get('role') === 'admin' ? 'admin/dashboard' : '/';
            return redirect()->to(base_url($target));
        }

        $firebaseConfig = config('Firebase');

        return view('auth/register', [
            'firebaseConfig' => $firebaseConfig->clientConfig(),
        ]);
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout.');
    }

    public function forgotPassword(): \CodeIgniter\HTTP\ResponseInterface|string
    {
        if ($this->request->is('post')) {
            $email = $this->request->getJsonVar('email');

            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Alamat email tidak valid.',
                ]);
            }

            $firebase = service('firebaseAuth');
            $sent     = $firebase->sendPasswordResetEmail($email);

            if ($sent) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tautan reset password telah dikirim ke email Anda. Periksa kotak masuk (atau folder spam).',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim email. Pastikan email terdaftar dan coba lagi.',
            ]);
        }

        return view('auth/forgot_password');
    }
}
