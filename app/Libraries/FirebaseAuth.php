<?php

namespace App\Libraries;

use Config\Firebase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;

class FirebaseAuth
{
    private Firebase $config;
    private ?array   $cachedKeys = null;

    public function __construct()
    {
        $this->config = config('Firebase');
    }

    // ── Verifikasi Token ──────────────────────────────────────────────────────

    public function verifyIdToken(string $idToken): ?object
    {
        $keys = $this->getPublicKeys();

        if (empty($keys)) {
            log_message('error', 'Kunci publik Firebase gagal diambil dari Google');
            return null;
        }

        try {
            $decoded = JWT::decode($idToken, $keys);
        } catch (\Exception $e) {
            log_message('error', 'Dekode JWT Firebase gagal: ' . $e->getMessage());
            return null;
        }

        if ($decoded->aud !== $this->config->projectId) {
            return null;
        }
        if ($decoded->iss !== "https://securetoken.google.com/{$this->config->projectId}") {
            return null;
        }
        if (isset($decoded->exp) && $decoded->exp < time()) {
            return null;
        }

        return $decoded;
    }

    // ── Sinkronisasi Pengguna Lokal ───────────────────────────────────────────

    public function getOrCreateLocalUser(object $token): array
    {
        $model = new UserModel();
        $uid   = $token->sub;
        $email = $token->email ?? '';
        $name  = $token->name ?? explode('@', $email)[0];

        $user = $model->findByFirebaseUid($uid);
        if ($user) {
            return $user;
        }

        $user = $model->findByEmail($email);
        if ($user) {
            $model->update($user['id'], ['firebase_uid' => $uid]);
            $user['firebase_uid'] = $uid;
            return $user;
        }

        $newId = $model->insert([
            'name'          => $name,
            'email'         => $email,
            'role'          => 'user',
            'firebase_uid'  => $uid,
            'google_avatar' => $token->picture ?? null,
        ]);

        return $model->find($newId) ?? [];
    }

    // ── Manajemen Pengguna ─────────────────────────────────────────────────────

    /**
     * Buat pengguna Firebase dan verifikasi email otomatis.
     * Mengembalikan UID Firebase jika sukses, null jika gagal.
     */
    public function createUser(string $email, string $password, string $displayName = ''): ?string
    {
        $payload = ['email' => $email, 'password' => $password, 'returnSecureToken' => true];
        if ($displayName !== '') {
            $payload['displayName'] = $displayName;
        }

        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$this->config->apiKey}",
            $payload
        );

        if (! isset($response['localId'])) {
            log_message('error', 'Firebase createUser gagal: ' . json_encode($response));
            return null;
        }

        $uid         = $response['localId'];
        $accessToken = $this->getAccessToken();

        if ($accessToken) {
            $client = \Config\Services::curlrequest();
            try {
                $client->post('https://identitytoolkit.googleapis.com/v1/accounts:update', [
                    'headers' => ['Authorization' => "Bearer {$accessToken}"],
                    'json'    => ['localId' => $uid, 'emailVerified' => true],
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Verifikasi email Firebase gagal: ' . $e->getMessage());
            }
        }

        return $uid;
    }

    public function updateUserEmail(string $uid, string $newEmail): bool
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:update?key={$this->config->apiKey}",
            ['localId' => $uid, 'email' => $newEmail, 'returnSecureToken' => false]
        );

        return ! isset($response['error']);
    }

    public function updateUserPassword(string $uid, string $newPassword): bool
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:update?key={$this->config->apiKey}",
            ['localId' => $uid, 'password' => $newPassword, 'returnSecureToken' => false]
        );

        return ! isset($response['error']);
    }

    public function deleteUser(string $uid): bool
    {
        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            return false;
        }

        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:delete?key={$this->config->apiKey}",
            ['localId' => $uid]
        );

        return ! isset($response['error']);
    }

    public function sendEmailVerification(string $idToken): bool
    {
        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key={$this->config->apiKey}",
            ['requestType' => 'VERIFY_EMAIL', 'idToken' => $idToken]
        );

        return ! isset($response['error']);
    }

    public function sendPasswordResetEmail(string $email): bool
    {
        $response = $this->postJson(
            "https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key={$this->config->apiKey}",
            ['requestType' => 'PASSWORD_RESET', 'email' => $email]
        );

        return ! isset($response['error']);
    }

    // ── Helper Privat ─────────────────────────────────────────────────────────

    private function getPublicKeys(): array
    {
        if ($this->cachedKeys !== null) {
            return $this->cachedKeys;
        }

        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
            $certs    = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            log_message('error', 'Ambil kunci publik Firebase gagal: ' . $e->getMessage());
            return [];
        }

        if (! $certs) {
            return [];
        }

        $keys = [];
        foreach ($certs as $kid => $cert) {
            $keys[$kid] = new Key($cert, 'RS256');
        }

        $this->cachedKeys = $keys;
        return $this->cachedKeys;
    }

    /**
     * Mengembalikan token akses OAuth2 Google yang di-cache.
     * Token di-cache di cache file CI4 dengan TTL 3500 detik (kedaluwarsa sebelum batas 1 jam Google).
     */
    private function getAccessToken(): ?string
    {
        $cache     = \Config\Services::cache();
        $cacheKey  = 'firebase_oauth_token';
        $cached    = $cache->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $path = $this->config->credentialsPath;
        if ($path === '' || ! file_exists($path)) {
            log_message('error', 'File kredensial Firebase tidak ditemukan: ' . $path);
            return null;
        }

        $serviceAccount = json_decode(file_get_contents($path), true);
        if (! $serviceAccount || ! isset($serviceAccount['client_email'])) {
            return null;
        }

        $now = time();
        $jwt = JWT::encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/identitytoolkit',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ], $serviceAccount['private_key'], 'RS256');

        $client   = \Config\Services::curlrequest();
        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        $token  = $result['access_token'] ?? null;

        if ($token !== null) {
            // Cache 3500 detik — aman di bawah batas 3600 detik Google
            $cache->save($cacheKey, $token, 3500);
        }

        return $token;
    }

    private function postJson(string $url, array $data): array
    {
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post($url, ['json' => $data]);
            return json_decode($response->getBody(), true) ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Panggilan REST Firebase gagal: ' . $e->getMessage());
            return [];
        }
    }
}
