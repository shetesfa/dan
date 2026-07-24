<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM questions WHERE id = $id");
    redirect('manage-questions.php');
}

// Handle mark as answered
if (isset($_GET['answered'])) {
    $id = (int)$_GET['answered'];
    $conn->query("UPDATE questions SET status = 'answered', answered_at = NOW() WHERE id = $id");
    redirect('manage-questions.php');
}

$questions = $conn->query("SELECT * FROM questions ORDER BY is_registered DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions | Admin</title>
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
        .questions-table {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
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
        }
        .registered-badge {
            background: #ff6b6b;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .guest-badge {
            background: #718096;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-answered {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-answered {
            background: #28a745;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .question-text {
            max-width: 300px;
            word-wrap: break-word;
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
                <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
                <a href="manage-questions.php" class="active"><i class="fas fa-question-circle"></i> Questions</a>
                <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2>User Questions</h2>
                <a href="logout.php" style="background: #dc3545; color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none;">Logout</a>
            </div>
            
            <div class="questions-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($q = $questions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $q['id']; ?></td>
                            <td>
                                <?php if($q['is_registered']): ?>
                                    <span class="registered-badge"><i class="fas fa-star"></i> Registered</span>
                                <?php else: ?>
                                    <span class="guest-badge"><i class="fas fa-user"></i> Guest</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($q['name']); ?></td>
                            <td>
                                📧 <?php echo htmlspecialchars($q['email']); ?><br>
                                📱 <?php echo htmlspecialchars($q['telegram']); ?>
                            </td>
                            <td class="question-text"><?php echo htmlspecialchars(substr($q['question'], 0, 100)); ?>...</td>
                            <td>
                                <span class="<?php echo $q['status'] == 'pending' ? 'status-pending' : 'status-answered'; ?>">
                                    <?php echo $q['status'] == 'pending' ? 'Pending' : 'Answered'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($q['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($q['status'] == 'pending'): ?>
                                    <a href="?answered=<?php echo $q['id']; ?>" class="btn-answered">Mark Answered</a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $q['id']; ?>" class="btn-delete" onclick="return confirm('Delete this question?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>