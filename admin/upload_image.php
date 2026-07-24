<?php
require_once '../config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Not authorized']);
    exit;
}
header('Content-Type: application/json');

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'message' => 'No file received.']);
    exit;
}

$file = $_FILES['file'];
$allowed = [
    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
    'image/gif' => 'gif', 'video/mp4' => 'mp4', 'video/webm' => 'webm',
];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowed[$mime])) {
    echo json_encode(['ok' => false, 'message' => 'Unsupported file type. Use JPG, PNG, WEBP, GIF, MP4 or WEBM.']);
    exit;
}

$maxSize = str_starts_with($mime, 'video/') ? 20 * 1024 * 1024 : 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    $limit = str_starts_with($mime, 'video/') ? '20MB' : '5MB';
    echo json_encode(['ok' => false, 'message' => "File is too large. Max size is {$limit}."]);
    exit;
}

$dir = __DIR__ . '/../uploads/images/';
if (!is_dir($dir)) { mkdir($dir, 0755, true); }

$filename = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
$destination = $dir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode(['ok' => false, 'message' => 'Could not save the file on the server.']);
    exit;
}

echo json_encode(['ok' => true, 'path' => 'uploads/images/' . $filename]);
