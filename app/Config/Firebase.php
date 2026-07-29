<?php

namespace Config;

class Firebase
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
        $this->credentialsPath = ROOTPATH . env('FIREBASE_CREDENTIALS', 'firebase-credentials.json');
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
