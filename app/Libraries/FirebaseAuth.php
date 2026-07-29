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

    // ── Token Verification ────────────────────────────────────────────────────

    public function verifyIdToken(string $idToken): ?object
    {
        $keys = $this->getPublicKeys();

        if (empty($keys)) {
            log_message('error', 'Firebase public keys could not be fetched from Google');
            return null;
        }

        try {
            $decoded = JWT::decode($idToken, $keys);
        } catch (\Exception $e) {
            log_message('error', 'Firebase JWT decode failed: ' . $e->getMessage());
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

    // ── Local User Sync ───────────────────────────────────────────────────────

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

    // ── User Management ───────────────────────────────────────────────────────

    /**
     * Create a Firebase user and auto-verify their email.
     * Returns the Firebase UID on success, null on failure.
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
            log_message('error', 'Firebase createUser failed: ' . json_encode($response));
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
                log_message('error', 'Firebase email verify failed: ' . $e->getMessage());
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

    // ── Private Helpers ───────────────────────────────────────────────────────

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
            log_message('error', 'Firebase public keys fetch failed: ' . $e->getMessage());
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
     * Returns a cached Google OAuth2 access token.
     * Token is cached in CI4 file cache with TTL 3500s (expires before the 1h Google limit).
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
            log_message('error', 'Firebase credentials file not found: ' . $path);
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
            // Cache for 3500s — safely under the 3600s Google token lifetime
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
            log_message('error', 'Firebase REST call failed: ' . $e->getMessage());
            return [];
        }
    }
}
