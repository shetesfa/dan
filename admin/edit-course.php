<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = $conn->query("SELECT * FROM courses WHERE id=$id")->fetch_assoc();

if (!$course) {
    redirect('manage-courses.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $status = $conn->real_escape_string($_POST['status']);
    $badge_text = $conn->real_escape_string($_POST['badge_text']);
    
    $sql = "UPDATE courses SET 
            title='$title', description='$description', price='$price', 
            duration='$duration', start_date='$start_date', icon_class='$icon_class', 
            status='$status', badge_text='$badge_text' 
            WHERE id=$id";
    
    if ($conn->query($sql)) {
        redirect('manage-courses.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Admin Panel</title>
    <link rel="icon" href="../images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
        }
        
        h2 {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .btn-submit {
            background: #ff6b6b;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Course</h2>
        <form method="POST">
            <div class="form-group">
                <label>Course Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="text" name="price" value="<?php echo htmlspecialchars($course['price']); ?>" required>
            </div>
            <div class="form-group">
                <label>Duration</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($course['duration']); ?>" required>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <input type="text" name="start_date" value="<?php echo htmlspecialchars($course['start_date']); ?>" required>
            </div>
            <div class="form-group">
                <label>Icon Class</label>
                <input type="text" name="icon_class" value="<?php echo htmlspecialchars($course['icon_class']); ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo $course['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="coming_soon" <?php echo $course['status'] == 'coming_soon' ? 'selected' : ''; ?>>Coming Soon</option>
                </select>
            </div>
            <div class="form-group">
                <label>Badge Text</label>
                <input type="text" name="badge_text" value="<?php echo htmlspecialchars($course['badge_text']); ?>">
            </div>
            <button type="submit" class="btn-submit">Update Course</button>
            <a href="manage-courses.php" class="btn-back">Cancel</a>
        </form>
    </div>
</body>
</html>