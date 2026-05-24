<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private function getAccessToken(): string
{
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

    if (env('FIREBASE_CREDENTIALS_JSON')) {
        $credentialsArray = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);

        $credentials = new ServiceAccountCredentials($scopes, $credentialsArray);
    } else {
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

        $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);
    }

    $token = $credentials->fetchAuthToken();

    return $token['access_token'];
}

    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        try {
            if (!$fcmToken) {
                return false;
            }

            $accessToken = $this->getAccessToken();
            $projectId = env('FIREBASE_PROJECT_ID');

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data),
                    'android' => [
                        'priority' => 'HIGH',
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post($url, $payload);
            Log::info('FCM RESPONSE', [
    'token' => substr($fcmToken, 0, 25),
    'status' => $response->status(),
    'response' => $response->body(),
]);

if (!$response->successful()) {
    return false;
}

            return true;

        } catch (\Throwable $e) {
            Log::error('FCM EXCEPTION', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return false;
        }
    }
}