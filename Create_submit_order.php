<?php
require_once 'config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$customer_telegram = isset($_POST['customer_telegram']) ? trim($_POST['customer_telegram']) : '';
$quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '1';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
$errors = [];
if (empty($customer_name)) $errors[] = "Name is required";
if (empty($customer_email)) $errors[] = "Email is required";
elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
if (empty($customer_phone)) $errors[] = "Phone number is required";

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS `product_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) DEFAULT NULL,
    `product_name` varchar(255) DEFAULT NULL,
    `customer_name` varchar(255) NOT NULL,
    `customer_email` varchar(255) NOT NULL,
    `customer_phone` varchar(50) NOT NULL,
    `customer_telegram` varchar(100) DEFAULT NULL,
    `quantity` varchar(50) DEFAULT NULL,
    `message` text,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Escape values
$product_name = $conn->real_escape_string($product_name);
$customer_name = $conn->real_escape_string($customer_name);
$customer_email = $conn->real_escape_string($customer_email);
$customer_phone = $conn->real_escape_string($customer_phone);
$customer_telegram = $conn->real_escape_string($customer_telegram);
$quantity = $conn->real_escape_string($quantity);
$message = $conn->real_escape_string($message);

// Insert into database
$sql = "INSERT INTO product_orders (product_id, product_name, customer_name, customer_email, customer_phone, customer_telegram, quantity, message, status, created_at) 
        VALUES ($product_id, '$product_name', '$customer_name', '$customer_email', '$customer_phone', '$customer_telegram', '$quantity', '$message', 'pending', NOW())";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    // Send Telegram notification with correct token
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $telegram_message = "🛍️ *NEW PRODUCT ORDER* 🛍️\n\n";
    $telegram_message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegram_message .= "📋 *Order ID:* #{$order_id}\n";
    $telegram_message .= "📦 *Product:* {$product_name}\n";
    $telegram_message .= "🔢 *Quantity:* {$quantity}\n\n";
    $telegram_message .= "👤 *Customer Details:*\n";
    $telegram_message .= "├ *Name:* {$customer_name}\n";
    $telegram_message .= "├ *Email:* {$customer_email}\n";
    $telegram_message .= "├ *Phone:* {$customer_phone}\n";
    if(!empty($customer_telegram)) {
        $telegram_message .= "└ *Telegram:* {$customer_telegram}\n";
    } else {
        $telegram_message .= "└ *Telegram:* Not provided\n";
    }
    
    if(!empty($message)) {
        $telegram_message .= "\n💬 *Additional Message:*\n{$message}\n";
    }
    
    $telegram_message .= "\n⏰ *Time:* " . date('Y-m-d H:i:s');
    $telegram_message .= "\n🔔 *Status:* Pending\n\n";
    $telegram_message .= "#ProductOrder #DanCreatives";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = ['chat_id' => $chatId, 'text' => $telegram_message, 'parse_mode' => 'Markdown'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);
    
    echo json_encode([
        'success' => true, 
        'message' => '✓ Order submitted successfully! We will contact you within 24 hours.',
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