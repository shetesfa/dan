<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle course deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM courses WHERE id = $id");
    redirect('manage-courses.php');
}

// Handle course status toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE courses SET status = IF(status='active', 'coming_soon', 'active') WHERE id = $id");
    redirect('manage-courses.php');
}

// Handle add/edit course
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $status = $conn->real_escape_string($_POST['status']);
    $badge_text = $conn->real_escape_string($_POST['badge_text']);
    
    if ($id > 0) {
        // Update
        $sql = "UPDATE courses SET 
                title='$title', description='$description', price='$price', 
                duration='$duration', start_date='$start_date', icon_class='$icon_class', 
                status='$status', badge_text='$badge_text' 
                WHERE id=$id";
    } else {
        // Insert
        $sql = "INSERT INTO courses (title, description, price, duration, start_date, icon_class, status, badge_text) 
                VALUES ('$title', '$description', '$price', '$duration', '$start_date', '$icon_class', '$status', '$badge_text')";
    }
    
    $conn->query($sql);
    redirect('manage-courses.php');
}

// Fetch all courses
$courses = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin Panel</title>
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
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: #1a2a3a;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            font-size: 24px;
        }
        
        .sidebar-header span {
            color: #ff6b6b;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 30px;
            color: #cbd5e0;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,107,107,0.1);
            color: #ff6b6b;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 30px;
        }
        
        .top-bar {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-add {
            background: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .courses-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #1a2a3a;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-coming {
            background: #fff3cd;
            color: #856404;
        }
        
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .btn-edit {
            background: #007bff;
            color: white;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-toggle {
            background: #ffc107;
            color: #333;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
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
        
        .btn-submit {
            background: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Dan<span>Creatives</span></h3>
                <p style="font-size: 12px; margin-top: 10px;">Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="manage-courses.php" class="active"><i class="fas fa-book"></i> Manage Courses</a>
                <a href="manage-comments.php"><i class="fas fa-comments"></i> Manage Comments</a>
                <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2>Manage Courses</h2>
                <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add New Course</button>
            </div>
            
            <div class="courses-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $course['id']; ?></td>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo $course['price']; ?></td>
                            <td><?php echo $course['duration']; ?></td>
                            <td>
                                <span class="status-badge <?php echo $course['status'] == 'active' ? 'status-active' : 'status-coming'; ?>">
                                    <?php echo $course['status'] == 'active' ? 'Active' : 'Coming Soon'; ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn-edit">Edit</a>
                                <a href="?toggle=<?php echo $course['id']; ?>" class="btn-toggle">Toggle Status</a>
                                <a href="?delete=<?php echo $course['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Add New Course</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" name="price" placeholder="e.g., 2,990 Birr" required>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" placeholder="e.g., 8 Weeks" required>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="text" name="start_date" placeholder="e.g., June 12, 2025" required>
                </div>
                <div class="form-group">
                    <label>Icon Class</label>
                    <input type="text" name="icon_class" value="fas fa-palette">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="coming_soon">Coming Soon</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Badge Text (optional)</label>
                    <input type="text" name="badge_text" placeholder="e.g., Most Popular">
                </div>
                <button type="submit" class="btn-submit">Save Course</button>
                <button type="button" onclick="closeModal()" style="margin-left: 10px;">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>