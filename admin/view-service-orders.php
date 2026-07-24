<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle order status update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $conn->real_escape_string($_GET['status']);
    $conn->query("UPDATE service_registrations SET status='$status' WHERE id=$id");
    redirect('view-service-orders.php');
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_registrations WHERE id=$id");
    redirect('view-service-orders.php');
}

// Filter by status
$status_filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
$where = $status_filter ? "WHERE status='$status_filter'" : "";

$orders = $conn->query("SELECT * FROM service_registrations $where ORDER BY id DESC");

// Get counts for statistics
$total_pending = $conn->query("SELECT COUNT(*) as count FROM service_registrations WHERE status='pending'")->fetch_assoc()['count'];
$total_processing = $conn->query("SELECT COUNT(*) as count FROM service_registrations WHERE status='processing'")->fetch_assoc()['count'];
$total_completed = $conn->query("SELECT COUNT(*) as count FROM service_registrations WHERE status='completed'")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM service_registrations")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Orders | Admin Panel</title>
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
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .top-bar h2 {
            color: #1a2a3a;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            color: #ff6b6b;
            opacity: 0.7;
        }
        
        .filter-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-bar a {
            padding: 8px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #1a2a3a;
            transition: 0.3s;
            font-size: 14px;
        }
        
        .filter-bar a.active, .filter-bar a:hover {
            background: #ff6b6b;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        .orders-table {
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
            color: #1a2a3a;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: #fafbfe;
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
        
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
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
        
        .btn-processing {
            background: #17a2b8;
            color: white;
        }
        
        .btn-completed {
            background: #28a745;
            color: white;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-view {
            background: #007bff;
            color: white;
        }
        
        .btn-processing:hover, .btn-completed:hover, .btn-delete:hover, .btn-view:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }
        
        .no-orders {
            text-align: center;
            padding: 60px;
        }
        
        .no-orders i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .order-detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .modal-content-detail {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 20px;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header-detail {
            background: linear-gradient(135deg, #1a2a3a, #2d3e4e);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header-detail h3 {
            font-size: 1.2rem;
        }
        
        .close-detail-modal {
            font-size: 28px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .close-detail-modal:hover {
            transform: rotate(90deg);
        }
        
        .modal-body-detail {
            padding: 25px;
        }
        
        .detail-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eef2f6;
        }
        
        .detail-section h4 {
            color: #ff6b6b;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .detail-section p {
            margin: 8px 0;
            color: #4a5568;
        }
        
        .detail-section strong {
            color: #1a2a3a;
            width: 130px;
            display: inline-block;
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
                <p style="font-size:12px;margin-top:10px;">Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="dashboard.php#servicesSection"><i class="fas fa-paint-brush"></i> Services</a>
                <a href="dashboard.php#productsSection"><i class="fas fa-box"></i> Products</a>
                <a href="dashboard.php#coursesSection"><i class="fas fa-book"></i> Courses</a>
                <a href="dashboard.php#product_ordersSection"><i class="fas fa-shopping-cart"></i> Product Orders</a>
                <a href="view-service-orders.php" class="active"><i class="fas fa-paint-brush"></i> Service Orders</a>
                <a href="dashboard.php#registrationsSection"><i class="fas fa-users"></i> Registrations</a>
                <a href="dashboard.php#questionsSection"><i class="fas fa-question-circle"></i> Questions</a>
                <a href="dashboard.php#aboutSection"><i class="fas fa-cog"></i> Settings</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2><i class="fas fa-paint-brush"></i> Service Orders (Portfolio Registrations)</h2>
                <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <div class="number"><?php echo $total_orders; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <div class="number"><?php echo $total_pending; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Processing</h3>
                        <div class="number"><?php echo $total_processing; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Completed</h3>
                        <div class="number"><?php echo $total_completed; ?></div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            
            <!-- Filter Bar -->
            <div class="filter-bar">
                <a href="view-service-orders.php" class="<?php echo !$status_filter ? 'active' : ''; ?>">All Orders</a>
                <a href="?filter=pending" class="<?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="?filter=processing" class="<?php echo $status_filter == 'processing' ? 'active' : ''; ?>">Processing</a>
                <a href="?filter=completed" class="<?php echo $status_filter == 'completed' ? 'active' : ''; ?>">Completed</a>
            </div>
            
            <!-- Orders Table -->
            <div class="orders-table">
                <?php if($orders && $orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Package</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Email / Telegram</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($order['service']); ?></td>
                            <td><?php echo htmlspecialchars($order['package_name']); ?> (<?php echo $order['package_price']; ?>)</td>
                            <td><?php echo htmlspecialchars($order['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($order['phone']); ?></td>
                            <td>
                                <?php if($order['email']): ?>
                                📧 <?php echo htmlspecialchars($order['email']); ?><br>
                                <?php endif; ?>
                                <?php if($order['telegram']): ?>
                                📱 <?php echo htmlspecialchars($order['telegram']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?><br>
                                <small><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                            </td>
                            <td class="action-buttons">
                                <a href="#" class="btn-view" onclick="viewOrderDetail(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if($order['status'] == 'pending'): ?>
                                    <a href="?status=processing&id=<?php echo $order['id']; ?>" class="btn-processing" onclick="return confirm('Mark as processing?')">
                                        <i class="fas fa-play"></i> Process
                                    </a>
                                <?php endif; ?>
                                <?php if($order['status'] == 'processing'): ?>
                                    <a href="?status=completed&id=<?php echo $order['id']; ?>" class="btn-completed" onclick="return confirm('Mark as completed?')">
                                        <i class="fas fa-check"></i> Complete
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $order['id']; ?>" class="btn-delete" onclick="return confirm('Delete this order?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>No Service Orders Yet</h3>
                    <p>Customers will appear here when they register for services from the portfolio page</p>
                    <a href="dashboard.php" class="btn-processing" style="display: inline-block; margin-top: 20px;">Back to Dashboard</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Order Detail Modal -->
    <div id="orderDetailModal" class="order-detail-modal" onclick="closeDetailModal()">
        <div class="modal-content-detail" onclick="event.stopPropagation()">
            <div class="modal-header-detail">
                <h3><i class="fas fa-paint-brush"></i> Service Order Details</h3>
                <span class="close-detail-modal" onclick="closeDetailModal()">&times;</span>
            </div>
            <div class="modal-body-detail" id="orderDetailContent">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
    
    <script>
        // Store orders data for modal
        let ordersData = {};
        <?php
        $all_orders_data = $conn->query("SELECT * FROM service_registrations ORDER BY id DESC");
        while($row = $all_orders_data->fetch_assoc()) {
            $row['created_at_formatted'] = date('F d, Y h:i A', strtotime($row['created_at']));
            echo "ordersData[{$row['id']}] = " . json_encode($row) . ";\n";
        }
        ?>
        
        function viewOrderDetail(orderId) {
            let order = ordersData[orderId];
            if(order) {
                let html = `
                    <div class="detail-section">
                        <h4><i class="fas fa-info-circle"></i> Order Information</h4>
                        <p><strong>Order ID:</strong> #${order.id}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></p>
                        <p><strong>Date:</strong> ${order.created_at_formatted}</p>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-paint-brush"></i> Service & Package</h4>
                        <p><strong>Service:</strong> ${escapeHtml(order.service)}</p>
                        <p><strong>Package:</strong> ${escapeHtml(order.package_name)}</p>
                        <p><strong>Price:</strong> ${escapeHtml(order.package_price)}</p>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-user"></i> Customer Information</h4>
                        <p><strong>Full Name:</strong> ${escapeHtml(order.fullname)}</p>
                        <p><strong>Phone:</strong> ${escapeHtml(order.phone)}</p>
                        ${order.email ? `<p><strong>Email:</strong> ${escapeHtml(order.email)}</p>` : ''}
                        ${order.telegram ? `<p><strong>Telegram:</strong> ${escapeHtml(order.telegram)}</p>` : ''}
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-file-alt"></i> Additional Notes</h4>
                        <p>${order.notes ? escapeHtml(order.notes) : '<em>No additional notes</em>'}</p>
                    </div>
                `;
                document.getElementById('orderDetailContent').innerHTML = html;
                document.getElementById('orderDetailModal').style.display = 'flex';
            }
        }
        
        function closeDetailModal() {
            document.getElementById('orderDetailModal').style.display = 'none';
        }
        
        function escapeHtml(text) {
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>
</body>
</html>