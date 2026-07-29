<?php

namespace App\Controllers;

use App\Libraries\FirebaseAuth;
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

        $firebase = new FirebaseAuth();
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

    public function loginPost(): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->to(base_url('login'));
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout.');
    }
}
