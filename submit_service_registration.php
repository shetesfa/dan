<?php
require_once 'config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$service = isset($_POST['service']) ? trim($_POST['service']) : '';
$package_name = isset($_POST['package_name']) ? trim($_POST['package_name']) : '';
$package_price = isset($_POST['package_price']) ? trim($_POST['package_price']) : '';
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telegram = isset($_POST['telegram']) ? trim($_POST['telegram']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

$errors = [];
if (empty($fullname)) $errors[] = "Name is required";
if (empty($phone)) $errors[] = "Phone number is required";

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$conn->query("CREATE TABLE IF NOT EXISTS `service_registrations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service` varchar(200) DEFAULT NULL,
    `package_name` varchar(100) DEFAULT NULL,
    `package_price` varchar(50) DEFAULT NULL,
    `fullname` varchar(100) NOT NULL,
    `phone` varchar(50) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `telegram` varchar(100) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$service = $conn->real_escape_string($service);
$package_name = $conn->real_escape_string($package_name);
$package_price = $conn->real_escape_string($package_price);
$fullname = $conn->real_escape_string($fullname);
$phone = $conn->real_escape_string($phone);
$email = $conn->real_escape_string($email);
$telegram = $conn->real_escape_string($telegram);
$notes = $conn->real_escape_string($notes);

$sql = "INSERT INTO service_registrations (service, package_name, package_price, fullname, phone, email, telegram, notes, status, created_at) 
        VALUES ('$service', '$package_name', '$package_price', '$fullname', '$phone', '$email', '$telegram', '$notes', 'pending', NOW())";

if ($conn->query($sql)) {
    $reg_id = $conn->insert_id;
    
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $message = "🎨 *NEW SERVICE REGISTRATION* 🎨\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📋 *Registration ID:* #{$reg_id}\n";
    $message .= "💼 *Service:* {$service}\n";
    $message .= "📦 *Package:* {$package_name}\n";
    $message .= "💰 *Price:* {$package_price}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 *Name:* {$fullname}\n";
    $message .= "📞 *Phone:* {$phone}\n";
    if(!empty($email)) $message .= "📧 *Email:* {$email}\n";
    if(!empty($telegram)) $message .= "📱 *Telegram:* {$telegram}\n";
    if(!empty($notes)) $message .= "\n📝 *Notes:*\n{$notes}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "⏰ *Time:* " . getEthiopiaDateTime() . "\n\n";
    $message .= "#ServiceRegistration #{$service}";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    
    echo json_encode([
        'success' => true, 
        'message' => '✓ Registration submitted successfully! We will contact you within 24 hours.',
        'reg_id' => $reg_id
    ]);
} else {
    error_log("Database error: " . $conn->error);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error. Please try again.'
    ]);
}

$conn->close();
?>