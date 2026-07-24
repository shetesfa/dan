<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM services WHERE id = $id");
    redirect('manage-services.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE services SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('manage-services.php');
}

// Handle reorder (AJAX)
if (isset($_POST['action']) && $_POST['action'] == 'reorder') {
    header('Content-Type: application/json');
    $orders = json_decode($_POST['orders'], true);
    if (is_array($orders)) {
        foreach ($orders as $order) {
            $id = (int)$order['id'];
            $display_order = (int)$order['order'];
            $conn->query("UPDATE services SET display_order = $display_order WHERE id = $id");
        }
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $features = $conn->real_escape_string($_POST['features']);
    $price = $conn->real_escape_string($_POST['price']);
    $status = $conn->real_escape_string($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    if ($id > 0) {
        $sql = "UPDATE services SET title='$title', description='$description', icon_class='$icon_class', features='$features', price='$price', status='$status', display_order=$display_order WHERE id=$id";
    } else {
        // Get max display_order for new item
        $result = $conn->query("SELECT MAX(display_order) as max FROM services");
        $max_order = $result->fetch_assoc()['max'];
        $display_order = ($max_order !== null) ? $max_order + 1 : 1;
        $sql = "INSERT INTO services (title, description, icon_class, features, price, status, display_order) VALUES ('$title', '$description', '$icon_class', '$features', '$price', '$status', $display_order)";
    }
    
    if ($conn->query($sql)) {
        $success = $id > 0 ? "Service updated!" : "Service added!";
    } else {
        $error = "Failed to save service: " . $conn->error;
    }
}

$services = $conn->query("SELECT * FROM services ORDER BY display_order ASC, id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | Admin</title>
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
        .top-bar { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .btn-add { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .services-table { background: white; border-radius: 12px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eef2f6; }
        th { background: #f8f9fa; font-weight: 600; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .action-buttons a { margin-right: 10px; text-decoration: none; padding: 5px 12px; border-radius: 5px; font-size: 12px; }
        .btn-edit { background: #007bff; color: white; }
        .btn-toggle { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto; }
        .modal-content { background: white; width: 90%; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 12px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .form-group textarea { min-height: 80px; }
        .btn-submit { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .error-message { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        
        /* Drag and Drop Styles */
        .drag-handle {
            cursor: grab;
            color: #94a3b8;
            font-size: 18px;
            width: 40px;
            text-align: center;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        .drag-handle:hover {
            color: #ff6b6b;
        }
        .dragging {
            opacity: 0.4;
            background: #f1f5f9;
        }
        .drag-over {
            border-top: 3px solid #ff6b6b;
        }
        tbody tr {
            cursor: default;
            transition: all 0.2s;
        }
        .reorder-info {
            background: #e2e8f0;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #475569;
            display: inline-block;
        }
        .reorder-info i {
            color: #ff6b6b;
            margin-right: 8px;
        }
        .order-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
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
                <a href="manage-services.php" class="active"><i class="fas fa-paint-brush"></i> Services</a>
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
                <h2>Manage Services</h2>
                <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Service</button>
            </div>
            
            <div class="reorder-info">
                <i class="fas fa-arrows-alt"></i> Drag and drop rows by clicking and holding the <i class="fas fa-grip-vertical"></i> icon to reorder services
            </div>
            
            <?php if(isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="services-table">
                <table id="services-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 60px;">Icon</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th style="width: 60px;">Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="services-tbody">
                        <?php 
                        $order_counter = 1;
                        while($s = $services->fetch_assoc()): 
                        ?>
                        <tr data-id="<?php echo $s['id']; ?>">
                            <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                            <td><?php echo $s['id']; ?></td>
                            <td><i class="<?php echo $s['icon_class']; ?>" style="font-size:24px; color:#ff6b6b;"></i></td>
                            <td><?php echo htmlspecialchars($s['title']); ?></td>
                            <td><?php echo htmlspecialchars($s['price']); ?></td>
                            <td><span class="status-badge <?php echo $s['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $s['status']; ?></span></td>
                            <td class="order-display"><?php echo $order_counter++; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditModal(<?php echo $s['id']; ?>)">Edit</a>
                                <a href="?toggle=<?php echo $s['id']; ?>" class="btn-toggle">Toggle</a>
                                <a href="?delete=<?php echo $s['id']; ?>" class="btn-delete" onclick="return confirm('Delete this service?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="serviceModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 id="modalTitle">Add Service</h3>
                <span onclick="closeModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
            </div>
            <form method="POST" id="serviceForm">
                <input type="hidden" name="id" id="service_id">
                <div class="form-group"><label>Title *</label><input type="text" name="title" id="title" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" id="description"></textarea></div>
                <div class="form-group"><label>Icon Class *</label><input type="text" name="icon_class" id="icon_class" placeholder="fas fa-paint-brush" required></div>
                <div class="form-group"><label>Features (separate with | )</label><textarea name="features" id="features" placeholder="Feature 1|Feature 2|Feature 3"></textarea></div>
                <div class="form-group"><label>Price *</label><input type="text" name="price" id="price" placeholder="From 1,500 Birr" required></div>
                <div class="form-group"><label>Status</label><select name="status" id="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                <div class="form-group"><label>Display Order</label><input type="number" name="display_order" id="display_order" value="0" readonly style="background:#f0f0f0;"></div>
                <button type="submit" class="btn-submit">Save Service</button>
            </form>
        </div>
    </div>
    
    <script>
        <?php 
        $services_data = []; 
        $all = $conn->query("SELECT * FROM services"); 
        while($row = $all->fetch_assoc()) { 
            $services_data[$row['id']] = $row; 
        } 
        ?>
        let services = <?php echo json_encode($services_data); ?>;
        
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Service';
            document.getElementById('serviceForm').reset();
            document.getElementById('service_id').value = '';
            document.getElementById('display_order').value = '';
            document.getElementById('serviceModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            let s = services[id];
            if(s) {
                document.getElementById('modalTitle').innerText = 'Edit Service';
                document.getElementById('service_id').value = s.id;
                document.getElementById('title').value = s.title;
                document.getElementById('description').value = s.description;
                document.getElementById('icon_class').value = s.icon_class;
                document.getElementById('features').value = s.features;
                document.getElementById('price').value = s.price;
                document.getElementById('status').value = s.status;
                document.getElementById('display_order').value = s.display_order;
                document.getElementById('serviceModal').style.display = 'block';
            }
        }
        
        function closeModal() { 
            document.getElementById('serviceModal').style.display = 'none'; 
        }
        
        window.onclick = function(event) { 
            if (event.target == document.getElementById('serviceModal')) closeModal(); 
        }
        
        // Drag and Drop Reordering
        let dragSrcElement = null;
        
        function handleDragStart(e) {
            dragSrcElement = this;
            e.dataTransfer.effectAllowed = 'move';
            this.classList.add('dragging');
        }
        
        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }
        
        function handleDragEnter(e) {
            this.classList.add('drag-over');
        }
        
        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }
        
        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            
            if (dragSrcElement !== this) {
                const tbody = document.getElementById('services-tbody');
                const rows = Array.from(tbody.children);
                
                const dragIndex = rows.indexOf(dragSrcElement);
                const dropIndex = rows.indexOf(this);
                
                if (dragIndex < dropIndex) {
                    this.parentNode.insertBefore(dragSrcElement, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(dragSrcElement, this);
                }
                
                // Update display order numbers in UI
                updateDisplayOrders();
                
                // Save new order to database
                saveNewOrder();
            }
            
            this.classList.remove('drag-over');
            return false;
        }
        
        function handleDragEnd(e) {
            this.classList.remove('dragging');
            const rows = document.querySelectorAll('#services-tbody tr');
            rows.forEach(row => {
                row.classList.remove('drag-over');
            });
        }
        
        function updateDisplayOrders() {
            const rows = document.querySelectorAll('#services-tbody tr');
            rows.forEach((row, index) => {
                const orderCell = row.querySelector('.order-display');
                if (orderCell) {
                    orderCell.textContent = index + 1;
                }
            });
        }
        
        function saveNewOrder() {
            const rows = document.querySelectorAll('#services-tbody tr');
            const orders = [];
            
            rows.forEach((row, index) => {
                const id = row.getAttribute('data-id');
                orders.push({
                    id: id,
                    order: index + 1
                });
            });
            
            // Send AJAX request to update order
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=reorder&orders=' + encodeURIComponent(JSON.stringify(orders))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('✓ Order updated successfully!');
                } else {
                    showToast('✗ Failed to update order', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving order:', error);
                showToast('✗ Network error. Please refresh and try again.', 'error');
            });
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'order-toast';
            toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }
        
        // Initialize drag and drop
        function initDragAndDrop() {
            const rows = document.querySelectorAll('#services-tbody tr');
            rows.forEach(row => {
                row.setAttribute('draggable', 'true');
                row.addEventListener('dragstart', handleDragStart);
                row.addEventListener('dragover', handleDragOver);
                row.addEventListener('dragenter', handleDragEnter);
                row.addEventListener('dragleave', handleDragLeave);
                row.addEventListener('drop', handleDrop);
                row.addEventListener('dragend', handleDragEnd);
            });
        }
        
        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', initDragAndDrop);
    </script>
</body>
</html>