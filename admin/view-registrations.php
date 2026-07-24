<?php
// Fix the config path - go up one level to root
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle registration approval/rejection
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $conn->query("UPDATE registrations SET status = 'approved' WHERE id = $id");
    redirect('view-registrations.php');
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $conn->query("UPDATE registrations SET status = 'rejected' WHERE id = $id");
    redirect('view-registrations.php');
}

// Handle registration deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get file path to delete
    $result = $conn->query("SELECT payment_receipt FROM registrations WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        if (!empty($row['payment_receipt']) && file_exists('../' . $row['payment_receipt'])) {
            unlink('../' . $row['payment_receipt']);
        }
    }
    
    $conn->query("DELETE FROM registrations WHERE id = $id");
    redirect('view-registrations.php');
}

// Filter and search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

$where = [];
if ($search) {
    $where[] = "(first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}
if ($status_filter) {
    $where[] = "status = '$status_filter'";
}

$where_sql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

$registrations = $conn->query("SELECT * FROM registrations $where_sql ORDER BY id DESC");

// Get statistics with error checking
$total_pending = 0;
$total_approved = 0;
$total_rejected = 0;
$total_registrations = 0;
$with_receipts = 0;

$pending_result = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'pending'");
if ($pending_result) $total_pending = $pending_result->fetch_assoc()['count'];

$approved_result = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'approved'");
if ($approved_result) $total_approved = $approved_result->fetch_assoc()['count'];

$rejected_result = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'rejected'");
if ($rejected_result) $total_rejected = $rejected_result->fetch_assoc()['count'];

$total_result = $conn->query("SELECT COUNT(*) as count FROM registrations");
if ($total_result) $total_registrations = $total_result->fetch_assoc()['count'];

$receipt_result = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE payment_receipt IS NOT NULL AND payment_receipt != ''");
if ($receipt_result) $with_receipts = $receipt_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations - Admin Panel | Dan Creatives</title>
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
            background: #1a1a2e;
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
            color: #ff4757;
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
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
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
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .top-bar h2 {
            color: #1a2a3a;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-info h3 {
            font-size: 12px;
            color: #718096;
            margin-bottom: 8px;
        }
        
        .stat-info .number {
            font-size: 28px;
            font-weight: 800;
            color: #1a2a3a;
        }
        
        .stat-icon {
            font-size: 40px;
            color: #ff4757;
            opacity: 0.7;
        }
        
        .filter-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-bar input, .filter-bar select {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
        }
        
        .filter-bar button {
            background: #ff4757;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .filter-bar .btn-reset {
            background: #6c757d;
            text-decoration: none;
            display: inline-block;
        }
        
        .registrations-table {
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
            background: #fafafa;
            font-weight: 600;
            color: #1a2a3a;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: #fafafa;
        }
        
        .receipt-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ff4757;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            transition: 0.3s;
        }
        
        .receipt-link:hover {
            background: #ff6b81;
            transform: translateY(-2px);
        }
        
        .no-receipt {
            color: #999;
            font-style: italic;
            font-size: 12px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .action-buttons a {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 11px;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .btn-approve {
            background: #28a745;
            color: white;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete {
            background: #6c757d;
            color: white;
        }
        
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        
        .btn-approve:hover, .btn-reject:hover, .btn-delete:hover, .btn-view:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            max-width: 90%;
            max-height: 90%;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .modal-content img {
            max-width: 100%;
            max-height: 80vh;
            display: block;
        }
        
        .modal-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: rgba(0,0,0,0.8);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            th, td {
                padding: 10px;
                font-size: 12px;
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
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
                <a href="manage-products.php"><i class="fas fa-box"></i> Manage Products</a>
                <a href="manage-services.php"><i class="fas fa-paint-brush"></i> Manage Services</a>
                <a href="view-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="view-requests.php"><i class="fas fa-envelope"></i> Requests</a>
                <a href="manage-questions.php"><i class="fas fa-question-circle"></i> Messages</a>
                <a href="view-registrations.php" class="active"><i class="fas fa-users"></i> Registrations</a>
                <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2><i class="fas fa-users"></i> Student Registrations</h2>
                <a href="logout.php" style="background: #ff4757; color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Total Registrations</h3>
                        <div class="number"><?php echo $total_registrations; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Pending Review</h3>
                        <div class="number"><?php echo $total_pending; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Approved</h3>
                        <div class="number"><?php echo $total_approved; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Rejected</h3>
                        <div class="number"><?php echo $total_rejected; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>With Payment Receipt</h3>
                        <div class="number"><?php echo $with_receipts; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                </div>
            </div>
            
            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <input type="text" name="search" placeholder="Search by name, email, phone..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1;">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    <button type="submit"><i class="fas fa-search"></i> Filter</button>
                    <a href="view-registrations.php" class="btn-reset" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </form>
            </div>
            
            <!-- Registrations Table -->
            <div class="registrations-table">
                <?php if($registrations && $registrations->num_rows > 0): ?>
                 <table>
                    <thead>
                         <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Course</th>
                            <th>Payment Receipt</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php while($reg = $registrations->fetch_assoc()): 
                            $receipt_path = $reg['payment_receipt'];
                            $full_path = '../' . $receipt_path;
                            $has_receipt = !empty($receipt_path) && file_exists($full_path);
                        ?>
                         <tr>
                            <td><strong>#<?php echo $reg['id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></strong>
                                <br>
                                <small style="color: #718096;"><?php echo htmlspecialchars($reg['email']); ?></small>
                            </td>
                            <td>
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($reg['phone']); ?>
                            </td>
                            <td>
                                <span style="background: #e9ecef; padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                    <?php echo htmlspecialchars($reg['course']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($has_receipt): ?>
                                    <?php 
                                    $ext = strtolower(pathinfo($receipt_path, PATHINFO_EXTENSION));
                                    if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): 
                                    ?>
                                        <a href="javascript:void(0)" onclick="viewImage('../<?php echo $receipt_path; ?>')" class="receipt-link">
                                            <i class="fas fa-image"></i> View Receipt
                                        </a>
                                    <?php else: ?>
                                        <a href="../<?php echo $receipt_path; ?>" target="_blank" class="receipt-link">
                                            <i class="fas fa-file-pdf"></i> View PDF
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="no-receipt">
                                        <i class="fas fa-times-circle"></i> No receipt
                                    </span>
                                    <?php if(!empty($receipt_path)): ?>
                                        <br><small style="color: #dc3545;">File missing: <?php echo basename($receipt_path); ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $reg['status']; ?>">
                                    <?php echo ucfirst($reg['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($reg['created_at'])); ?>
                                <br>
                                <small><?php echo date('h:i A', strtotime($reg['created_at'])); ?></small>
                            </td>
                            <td class="action-buttons">
                                <?php if($reg['status'] == 'pending'): ?>
                                    <a href="?approve=<?php echo $reg['id']; ?>" class="btn-approve" onclick="return confirm('Approve this registration?')">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <a href="?reject=<?php echo $reg['id']; ?>" class="btn-reject" onclick="return confirm('Reject this registration?')">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                <?php endif; ?>
                                <?php if($has_receipt): ?>
                                    <a href="javascript:void(0)" onclick="viewImage('../<?php echo $receipt_path; ?>')" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $reg['id']; ?>" class="btn-delete" onclick="return confirm('Delete this registration? This will also delete the receipt.')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                         </tr>
                        <?php endwhile; ?>
                    </tbody>
                 </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-users" style="font-size: 48px; color: #ccc;"></i>
                        <h3 style="margin-top: 20px; color: #718096;">No registrations found</h3>
                        <p>No student registrations match your search criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="modal" onclick="closeImageModal()">
        <div class="modal-close">&times;</div>
        <div class="modal-content">
            <img id="modalImage" src="" alt="Payment Receipt">
        </div>
    </div>
    
    <script>
        function viewImage(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('imageModal').style.display = 'flex';
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>