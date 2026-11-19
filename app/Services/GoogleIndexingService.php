<?php

namespace App\Services;

use App\Models\IndexingLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
{
    public const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

    public const INDEXING_API_ENDPOINT = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    public const INDEXING_SCOPE = 'https://www.googleapis.com/auth/indexing';

    /**
     * Check if Google Indexing API service account credentials are configured.
     */
    public function isConfigured(): bool
    {
        $creds = $this->resolveCredentials();

        return ! empty($creds['client_email']) && ! empty($creds['private_key']);
    }

    /**
     * Get decrypted credentials array containing client_email and private_key.
     *
     * @return array{client_email: string, private_key: string, project_id?: string}
     */
    public function resolveCredentials(): array
    {
        /** @var string|null $encrypted */
        $encrypted = setting('seo_google_indexing_credentials');
        if (empty($encrypted)) {
            return ['client_email' => '', 'private_key' => ''];
        }

        try {
            $decrypted = Crypt::decryptString($encrypted);
            $json = json_decode($decrypted, true);

            if (is_array($json)) {
                return [
                    'client_email' => (string) ($json['client_email'] ?? ''),
                    'private_key' => (string) ($json['private_key'] ?? ''),
                    'project_id' => (string) ($json['project_id'] ?? ''),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to decrypt Google Indexing credentials: '.$e->getMessage());
        }

        return ['client_email' => '', 'private_key' => ''];
    }

    /**
     * Save raw JSON credentials string encrypted into database.
     */
    public function saveJsonCredentials(string $jsonString): bool
    {
        $data = json_decode(trim($jsonString), true);
        if (! is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            return false;
        }

        $clean = [
            'client_email' => (string) $data['client_email'],
            'private_key' => (string) $data['private_key'],
            'project_id' => (string) ($data['project_id'] ?? ''),
        ];

        Setting::set('seo_google_indexing_credentials', Crypt::encryptString((string) json_encode($clean)), 'seo', 'encrypted');
        Setting::set('seo_google_indexing_credential_mode', 'json', 'seo', 'text');

        return true;
    }

    /**
     * Save explicit client_email and private_key fields encrypted into database.
     */
    public function saveFieldCredentials(string $clientEmail, string $privateKey): bool
    {
        $clientEmail = trim($clientEmail);
        $privateKey = trim($privateKey);

        if (empty($clientEmail) || empty($privateKey)) {
            return false;
        }

        // Format multiline private key if needed
        if (! str_contains($privateKey, '-----BEGIN PRIVATE KEY-----')) {
            return false;
        }

        $clean = [
            'client_email' => $clientEmail,
            'private_key' => $privateKey,
        ];

        Setting::set('seo_google_indexing_credentials', Crypt::encryptString((string) json_encode($clean)), 'seo', 'encrypted');
        Setting::set('seo_google_indexing_credential_mode', 'fields', 'seo', 'text');

        return true;
    }

    /**
     * Generate OAuth2 Access Token using Google Service Account JWT Assertion.
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'google_indexing_access_token';
        /** @var string|null $cachedToken */
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) {
            return $cachedToken;
        }

        $creds = $this->resolveCredentials();
        if (empty($creds['client_email']) || empty($creds['private_key'])) {
            return null;
        }

        $jwt = $this->createJwt($creds['client_email'], $creds['private_key']);
        if (! $jwt) {
            return null;
        }

        try {
            $response = Http::asForm()->timeout(10)->post(self::TOKEN_ENDPOINT, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = (string) ($data['access_token'] ?? '');
                $expiresIn = (int) ($data['expires_in'] ?? 3600);

                if (! empty($token)) {
                    Cache::put($cacheKey, $token, max(60, $expiresIn - 120));

                    return $token;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Google Indexing OAuth Token Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Submit an array of URLs to Google Indexing API v3.
     *
     * @param  array<string>  $urls
     * @param  string  $type  ('URL_UPDATED' | 'URL_DELETED')
     * @param  mixed|null  $model
     */
    public function submitUrls(array $urls, string $type = 'URL_UPDATED', $model = null): bool
    {
        $urls = array_values(array_filter(array_unique(array_map('trim', $urls))));
        if (empty($urls)) {
            return false;
        }

        $token = $this->getAccessToken();
        if (! $token) {
            foreach ($urls as $url) {
                IndexingLog::create([
                    'protocol' => 'google',
                    'url' => $url,
                    'status_code' => 401,
                    'response' => 'Failed to obtain Google OAuth access token. Please check Service Account credentials.',
                    'request_time' => now(),
                    'entity_type' => $model ? get_class($model) : null,
                    'entity_id' => $model && isset($model->id) ? (int) $model->id : null,
                ]);
            }

            return false;
        }

        $allSuccess = true;

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                    ])
                    ->post(self::INDEXING_API_ENDPOINT, [
                        'url' => $url,
                        'type' => $type,
                    ]);

                $status = $response->status();
                $responseBody = $response->body();
                $success = ($status === 200);

                if (! $success) {
                    $allSuccess = false;
                } else {
                    Setting::set('seo_google_indexing_last_ping_at', now()->toIso8601String(), 'seo', 'text');
                }

                IndexingLog::create([
                    'protocol' => 'google',
                    'url' => $url,
                    'status_code' => $status,
                    'response' => $responseBody,
                    'request_time' => now(),
                    'entity_type' => $model ? get_class($model) : null,
                    'entity_id' => $model && isset($model->id) ? (int) $model->id : null,
                ]);
            } catch (\Throwable $e) {
                $allSuccess = false;
                IndexingLog::create([
                    'protocol' => 'google',
                    'url' => $url,
                    'status_code' => 0,
                    'response' => $e->getMessage(),
                    'request_time' => now(),
                    'entity_type' => $model ? get_class($model) : null,
                    'entity_id' => $model && isset($model->id) ? (int) $model->id : null,
                ]);
            }
        }

        return $allSuccess;
    }

    /**
     * Auto ping Google Indexing API for an entity.
     *
     * @param  mixed  $entity
     */
    public function pingEntity($entity, string $type = 'URL_UPDATED'): bool
    {
        $enabled = (bool) setting('seo_google_indexing_enabled', true);
        $autoPing = (bool) setting('seo_google_indexing_auto_ping', true);

        if (! $enabled || ! $autoPing || ! $this->isConfigured()) {
            return false;
        }

        $url = null;
        if (method_exists($entity, 'getPublicUrl')) {
            $url = $entity->getPublicUrl();
        } elseif (isset($entity->slug)) {
            $url = url('/'.$entity->slug);
        }

        if (! $url) {
            return false;
        }

        $cacheKey = 'google_ping_'.md5($url);
        if (Cache::has($cacheKey)) {
            return true;
        }

        $success = $this->submitUrls([$url], $type, $entity);
        if ($success) {
            Cache::put($cacheKey, true, 60);
        }

        return $success;
    }

    /**
     * Get count of Google Indexing API requests sent today.
     */
    public function getTodayRequestCount(): int
    {
        return IndexingLog::query()
            ->where('protocol', 'google')
            ->whereDate('request_time', now()->toDateString())
            ->count();
    }

    /**
     * Helper to construct signed RS256 JWT assertion.
     */
    private function createJwt(string $clientEmail, string $privateKey): ?string
    {
        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $payload = [
            'iss' => $clientEmail,
            'scope' => self::INDEXING_SCOPE,
            'aud' => self::TOKEN_ENDPOINT,
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $base64Header = $this->base64UrlEncode((string) json_encode($header));
        $base64Payload = $this->base64UrlEncode((string) json_encode($payload));

        $signatureInput = $base64Header.'.'.$base64Payload;
        $signature = '';

        $key = openssl_pkey_get_private($privateKey);
        if (! $key) {
            Log::error('Invalid OpenSSL Private Key provided for Google Indexing');

            return null;
        }

        $signed = openssl_sign($signatureInput, $signature, $key, OPENSSL_ALGO_SHA256);
        if (! $signed) {
            return null;
        }

        return $signatureInput.'.'.$this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
