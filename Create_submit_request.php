<?php
require_once 'config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
$service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
$client_name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
$client_email = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';
$client_phone = isset($_POST['client_phone']) ? trim($_POST['client_phone']) : '';
$client_telegram = isset($_POST['client_telegram']) ? trim($_POST['client_telegram']) : '';
$budget = isset($_POST['budget']) ? trim($_POST['budget']) : '';
$deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$requirements = isset($_POST['requirements']) ? trim($_POST['requirements']) : '';

error_log("Service Request Received: Name=$client_name, Email=$client_email, Service=$service_name");

$errors = [];
if (empty($client_name)) $errors[] = "Name is required";
if (empty($client_email)) $errors[] = "Email is required";
elseif (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
if (empty($client_phone)) $errors[] = "Phone number is required";
if (empty($requirements)) $errors[] = "Project requirements are required";

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$create_table = "CREATE TABLE IF NOT EXISTS `service_requests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service_id` int(11) DEFAULT NULL,
    `service_name` varchar(255) DEFAULT NULL,
    `customer_name` varchar(255) NOT NULL,
    `customer_email` varchar(255) NOT NULL,
    `customer_phone` varchar(50) NOT NULL,
    `customer_telegram` varchar(100) DEFAULT NULL,
    `requirements` text NOT NULL,
    `budget` varchar(100) DEFAULT NULL,
    `deadline` varchar(100) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$conn->query($create_table);

$service_name = $conn->real_escape_string($service_name);
$client_name = $conn->real_escape_string($client_name);
$client_email = $conn->real_escape_string($client_email);
$client_phone = $conn->real_escape_string($client_phone);
$client_telegram = $conn->real_escape_string($client_telegram);
$budget = $conn->real_escape_string($budget);
$deadline = $conn->real_escape_string($deadline);
$requirements = $conn->real_escape_string($requirements);

$sql = "INSERT INTO service_requests (service_id, service_name, customer_name, customer_email, customer_phone, customer_telegram, requirements, budget, deadline, status, created_at) 
        VALUES ($service_id, '$service_name', '$client_name', '$client_email', '$client_phone', '$client_telegram', '$requirements', '$budget', '$deadline', 'pending', NOW())";

if ($conn->query($sql)) {
    $request_id = $conn->insert_id;
    error_log("Service Request Saved: ID=$request_id");
    
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $message = "🎨 *NEW SERVICE REQUEST* 🎨\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📋 *Request ID:* #{$request_id}\n";
    $message .= "💼 *Service:* {$service_name}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 *Client:* {$client_name}\n";
    $message .= "📧 *Email:* {$client_email}\n";
    $message .= "📞 *Phone:* {$client_phone}\n";
    if(!empty($client_telegram)) $message .= "📱 *Telegram:* {$client_telegram}\n";
    $message .= "💰 *Budget:* {$budget}\n";
    $message .= "⏰ *Deadline:* {$deadline}\n";
    $message .= "\n📝 *Requirements:*\n{$requirements}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "⏰ *Time:* " . date('Y-m-d H:i:s') . "\n";
    $message .= "🔔 *Status:* Pending\n\n";
    $message .= "#ServiceRequest #DanCreatives";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("Telegram Response: HTTP $httpCode");
    
    echo json_encode([
        'success' => true, 
        'message' => '✓ Service request submitted successfully! We will contact you within 24 hours.',
        'request_id' => $request_id
    ]);
} else {
    error_log("Database Error: " . $conn->error);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $conn->error
    ]);
}

$conn->close();
?>