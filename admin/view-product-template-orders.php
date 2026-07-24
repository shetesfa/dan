<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Update order status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $conn->real_escape_string($_GET['status']);
    $conn->query("UPDATE product_template_orders SET status='$status' WHERE id=$id");
    redirect('view-product-template-orders.php');
}

// Delete order
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM product_template_orders WHERE id=$id");
    redirect('view-product-template-orders.php');
}

// Filter by status
$status_filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
$where = $status_filter ? "WHERE status='$status_filter'" : "";

$orders = $conn->query("SELECT * FROM product_template_orders $where ORDER BY id DESC");

// Get counts for statistics
$total_pending = $conn->query("SELECT COUNT(*) as count FROM product_template_orders WHERE status='pending'")->fetch_assoc()['count'];
$total_processing = $conn->query("SELECT COUNT(*) as count FROM product_template_orders WHERE status='processing'")->fetch_assoc()['count'];
$total_completed = $conn->query("SELECT COUNT(*) as count FROM product_template_orders WHERE status='completed'")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM product_template_orders")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Template Orders | Admin Panel</title>
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
        
        .template-preview {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .template-preview img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
            width: 120px;
            display: inline-block;
        }
        
        .template-preview-large {
            display: flex;
            gap: 15px;
            align-items: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin-top: 10px;
        }
        
        .template-preview-large img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
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
                <a href="manage-courses.php"><i class="fas fa-book"></i> Courses</a>
                <a href="manage-products.php"><i class="fas fa-box"></i> Products</a>
                <a href="manage-product-templates.php"><i class="fas fa-images"></i> Product Templates</a>
                <a href="view-product-template-orders.php" class="active"><i class="fas fa-shopping-cart"></i> Template Orders</a>
                <a href="manage-services.php"><i class="fas fa-paint-brush"></i> Services</a>
                <a href="view-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="view-requests.php"><i class="fas fa-envelope"></i> Requests</a>
                <a href="manage-questions.php"><i class="fas fa-question-circle"></i> Messages</a>
                <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
                <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2><i class="fas fa-palette"></i> Product Template Orders</h2>
                <a href="logout.php" style="background: #ff6b6b; color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
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
                <a href="view-product-template-orders.php" class="<?php echo !$status_filter ? 'active' : ''; ?>">All Orders</a>
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
                            <th>Design Style</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Qty</th>
                            <th>Size/Color</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td>
                                <div class="template-preview">
                                    <img src="<?php echo $order['template_image']; ?>" onerror="this.src='https://via.placeholder.com/50'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['template_name']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                            </td>
                            <td>
                                📧 <?php echo htmlspecialchars($order['customer_email']); ?><br>
                                📞 <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                <?php if($order['customer_telegram']): ?>
                                📱 <?php echo htmlspecialchars($order['customer_telegram']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td>
                                <?php if($order['size']): ?>📏 <?php echo $order['size']; ?><br><?php endif; ?>
                                <?php if($order['color']): ?>🎨 <?php echo $order['color']; ?><?php endif; ?>
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
                                    <a href="?status=processing&id=<?php echo $order['id']; ?>" class="btn-processing">
                                        <i class="fas fa-play"></i> Process
                                    </a>
                                <?php endif; ?>
                                <?php if($order['status'] == 'processing'): ?>
                                    <a href="?status=completed&id=<?php echo $order['id']; ?>" class="btn-completed">
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
                    <h3>No Template Orders Yet</h3>
                    <p>Customers will appear here when they order product templates</p>
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
                <h3><i class="fas fa-shopping-cart"></i> Order Details</h3>
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
        $all_orders_data = $conn->query("SELECT * FROM product_template_orders ORDER BY id DESC");
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
                        <h4><i class="fas fa-palette"></i> Design Details</h4>
                        <div class="template-preview-large">
                            <img src="${order.template_image}" onerror="this.src='https://via.placeholder.com/80'">
                            <div>
                                <p><strong>Style:</strong> ${escapeHtml(order.template_name)}</p>
                                <p><strong>Product:</strong> ${escapeHtml(order.product_name)}</p>
                                <p><strong>Quantity:</strong> ${order.quantity}</p>
                                ${order.size ? `<p><strong>Size:</strong> ${escapeHtml(order.size)}</p>` : ''}
                                ${order.color ? `<p><strong>Color:</strong> ${escapeHtml(order.color)}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-user"></i> Customer Information</h4>
                        <p><strong>Name:</strong> ${escapeHtml(order.customer_name)}</p>
                        <p><strong>Email:</strong> ${escapeHtml(order.customer_email)}</p>
                        <p><strong>Phone:</strong> ${escapeHtml(order.customer_phone)}</p>
                        ${order.customer_telegram ? `<p><strong>Telegram:</strong> ${escapeHtml(order.customer_telegram)}</p>` : ''}
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-file-alt"></i> Requirements</h4>
                        <p>${order.requirements ? escapeHtml(order.requirements) : '<em>No additional requirements</em>'}</p>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-info-circle"></i> Order Information</h4>
                        <p><strong>Order ID:</strong> #${order.id}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></p>
                        <p><strong>Date:</strong> ${order.created_at_formatted}</p>
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