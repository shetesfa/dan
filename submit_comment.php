<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'] ?? 0;
    $user_name = $conn->real_escape_string($_POST['user_name'] ?? '');
    $user_email = $conn->real_escape_string($_POST['user_email'] ?? '');
    $comment = $conn->real_escape_string($_POST['comment'] ?? '');
    
    if ($user_name && $comment && $course_id) {
        $insert = "INSERT INTO comments (course_id, user_name, user_email, comment, approved) 
                   VALUES ('$course_id', '$user_name', '$user_email', '$comment', 0)";
        
        if ($conn->query($insert)) {
            // Send notification to admin via Telegram - UPDATED
            $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
            $chatId = "6823964923";
            
            $message = "💬 *New Comment Pending Approval*\n\n";
            $message .= "Course ID: $course_id\n";
            $message .= "Name: $user_name\n";
            $message .= "Comment: $comment\n";
            
            $url = "https://api.telegram.org/bot$botToken/sendMessage";
            $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_exec($ch);
            curl_close($ch);
            
            $_SESSION['comment_success'] = "Thank you! Your comment is pending approval.";
        } else {
            $_SESSION['comment_error'] = "Failed to submit comment. Please try again.";
        }
    } else {
        $_SESSION['comment_error'] = "Please fill in all required fields.";
    }
    
    header("Location: courses.php#course-$course_id");
    exit();
}
?>