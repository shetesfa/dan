<?php
require_once '../config.php';
require_once '../ai/gemini_client.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'save_settings') {
    $provider = $conn->real_escape_string($_POST['provider'] ?? 'gemini');
    $model = $conn->real_escape_string($_POST['model'] ?? 'gemini-2.5-flash-lite');
    $system_prompt = $conn->real_escape_string($_POST['system_prompt'] ?? '');
    $welcome_message = $conn->real_escape_string($_POST['welcome_message'] ?? '');
    $enabled = isset($_POST['enabled']) && $_POST['enabled'] == '1' ? 1 : 0;
    $newKey = trim($_POST['api_key'] ?? '');

    $current = $conn->query("SELECT api_key FROM ai_settings WHERE id=1")->fetch_assoc();
    $keyChanged = $newKey !== '' && $newKey !== ($current['api_key'] ?? '');

    if ($newKey !== '') {
        $keyEsc = $conn->real_escape_string($newKey);
        $conn->query("UPDATE ai_settings SET provider='{$provider}', api_key='{$keyEsc}', model='{$model}', system_prompt='{$system_prompt}', welcome_message='{$welcome_message}', enabled={$enabled}" . ($keyChanged ? ", status='not_configured', last_error=NULL" : "") . " WHERE id=1");
    } else {
        $conn->query("UPDATE ai_settings SET provider='{$provider}', model='{$model}', system_prompt='{$system_prompt}', welcome_message='{$welcome_message}', enabled={$enabled} WHERE id=1");
    }

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'test_connection') {
    $settings = $conn->query("SELECT * FROM ai_settings WHERE id=1")->fetch_assoc();
    if (empty($settings['api_key'])) {
        echo json_encode(['ok' => false, 'message' => 'No API key saved yet. Paste a key and save first.']);
        exit;
    }
    $result = call_gemini(
        $settings['api_key'],
        $settings['model'] ?: 'gemini-2.5-flash-lite',
        'You are a connection test. Reply with a single short friendly sentence confirming you are working.',
        [],
        'Say hello in one short sentence.'
    );
    if ($result['ok']) {
        $conn->query("UPDATE ai_settings SET status='active', last_error=NULL, last_checked_at=NOW() WHERE id=1");
        echo json_encode(['ok' => true, 'message' => $result['text']]);
    } else {
        $status = $result['error_type'] === 'quota' ? 'limit_reached' : ($result['error_type'] === 'auth' ? 'invalid_key' : 'error');
        $errEsc = $conn->real_escape_string($result['message']);
        $conn->query("UPDATE ai_settings SET status='{$status}', last_error='{$errEsc}', last_checked_at=NOW() WHERE id=1");
        echo json_encode(['ok' => false, 'message' => $result['message'], 'error_type' => $result['error_type']]);
    }
    exit;
}

if ($action === 'mark_resolved') {
    $id = (int)($_POST['id'] ?? 0);
    $conn->query("UPDATE ai_conversations SET status='resolved' WHERE id={$id}");
    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'get_conversation') {
    $id = (int)($_GET['id'] ?? 0);
    $conv = $conn->query("SELECT * FROM ai_conversations WHERE id={$id}")->fetch_assoc();
    $msgs = [];
    $res = $conn->query("SELECT role, content, created_at FROM ai_messages WHERE conversation_id={$id} ORDER BY id ASC");
    while ($r = $res->fetch_assoc()) { $msgs[] = $r; }
    echo json_encode(['ok' => true, 'conversation' => $conv, 'messages' => $msgs]);
    exit;
}

if ($action === 'delete_conversation') {
    $id = (int)($_POST['id'] ?? 0);
    $conn->query("DELETE FROM ai_conversations WHERE id={$id}");
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
