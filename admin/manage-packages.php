<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_packages WHERE id = $id");
    redirect('manage-packages.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE service_packages SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('manage-packages.php');
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $service_id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $package_name = $conn->real_escape_string($_POST['package_name']);
    $package_price = $conn->real_escape_string($_POST['package_price']);
    $features = $conn->real_escape_string($_POST['features']);
    
    if ($id > 0) {
        $sql = "UPDATE service_packages SET 
                service_id=$service_id, 
                service_name='$service_name',
                package_name='$package_name', 
                package_price='$package_price', 
                features='$features' 
                WHERE id=$id";
    } else {
        $result = $conn->query("SELECT MAX(display_order) as max FROM service_packages WHERE service_id=$service_id");
        $max_order = $result->fetch_assoc()['max'];
        $display_order = ($max_order !== null) ? $max_order + 1 : 1;
        
        $sql = "INSERT INTO service_packages (service_id, service_name, package_name, package_price, features, display_order) 
                VALUES ($service_id, '$service_name', '$package_name', '$package_price', '$features', $display_order)";
    }
    
    if ($conn->query($sql)) {
        $success = $id > 0 ? "Package updated!" : "Package added!";
    } else {
        $error = "Failed to save: " . $conn->error;
    }
}

// Handle reorder (AJAX)
if (isset($_POST['action']) && $_POST['action'] == 'reorder') {
    header('Content-Type: application/json');
    $orders = json_decode($_POST['orders'], true);
    if (is_array($orders)) {
        foreach ($orders as $order) {
            $id = (int)$order['id'];
            $display_order = (int)$order['order'];
            $conn->query("UPDATE service_packages SET display_order = $display_order WHERE id = $id");
        }
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

$services = $conn->query("SELECT id, title FROM services WHERE status='active' ORDER BY title");
$packages = $conn->query("SELECT p.*, s.title as service_title 
                          FROM service_packages p 
                          LEFT JOIN services s ON p.service_id = s.id 
                          ORDER BY p.service_id, p.display_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages | Admin Panel</title>
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
        .top-bar { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .btn-add { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .packages-table { background: white; border-radius: 12px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eef2f6; }
        th { background: #f8f9fa; font-weight: 600; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .action-buttons a { margin-right: 10px; text-decoration: none; padding: 5px 12px; border-radius: 5px; font-size: 12px; display: inline-block; }
        .btn-edit { background: #007bff; color: white; }
        .btn-toggle { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .drag-handle { cursor: grab; color: #94a3b8; font-size: 18px; width: 40px; text-align: center; }
        .drag-handle:active { cursor: grabbing; }
        .dragging { opacity: 0.4; }
        .drag-over { border-top: 3px solid #ff6b6b; }
        .reorder-info { background: #e2e8f0; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: inline-block; }
        .order-toast { position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; z-index: 9999; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto; }
        .modal-content { background: white; width: 90%; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 12px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .form-group textarea { min-height: 100px; }
        .btn-submit { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .error-message { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .feature-list { font-size: 12px; max-width: 250px; }
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
                <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
                <a href="manage-packages.php" class="active"><i class="fas fa-tags"></i> Packages</a>
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
                <h2><i class="fas fa-tags"></i> Service Packages</h2>
                <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Package</button>
            </div>
            
            <div class="reorder-info">
                <i class="fas fa-arrows-alt"></i> Drag and drop to reorder packages within each service
            </div>
            
            <?php if(isset($success)): ?>
                <div class="success-message">✓ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="error-message">✗ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="packages-table">
                <table id="packages-table">
                    <thead>
                        <tr>
                            <th style="width:40px;"></th>
                            <th style="width:50px;">ID</th>
                            <th>Service</th>
                            <th>Package Name</th>
                            <th>Price</th>
                            <th>Features</th>
                            <th>Status</th>
                            <th style="width:60px;">Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="packages-tbody">
                        <?php 
                        $current_service = '';
                        $order_counter = 1;
                        while($pkg = $packages->fetch_assoc()): 
                            if($current_service != $pkg['service_id']) {
                                $order_counter = 1;
                                $current_service = $pkg['service_id'];
                            }
                            $features_list = explode('|', $pkg['features']);
                            $features_short = implode(', ', array_slice($features_list, 0, 3));
                            if(count($features_list) > 3) $features_short .= '...';
                        ?>
                        <tr data-id="<?php echo $pkg['id']; ?>" data-service-id="<?php echo $pkg['service_id']; ?>">
                            <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                            <td><?php echo $pkg['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($pkg['service_title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($pkg['package_name']); ?></td>
                            <td><?php echo htmlspecialchars($pkg['package_price']); ?></td>
                            <td class="feature-list"><?php echo htmlspecialchars($features_short); ?></td>
                            <td><span class="status-badge <?php echo $pkg['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $pkg['status']; ?></span></td>
                            <td class="order-display"><?php echo $order_counter++; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditModal(<?php echo $pkg['id']; ?>)">Edit</a>
                                <a href="?toggle=<?php echo $pkg['id']; ?>" class="btn-toggle">Toggle</a>
                                <a href="?delete=<?php echo $pkg['id']; ?>" class="btn-delete" onclick="return confirm('Delete this package?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if($packages->num_rows == 0): ?>
                <div style="text-align:center; padding:60px;">
                    <i class="fas fa-tags" style="font-size:48px; color:#ccc;"></i>
                    <h3 style="margin-top:20px;">No Packages</h3>
                    <p>Click "Add Package" to create packages for your services.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 id="modalTitle">Add Package</h3>
                <span onclick="closeModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
            </div>
            <form method="POST" id="packageForm">
                <input type="hidden" name="id" id="package_id">
                <div class="form-group">
                    <label>Service *</label>
                    <select name="service_id" id="service_id" required onchange="updateServiceName()">
                        <option value="">Select Service</option>
                        <?php 
                        $services->data_seek(0);
                        while($s = $services->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="hidden" name="service_name" id="service_name">
                </div>
                <div class="form-group">
                    <label>Package Name *</label>
                    <input type="text" name="package_name" id="package_name" placeholder="e.g., Standard Package, Premium Package" required>
                </div>
                <div class="form-group">
                    <label>Price *</label>
                    <input type="text" name="package_price" id="package_price" placeholder="e.g., 1,500 Birr" required>
                </div>
                <div class="form-group">
                    <label>Features (separate with | pipe symbol)</label>
                    <textarea name="features" id="features" rows="4" placeholder="Logo Design|Basic Brand Colors|2 Revisions|3 Days Delivery" required></textarea>
                    <small style="color:#718096;">Use | (pipe) to separate each feature</small>
                </div>
                <button type="submit" class="btn-submit">Save Package</button>
            </form>
        </div>
    </div>
    
    <script>
        let packagesData = {};
        <?php
        $all_packages = $conn->query("SELECT * FROM service_packages");
        while($row = $all_packages->fetch_assoc()) {
            echo "packagesData[{$row['id']}] = " . json_encode($row) . ";\n";
        }
        ?>
        
        function updateServiceName() {
            let select = document.getElementById('service_id');
            let text = select.options[select.selectedIndex]?.text || '';
            document.getElementById('service_name').value = text;
        }
        
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Package';
            document.getElementById('packageForm').reset();
            document.getElementById('package_id').value = '';
            document.getElementById('packageModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            let pkg = packagesData[id];
            if(pkg) {
                document.getElementById('modalTitle').innerText = 'Edit Package';
                document.getElementById('package_id').value = pkg.id;
                document.getElementById('service_id').value = pkg.service_id;
                document.getElementById('service_name').value = pkg.service_name;
                document.getElementById('package_name').value = pkg.package_name;
                document.getElementById('package_price').value = pkg.package_price;
                document.getElementById('features').value = pkg.features;
                document.getElementById('packageModal').style.display = 'block';
            }
        }
        
        function closeModal() {
            document.getElementById('packageModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('packageModal')) {
                closeModal();
            }
        }
        
        // Drag and Drop Reordering
        let dragSrcElement = null;
        
        function handleDragStart(e) {
            dragSrcElement = this;
            e.dataTransfer.effectAllowed = 'move';
            this.classList.add('dragging');
        }
        
        function handleDragOver(e) {
            e.preventDefault();
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
            e.preventDefault();
            e.stopPropagation();
            
            const dragServiceId = dragSrcElement.getAttribute('data-service-id');
            const dropServiceId = this.getAttribute('data-service-id');
            
            if (dragServiceId !== dropServiceId) {
                showToast('✗ Cannot reorder across different services', 'error');
                this.classList.remove('drag-over');
                return false;
            }
            
            if (dragSrcElement !== this) {
                const tbody = document.getElementById('packages-tbody');
                const rows = Array.from(tbody.children);
                const dragIndex = rows.indexOf(dragSrcElement);
                const dropIndex = rows.indexOf(this);
                
                if (dragIndex < dropIndex) {
                    this.parentNode.insertBefore(dragSrcElement, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(dragSrcElement, this);
                }
                
                updateOrderNumbers();
                saveOrder();
            }
            
            this.classList.remove('drag-over');
            return false;
        }
        
        function handleDragEnd(e) {
            this.classList.remove('dragging');
            const rows = document.querySelectorAll('#packages-tbody tr');
            rows.forEach(row => row.classList.remove('drag-over'));
        }
        
        function updateOrderNumbers() {
            const rows = document.querySelectorAll('#packages-tbody tr');
            let currentService = '';
            let orderCounter = 1;
            
            rows.forEach((row, index) => {
                const serviceId = row.getAttribute('data-service-id');
                if (currentService !== serviceId) {
                    currentService = serviceId;
                    orderCounter = 1;
                }
                const orderCell = row.querySelector('.order-display');
                if (orderCell) orderCell.textContent = orderCounter++;
            });
        }
        
        function saveOrder() {
            const rows = document.querySelectorAll('#packages-tbody tr');
            const orders = [];
            
            rows.forEach((row, index) => {
                const id = row.getAttribute('data-id');
                orders.push({ id: id, order: index + 1 });
            });
            
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=reorder&orders=' + encodeURIComponent(JSON.stringify(orders))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) showToast('✓ Order updated!');
                else showToast('✗ Failed to update', 'error');
            })
            .catch(() => showToast('✗ Network error', 'error'));
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'order-toast';
            toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#packages-tbody tr');
            rows.forEach(row => {
                row.setAttribute('draggable', 'true');
                row.addEventListener('dragstart', handleDragStart);
                row.addEventListener('dragover', handleDragOver);
                row.addEventListener('dragenter', handleDragEnter);
                row.addEventListener('dragleave', handleDragLeave);
                row.addEventListener('drop', handleDrop);
                row.addEventListener('dragend', handleDragEnd);
            });
        });
    </script>
</body>
</html>