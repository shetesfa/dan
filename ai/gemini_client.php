<?php
/**
 * Thin wrapper around Google's Gemini API (generateContent).
 * Chosen because it has a genuinely free tier — no card needed — which
 * fits a self-serve setup where the owner pastes their own key in the
 * admin panel instead of a key being baked into the code.
 *
 * $history is an array of ['role' => 'user'|'model', 'text' => '...']
 * Returns:
 *   ['ok' => true,  'text' => '...']
 *   ['ok' => false, 'error_type' => 'quota'|'auth'|'network'|'other', 'message' => '...']
 */
function call_gemini(string $api_key, string $model, string $system_prompt, array $history, string $user_message, ?string $image_base64 = null, ?string $image_mime = null): array {
    if (empty($api_key)) {
        return ['ok' => false, 'error_type' => 'auth', 'message' => 'No API key configured.'];
    }

    $contents = [];
    foreach ($history as $h) {
        $contents[] = [
            'role' => $h['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $h['text']]],
        ];
    }

    $userParts = [['text' => $user_message]];
    if ($image_base64 && $image_mime) {
        $userParts[] = [
            'inline_data' => [
                'mime_type' => $image_mime,
                'data' => $image_base64,
            ],
        ];
    }
    $contents[] = ['role' => 'user', 'parts' => $userParts];

    $payload = [
        'system_instruction' => ['parts' => [['text' => $system_prompt]]],
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.6,
            'maxOutputTokens' => 500,
        ],
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . rawurlencode($model) . ":generateContent";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $api_key,
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 25,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'error_type' => 'network', 'message' => $curlErr ?: 'Could not reach Gemini API.'];
    }

    $data = json_decode($response, true);

    if ($httpCode === 200) {
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($text === null) {
            // Model may have blocked the response (safety filters etc.)
            return ['ok' => false, 'error_type' => 'other', 'message' => 'Empty response from model.'];
        }
        return ['ok' => true, 'text' => trim($text)];
    }

    $apiMessage = $data['error']['message'] ?? "HTTP {$httpCode}";

    if ($httpCode === 429) {
        return ['ok' => false, 'error_type' => 'quota', 'message' => $apiMessage];
    }
    if ($httpCode === 400 || $httpCode === 403) {
        return ['ok' => false, 'error_type' => 'auth', 'message' => $apiMessage];
    }
    return ['ok' => false, 'error_type' => 'other', 'message' => $apiMessage];
}
