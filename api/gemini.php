<?php
header('Content-Type: application/json');
$config = require __DIR__ . '/../config/gemini.php';
$apiKey = $config['api_key'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
if (trim($message) === '') {
    echo json_encode(['reply' => '']);
    exit;
}

$payload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $message]
            ]
        ]
    ]
];

$models = [
    'gemini-3.5-flash',
    'gemini-3.1-flash-lite',
    'gemini-2.5-flash',
    'gemini-2.0-flash',
    'gemini-1.5-flash'
];

$response = '';
$httpCode = 0;
$curlError = '';
$success = false;

foreach ($models as $model) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $curlError = curl_error($ch);
        curl_close($ch);
        continue; // Try next model
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $success = true;
        break; // Successfully got response
    }
}

if (!$success) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API error or quota exceeded', 
        'status' => $httpCode, 
        'curl_error' => $curlError,
        'body' => json_decode($response, true) ?? $response
    ]);
    exit;
}

$data = json_decode($response, true);
$reply = '';
if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $data['candidates'][0]['content']['parts'][0]['text'];
}

echo json_encode(['reply' => $reply]);
?>
