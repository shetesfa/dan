<?php
require_once 'config.php';

header('Content-Type: application/json');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$telegram = trim($_POST['telegram'] ?? '');
$question = trim($_POST['question'] ?? '');

if (empty($name) || empty($question)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in your name and question']);
    exit;
}

if (empty($email) && empty($telegram)) {
    echo json_encode(['success' => false, 'message' => 'Please provide either Email OR Telegram username for us to answer you']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

$is_registered = false;
$check = $conn->query("SELECT id FROM registrations WHERE email = '$email' OR phone = '$telegram'");
if ($check && $check->num_rows > 0) {
    $is_registered = true;
}

$sql = "INSERT INTO questions (name, email, telegram, question, is_registered) 
        VALUES ('$name', '$email', '$telegram', '$question', " . ($is_registered ? 1 : 0) . ")";

if ($conn->query($sql)) {
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $contact = !empty($email) ? "📧 Email: $email\n" : "";
    $contact .= !empty($telegram) ? "📱 Telegram: $telegram\n" : "";
    
    if ($is_registered) {
        $message = "🎓⭐ *REGISTERED STUDENT QUESTION* ⭐🎓\n\n";
        $message .= "👤 Name: $name\n";
        $message .= $contact;
        $message .= "\n❓ Question:\n$question\n\n";
        $message .= "⏰ Time: " . getEthiopiaDateTime() . "\n\n#RegisteredStudent";
    } else {
        $message = "❓ *NEW QUESTION FROM WEBSITE* ❓\n\n";
        $message .= "👤 Name: $name\n";
        $message .= $contact;
        $message .= "\n❓ Question:\n$question\n\n";
        $message .= "⏰ Time: " . getEthiopiaDateTime() . "\n\n#GuestQuestion";
    }
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    
    echo json_encode(['success' => true, 'message' => 'Your question has been sent! We\'ll answer within 24 hours.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send. Please try again.']);
}
?>