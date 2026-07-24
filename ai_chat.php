<?php
require_once 'config.php';
require_once 'ai/business_context.php';
require_once 'ai/gemini_client.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}

$session_id   = preg_replace('/[^a-zA-Z0-9\-]/', '', $input['session_id'] ?? '');
$message      = trim($input['message'] ?? '');
$source_page  = substr($conn->real_escape_string($input['page'] ?? ''), 0, 100);
$force_escalate = !empty($input['force_escalate']);
$image_base64 = $input['image_base64'] ?? null;
$image_mime   = $input['image_mime'] ?? null;
$visitor_name = trim($input['visitor_name'] ?? '');
$visitor_phone = trim($input['visitor_phone'] ?? '');
$visitor_telegram = trim($input['visitor_telegram'] ?? '');

if (!$session_id || (!$message && !$force_escalate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing session_id or message']);
    exit;
}

// ---- find or create conversation ----
$stmt = $conn->prepare("SELECT * FROM ai_conversations WHERE session_id = ? LIMIT 1");
$stmt->bind_param('s', $session_id);
$stmt->execute();
$conv = $stmt->get_result()->fetch_assoc();

if (!$conv) {
    $stmt = $conn->prepare("INSERT INTO ai_conversations (session_id, source_page) VALUES (?, ?)");
    $stmt->bind_param('ss', $session_id, $source_page);
    $stmt->execute();
    $conv_id = $stmt->insert_id;
    $conv = ['id' => $conv_id, 'status' => 'active'];
} else {
    $conv_id = $conv['id'];
}

// update visitor contact info if the widget captured it
if ($visitor_name || $visitor_phone || $visitor_telegram) {
    $stmt = $conn->prepare("UPDATE ai_conversations SET visitor_name = COALESCE(NULLIF(?, ''), visitor_name), visitor_phone = COALESCE(NULLIF(?, ''), visitor_phone), visitor_telegram = COALESCE(NULLIF(?, ''), visitor_telegram) WHERE id = ?");
    $stmt->bind_param('sssi', $visitor_name, $visitor_phone, $visitor_telegram, $conv_id);
    $stmt->execute();
}

function ai_log_message($conn, $conv_id, $role, $content) {
    $stmt = $conn->prepare("INSERT INTO ai_messages (conversation_id, role, content) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $conv_id, $role, $content);
    $stmt->execute();
    $conn->query("UPDATE ai_conversations SET last_message_at = NOW() WHERE id = " . (int)$conv_id);
}

function ai_escalate($conn, $conv_id, $reason, $visitor_name, $visitor_phone, $visitor_telegram, $last_user_message) {
    $reasonEsc = $conn->real_escape_string($reason);
    $conn->query("UPDATE ai_conversations SET status='escalated', escalation_reason='{$reasonEsc}' WHERE id=" . (int)$conv_id);

    $contact = trim(($visitor_name ?: 'Unknown visitor') .
        ($visitor_phone ? " | 📞 {$visitor_phone}" : "") .
        ($visitor_telegram ? " | 💬 @{$visitor_telegram}" : ""));

    $msg = "🤖 *AI chat needs you*\n" .
           "{$contact}\n" .
           "Reason: {$reason}\n" .
           "Last message: \"" . substr($last_user_message, 0, 300) . "\"\n" .
           "Reply to them directly, or open Admin > AI Assistant to see the full chat (conversation #{$conv_id}).";
    if (function_exists('sendTelegramNotification')) {
        sendTelegramNotification($msg);
    }
}

if ($message) {
    ai_log_message($conn, $conv_id, 'user', $message);
}

// ---- explicit "talk to a human" button ----
if ($force_escalate) {
    ai_escalate($conn, $conv_id, 'Visitor asked to talk to a human', $visitor_name, $visitor_phone, $visitor_telegram, $message ?: '(no message)');
    $reply = "Got it — I've let our team know 👋 Someone will reach out to you on Telegram or phone shortly. Feel free to keep chatting with me in the meantime!";
    ai_log_message($conn, $conv_id, 'assistant', $reply);
    echo json_encode(['reply' => $reply, 'escalated' => true]);
    exit;
}

// ---- load AI settings ----
$settings = $conn->query("SELECT * FROM ai_settings WHERE id = 1")->fetch_assoc();

if (!$settings || !$settings['enabled'] || empty($settings['api_key'])) {
    ai_escalate($conn, $conv_id, 'AI assistant is not configured yet', $visitor_name, $visitor_phone, $visitor_telegram, $message);
    $reply = "Thanks for reaching out! Our assistant is still warming up — I've notified our team and they'll reply to you personally very soon 🙌";
    ai_log_message($conn, $conv_id, 'assistant', $reply);
    echo json_encode(['reply' => $reply, 'escalated' => true]);
    exit;
}

// ---- build context + system prompt ----
$business_context = build_ai_context($conn);
$custom_prompt = $settings['system_prompt'] ?: "You are the friendly virtual assistant for Dan Creatives, a graphics design studio, print-on-demand shop, and design academy in Ethiopia.";

$system_prompt = $custom_prompt . "\n\n" .
    "STRICT SCOPE: you only discuss Dan Creatives — its services, products, courses, prices, and how to order. " .
    "You are NOT a general-purpose assistant. If someone asks something unrelated to this business (general knowledge, other companies, coding help, personal advice, current events, translations of unrelated text, writing unrelated content, etc.), " .
    "do NOT answer it — politely say that's outside what you can help with here and steer back to how you can help with Dan Creatives' services, products, or courses. Do this even if you happen to know the answer. " .
    "Use ONLY the business information below to answer business questions. Never invent prices, deadlines, or policies that aren't listed. " .
    "Keep answers short (2-5 sentences), warm, and conversational, like a helpful human — not a robot reading a list. " .
    "Always reply in the same language the visitor writes in (English or Amharic). " .
    "If the visitor's question is something you cannot confidently answer from the info below, OR they ask for the owner/a human/a call, " .
    "OR they want to negotiate a custom price or deadline, OR they sound frustrated or upset, " .
    "start your reply with the exact tag [[ESCALATE]] followed by a short warm message telling them you're connecting them with the team. " .
    "Otherwise just answer normally with no tag.\n\n" .
    $business_context;

// ---- recent history for context (last 10 messages) ----
$history = [];
$hist_res = $conn->query("SELECT role, content FROM ai_messages WHERE conversation_id = " . (int)$conv_id . " AND role != 'system' ORDER BY id DESC LIMIT 10");
$rows = [];
while ($r = $hist_res->fetch_assoc()) { $rows[] = $r; }
$rows = array_reverse($rows);
foreach ($rows as $r) {
    if ($r['role'] === 'user' && $r['content'] === $message) continue; // avoid duplicating the message we're about to send
    $history[] = ['role' => $r['role'], 'text' => $r['content']];
}

$result = call_gemini($settings['api_key'], $settings['model'] ?: 'gemini-2.5-flash-lite', $system_prompt, $history, $message, $image_base64, $image_mime);

if (!$result['ok']) {
    $status = $result['error_type'] === 'quota' ? 'limit_reached' : ($result['error_type'] === 'auth' ? 'invalid_key' : 'error');
    $errMsg = $conn->real_escape_string($result['message']);
    $prevStatus = $settings['status'];
    $conn->query("UPDATE ai_settings SET status='{$status}', last_error='{$errMsg}', last_checked_at=NOW() WHERE id=1");

    // Only ping the admin about the key itself once per status change, so they aren't spammed on every message
    if ($prevStatus !== $status) {
        $keyMsg = $status === 'limit_reached'
            ? "⚠️ *Gemini API limit reached.* The AI assistant will keep handing chats to you directly until you add a new key in Admin > AI Assistant."
            : "⚠️ *AI assistant error* ({$status}): {$result['message']}\nCheck the API key in Admin > AI Assistant.";
        if (function_exists('sendTelegramNotification')) {
            sendTelegramNotification($keyMsg);
        }
    }

    ai_escalate($conn, $conv_id, 'AI service unavailable (' . $status . ')', $visitor_name, $visitor_phone, $visitor_telegram, $message);
    $reply = "I'm having a little trouble thinking right now 😅 I've already told our team about your message — they'll follow up with you directly very soon!";
    ai_log_message($conn, $conv_id, 'assistant', $reply);
    echo json_encode(['reply' => $reply, 'escalated' => true]);
    exit;
}

// success — reset status to active if it had previously failed
if ($settings['status'] !== 'active') {
    $conn->query("UPDATE ai_settings SET status='active', last_error=NULL, last_checked_at=NOW() WHERE id=1");
}

$reply = $result['text'];
$escalated = false;

if (strpos($reply, '[[ESCALATE]]') === 0) {
    $escalated = true;
    $reply = trim(substr($reply, strlen('[[ESCALATE]]')));
    ai_escalate($conn, $conv_id, 'AI flagged this for a human', $visitor_name, $visitor_phone, $visitor_telegram, $message);
} else {
    $conn->query("UPDATE ai_conversations SET status='active' WHERE id=" . (int)$conv_id . " AND status != 'resolved'");
}

ai_log_message($conn, $conv_id, 'assistant', $reply);

echo json_encode(['reply' => $reply, 'escalated' => $escalated]);
