<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    redirect('manage-products.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE products SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('manage-products.php');
}

// Handle reorder (AJAX)
if (isset($_POST['reorder'])) {
    header('Content-Type: application/json');
    $orders = json_decode($_POST['reorder'], true);
    $success = true;
    
    if (is_array($orders)) {
        foreach ($orders as $index => $id) {
            $id = (int)$id;
            $order_num = $index + 1;
            $conn->query("UPDATE products SET display_order = $order_num WHERE id = $id");
            if ($conn->affected_rows < 0 && $conn->error) {
                $success = false;
                break;
            }
        }
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reorder'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    
    if ($id > 0) {
        $sql = "UPDATE products SET title='$title', description='$description', price='$price', image_url='$image_url', category='$category', status='$status' WHERE id=$id";
    } else {
        // Get max display_order for new item
        $result = $conn->query("SELECT MAX(display_order) as max FROM products");
        $max_order = $result->fetch_assoc()['max'];
        $display_order = ($max_order !== null) ? $max_order + 1 : 1;
        $sql = "INSERT INTO products (title, description, price, image_url, category, status, display_order) VALUES ('$title', '$description', '$price', '$image_url', '$category', '$status', $display_order)";
    }
    
    if ($conn->query($sql)) {
        $success = $id > 0 ? "Product updated!" : "Product added!";
        // Redirect to refresh the order
        header("Location: manage-products.php?success=" . urlencode($success));
        exit;
    } else {
        $error = "Failed to save product: " . $conn->error;
    }
}

// Get success message from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Ensure all products have display_order values
$conn->query("UPDATE products SET display_order = id WHERE display_order IS NULL OR display_order = 0");

$products = $conn->query("SELECT * FROM products ORDER BY display_order ASC, id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin</title>
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
        .products-table {
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
        .product-img {
            width: 60px;
            height: 60px;
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
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-edit {
            background: #007bff;
            color: white;
        }
        .btn-toggle {
            background: #ffc107;
            color: #333;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
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
            overflow-y: auto;
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
        .form-group textarea {
            min-height: 80px;
        }
        .btn-submit {
            background: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        /* Drag and Drop Styles */
        .drag-handle {
            cursor: move;
            color: #94a3b8;
            font-size: 18px;
            width: 40px;
            text-align: center;
        }
        .drag-handle:hover {
            color: #ff6b6b;
        }
        .dragging {
            opacity: 0.5;
            background: #f1f5f9;
        }
        .drag-over {
            border-top: 3px solid #ff6b6b !important;
        }
        tbody tr {
            cursor: default;
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
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage-courses.php"><i class="fas fa-book"></i> Courses</a>
                <a href="manage-products.php" class="active"><i class="fas fa-box"></i> Products</a>
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
                <h2>Manage Products</h2>
                <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Product</button>
            </div>
            
            <div class="reorder-info">
                <i class="fas fa-arrows-alt"></i> Drag and drop rows by clicking and holding the <i class="fas fa-grip-vertical"></i> icon to reorder products
            </div>
            
            <?php if(isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="products-table">
                <table id="products-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th style="width: 60px;">Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        <?php 
                        $order_counter = 1;
                        while($p = $products->fetch_assoc()): 
                        ?>
                        <tr data-id="<?php echo $p['id']; ?>">
                            <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                            <td><?php echo $p['id']; ?></td>
                            <td><img src="<?php echo $p['image_url']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/60'"></td>
                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                            <td><?php echo $p['price']; ?></td>
                            <td><?php echo ucfirst($p['category']); ?></td>
                            <td><span class="status-badge <?php echo $p['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['status']; ?></span></td>
                            <td class="order-display"><?php echo $order_counter++; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditModal(<?php echo $p['id']; ?>)">Edit</a>
                                <a href="?toggle=<?php echo $p['id']; ?>" class="btn-toggle">Toggle</a>
                                <a href="?delete=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 id="modalTitle">Add Product</h3>
                <span onclick="closeModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
            </div>
            <form method="POST" id="productForm">
                <input type="hidden" name="id" id="product_id">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                <div class="form-group">
                    <label>Price *</label>
                    <input type="text" name="price" id="price" placeholder="e.g., From 450 Birr" required>
                </div>
                <div class="form-group">
                    <label>Image URL *</label>
                    <input type="text" name="image_url" id="image_url" placeholder="https://..." required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="category">
                        <option value="apparel">Apparel</option>
                        <option value="accessories">Accessories</option>
                        <option value="gifts">Gifts</option>
                        <option value="decor">Decor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Save Product</button>
            </form>
        </div>
    </div>
    
    <script>
        // Store products data
        let productsData = {};
        <?php
        $all_products = $conn->query("SELECT * FROM products");
        while($row = $all_products->fetch_assoc()) {
            echo "productsData[{$row['id']}] = " . json_encode($row) . ";\n";
        }
        ?>
        
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
            document.getElementById('productModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            let p = productsData[id];
            if(p) {
                document.getElementById('modalTitle').innerText = 'Edit Product';
                document.getElementById('product_id').value = p.id;
                document.getElementById('title').value = p.title;
                document.getElementById('description').value = p.description;
                document.getElementById('price').value = p.price;
                document.getElementById('image_url').value = p.image_url;
                document.getElementById('category').value = p.category;
                document.getElementById('status').value = p.status;
                document.getElementById('productModal').style.display = 'block';
            }
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }
        
        // Drag and Drop Reordering
        let draggedRow = null;
        
        function handleDragStart(e) {
            draggedRow = this;
            e.dataTransfer.effectAllowed = 'move';
            this.style.opacity = '0.5';
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
            
            if (draggedRow !== this) {
                // Get the tbody
                const tbody = document.getElementById('products-tbody');
                const rows = Array.from(tbody.children);
                
                const draggedIndex = rows.indexOf(draggedRow);
                const targetIndex = rows.indexOf(this);
                
                // Move the row
                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedRow, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedRow, this);
                }
                
                // Update order numbers
                updateOrderNumbers();
                
                // Save to database
                saveOrder();
            }
            
            this.classList.remove('drag-over');
            return false;
        }
        
        function handleDragEnd(e) {
            this.style.opacity = '';
            const rows = document.querySelectorAll('#products-tbody tr');
            rows.forEach(row => {
                row.classList.remove('drag-over');
            });
        }
        
        function updateOrderNumbers() {
            const rows = document.querySelectorAll('#products-tbody tr');
            rows.forEach((row, index) => {
                const orderCell = row.querySelector('.order-display');
                if (orderCell) {
                    orderCell.textContent = index + 1;
                }
            });
        }
        
        function saveOrder() {
            const rows = document.querySelectorAll('#products-tbody tr');
            const orderIds = [];
            
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                orderIds.push(id);
            });
            
            // Send AJAX request
            const formData = new FormData();
            formData.append('reorder', JSON.stringify(orderIds));
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
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
                console.error('Error:', error);
                showToast('✗ Error saving order', 'error');
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
        
        // Initialize drag and drop after page loads
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#products-tbody tr');
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