<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Update request status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $conn->real_escape_string($_GET['status']);
    $conn->query("UPDATE service_requests SET status='$status' WHERE id=$id");
    redirect('view-requests.php');
}

// Delete request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_requests WHERE id=$id");
    redirect('view-requests.php');
}

$requests = $conn->query("SELECT * FROM service_requests ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Service Requests | Admin</title>
    <link rel="icon" href="../images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #1a2a3a; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-header { padding: 30px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { font-size: 24px; }
        .sidebar-header span { color: #ff6b6b; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 30px; color: #cbd5e0; text-decoration: none; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,107,107,0.1); color: #ff6b6b; }
        .main-content { margin-left: 280px; flex: 1; padding: 30px; }
        .top-bar { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; }
        .requests-table { background: white; border-radius: 12px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eef2f6; }
        th { background: #f8f9fa; font-weight: 600; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-contacted { background: #cce5ff; color: #004085; }
        .status-in_progress { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .action-buttons a { margin-right: 10px; text-decoration: none; padding: 5px 12px; border-radius: 5px; font-size: 12px; }
        .btn-contacted { background: #17a2b8; color: white; }
        .btn-progress { background: #ffc107; color: #333; }
        .btn-completed { background: #28a745; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header"><h3>Dan<span>Creatives</span></h3><p style="font-size:12px;margin-top:10px;">Admin Panel</p></div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage-courses.php"><i class="fas fa-book"></i> Courses</a>
                <a href="manage-products.php"><i class="fas fa-box"></i> Products</a>
                <a href="manage-services.php"><i class="fas fa-paint-brush"></i> Services</a>
                <a href="view-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="view-requests.php" class="active"><i class="fas fa-envelope"></i> Requests</a>
                <a href="manage-questions.php"><i class="fas fa-question-circle"></i> Messages</a>
                <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
                <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar"><h2>Service Requests</h2></div>
            <div class="requests-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Service</th><th>Client</th><th>Contact</th><th>Budget</th><th>Deadline</th><th>Requirements</th><th>Status</th><th>Date</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($req = $requests->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $req['id']; ?></td>
                            <td><?php echo htmlspecialchars($req['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($req['customer_name']); ?></td>
                            <td>
                                📧 <?php echo htmlspecialchars($req['customer_email']); ?><br>
                                📞 <?php echo htmlspecialchars($req['customer_phone']); ?><br>
                                📱 <?php echo htmlspecialchars($req['customer_telegram']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($req['budget']); ?></td>
                            <td><?php echo htmlspecialchars($req['deadline']); ?></td>
                            <td style="max-width: 200px;"><?php echo htmlspecialchars(substr($req['requirements'], 0, 50)); ?>...</td>
                            <td>
                                <span class="status-badge status-<?php echo str_replace('_', '-', $req['status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $req['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($req['status'] == 'pending'): ?>
                                    <a href="?status=contacted&id=<?php echo $req['id']; ?>" class="btn-contacted">Contacted</a>
                                <?php endif; ?>
                                <?php if($req['status'] == 'contacted'): ?>
                                    <a href="?status=in_progress&id=<?php echo $req['id']; ?>" class="btn-progress">In Progress</a>
                                <?php endif; ?>
                                <?php if($req['status'] == 'in_progress'): ?>
                                    <a href="?status=completed&id=<?php echo $req['id']; ?>" class="btn-completed">Complete</a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $req['id']; ?>" class="btn-delete" onclick="return confirm('Delete this request?')">Delete</a>
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