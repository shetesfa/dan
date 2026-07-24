<?php
require_once 'config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$template_id = isset($_POST['template_id']) ? (int)$_POST['template_id'] : 0;
$template_name = isset($_POST['template_name']) ? trim($_POST['template_name']) : '';
$template_image = isset($_POST['template_image']) ? trim($_POST['template_image']) : '';
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$customer_telegram = isset($_POST['customer_telegram']) ? trim($_POST['customer_telegram']) : '';
$quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '1';
$size = isset($_POST['size']) ? trim($_POST['size']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';
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

$conn->query("CREATE TABLE IF NOT EXISTS `product_template_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `product_name` varchar(200) DEFAULT NULL,
    `template_id` int(11) NOT NULL,
    `template_name` varchar(200) NOT NULL,
    `template_image` varchar(500) DEFAULT NULL,
    `customer_name` varchar(100) NOT NULL,
    `customer_email` varchar(100) DEFAULT NULL,
    `customer_phone` varchar(50) NOT NULL,
    `customer_telegram` varchar(100) DEFAULT NULL,
    `quantity` varchar(50) DEFAULT '1',
    `size` varchar(50) DEFAULT NULL,
    `color` varchar(100) DEFAULT NULL,
    `requirements` text DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$product_name = $conn->real_escape_string($product_name);
$template_name = $conn->real_escape_string($template_name);
$template_image = $conn->real_escape_string($template_image);
$customer_name = $conn->real_escape_string($customer_name);
$customer_email = $conn->real_escape_string($customer_email);
$customer_phone = $conn->real_escape_string($customer_phone);
$customer_telegram = $conn->real_escape_string($customer_telegram);
$quantity = $conn->real_escape_string($quantity);
$size = $conn->real_escape_string($size);
$color = $conn->real_escape_string($color);
$requirements = $conn->real_escape_string($requirements);

$sql = "INSERT INTO product_template_orders (product_id, product_name, template_id, template_name, template_image, customer_name, customer_email, customer_phone, customer_telegram, quantity, size, color, requirements, status, created_at) 
        VALUES ($product_id, '$product_name', $template_id, '$template_name', '$template_image', '$customer_name', '$customer_email', '$customer_phone', '$customer_telegram', '$quantity', '$size', '$color', '$requirements', 'pending', NOW())";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
    $chatId = "6823964923";
    
    $message = "🛍️ *NEW PRODUCT TEMPLATE ORDER* 🛍️\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📋 *Order ID:* #{$order_id}\n";
    $message .= "📦 *Product:* {$product_name}\n";
    $message .= "🎨 *Design Style:* {$template_name}\n";
    $message .= "🔢 *Quantity:* {$quantity}\n";
    if(!empty($size)) $message .= "📏 *Size:* {$size}\n";
    if(!empty($color)) $message .= "🎨 *Color:* {$color}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 *Customer:* {$customer_name}\n";
    if(!empty($customer_email)) $message .= "📧 *Email:* {$customer_email}\n";
    $message .= "📞 *Phone:* {$customer_phone}\n";
    if(!empty($customer_telegram)) $message .= "📱 *Telegram:* {$customer_telegram}\n";
    if(!empty($requirements)) $message .= "\n📝 *Requirements:*\n{$requirements}\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "⏰ *Time:* " . getEthiopiaDateTime() . "\n\n";
    $message .= "#ProductTemplateOrder #{$product_name}";
    
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