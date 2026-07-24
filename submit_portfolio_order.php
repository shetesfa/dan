<?php
require_once 'config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$portfolio_id = isset($_POST['portfolio_id']) ? (int)$_POST['portfolio_id'] : 0;
$portfolio_title = isset($_POST['portfolio_title']) ? trim($_POST['portfolio_title']) : '';
$portfolio_media = isset($_POST['portfolio_media']) ? trim($_POST['portfolio_media']) : '';
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
$service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$customer_telegram = isset($_POST['customer_telegram']) ? trim($_POST['customer_telegram']) : '';
$budget = isset($_POST['budget']) ? trim($_POST['budget']) : '';
$deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$requirements = isset($_POST['requirements']) ? trim($_POST['requirements']) : '';

$errors = [];
if (empty($customer_name)) $errors[] = "Name is required";
if (empty($customer_phone)) $errors[] = "Phone number is required";

if (!empty($customer_email) && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS `portfolio_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `portfolio_id` int(11) NOT NULL,
    `portfolio_title` varchar(200) DEFAULT NULL,
    `portfolio_media` varchar(500) DEFAULT NULL,
    `service_id` int(11) NOT NULL,
    `service_name` varchar(200) DEFAULT NULL,
    `customer_name` varchar(100) NOT NULL,
    `customer_email` varchar(100) DEFAULT NULL,
    `customer_phone` varchar(50) NOT NULL,
    `customer_telegram` varchar(100) DEFAULT NULL,
    `requirements` text DEFAULT NULL,
    `budget` varchar(100) DEFAULT NULL,
    `deadline` varchar(100) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$portfolio_title = $conn->real_escape_string($portfolio_title);
$portfolio_media = $conn->real_escape_string($portfolio_media);
$service_name = $conn->real_escape_string($service_name);
$customer_name = $conn->real_escape_string($customer_name);
$customer_email = $conn->real_escape_string($customer_email);
$customer_phone = $conn->real_escape_string($customer_phone);
$customer_telegram = $conn->real_escape_string($customer_telegram);
$budget = $conn->real_escape_string($budget);
$deadline = $conn->real_escape_string($deadline);
$requirements = $conn->real_escape_string($requirements);

$sql = "INSERT INTO portfolio_orders (portfolio_id, portfolio_title, portfolio_media, service_id, service_name, customer_name, customer_email, customer_phone, customer_telegram, requirements, budget, deadline, status, created_at) 
        VALUES ($portfolio_id, '$portfolio_title', '$portfolio_media', $service_id, '$service_name', '$customer_name', '$customer_email', '$customer_phone', '$customer_telegram', '$requirements', '$budget', '$deadline', 'pending', NOW())";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $message = "🎨 *NEW PORTFOLIO ORDER* 🎨\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📋 *Order ID:* #{$order_id}\n";
    $message .= "💼 *Service:* {$service_name}\n";
    $message .= "🎨 *Design:* {$portfolio_title}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 *Client:* {$customer_name}\n";
    if(!empty($customer_email)) $message .= "📧 *Email:* {$customer_email}\n";
    $message .= "📞 *Phone:* {$customer_phone}\n";
    if(!empty($customer_telegram)) $message .= "📱 *Telegram:* {$customer_telegram}\n";
    $message .= "💰 *Budget:* {$budget}\n";
    $message .= "⏰ *Deadline:* {$deadline}\n";
    if(!empty($requirements)) $message .= "\n📝 *Requirements:*\n{$requirements}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "⏰ *Time:* " . getEthiopiaDateTime() . "\n\n";
    $message .= "#PortfolioOrder #{$service_name}";
    
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
        'message' => '✓ Request submitted successfully! We will contact you within 24 hours.',
        'order_id' => $order_id
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