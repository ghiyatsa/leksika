<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Firebase extends BaseConfig
{
    public string $projectId;
    public string $apiKey;
    public string $authDomain;
    public string $credentialsPath;

    public function __construct()
    {
        $this->projectId       = env('FIREBASE_PROJECT_ID', '');
        $this->apiKey          = env('FIREBASE_API_KEY', '');
        $this->authDomain      = env('FIREBASE_AUTH_DOMAIN', '');
        $this->credentialsPath = $this->resolveCredentialsPath();
    }

    private function resolveCredentialsPath(): string
    {
        $creds = env('FIREBASE_CREDENTIALS', 'firebase-credentials.json');

        if ($creds === '') {
            return '';
        }

        if (str_starts_with($creds, '/')) {
            return $creds;
        }

        return ROOTPATH . $creds;
    }


    public function isValid(): bool
    {
        return $this->projectId !== '' && $this->apiKey !== '';
    }

    public function clientConfig(): array
    {
        return [
            'apiKey'            => $this->apiKey,
            'authDomain'        => $this->authDomain,
            'projectId'         => $this->projectId,
        ];
    }
}
