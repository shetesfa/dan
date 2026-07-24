<?php
// config.php - Database configuration
session_start();

// Set timezone to Ethiopia/Addis Ababa (UTC+3)
date_default_timezone_set('Africa/Addis_Ababa');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change if you have MySQL password
define('DB_NAME', 'dan_creatives_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Set MySQL timezone to Ethiopia time (UTC+3)
$conn->query("SET time_zone = '+03:00'");

// Telegram Bot Configuration
define('TELEGRAM_BOT_TOKEN', '8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s');
define('TELEGRAM_CHAT_ID', '6823964923');

// Site configuration
define('SITE_NAME', 'Dan Creatives');
define('SITE_URL', 'http://localhost/dan-creatives'); // Change to your URL

// Function to send Telegram notification with error handling
function sendTelegramNotification($message) {
    if (!function_exists('curl_init')) {
        return false;
    }
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpCode == 200);
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function getEthiopiaTime() {
    return date('Y-m-d H:i:s');
}

function getEthiopiaDateTime() {
    return date('F d, Y h:i:s A');
}
?>