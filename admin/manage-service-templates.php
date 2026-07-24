<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_templates WHERE id = $id");
    redirect('manage-service-templates.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE service_templates SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('manage-service-templates.php');
}

// Handle reorder (AJAX)
if (isset($_POST['action']) && $_POST['action'] == 'reorder') {
    header('Content-Type: application/json');
    $orders = json_decode($_POST['orders'], true);
    if (is_array($orders)) {
        foreach ($orders as $order) {
            $id = (int)$order['id'];
            $display_order = (int)$order['order'];
            $conn->query("UPDATE service_templates SET display_order = $display_order WHERE id = $id");
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
    $service_id = (int)$_POST['service_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $status = $conn->real_escape_string($_POST['status']);
    
    if ($id > 0) {
        $sql = "UPDATE service_templates SET service_id=$service_id, title='$title', description='$description', image_url='$image_url', status='$status' WHERE id=$id";
    } else {
        $result = $conn->query("SELECT MAX(display_order) as max FROM service_templates WHERE service_id=$service_id");
        $max_order = $result->fetch_assoc()['max'];
        $display_order = ($max_order !== null) ? $max_order + 1 : 1;
        $sql = "INSERT INTO service_templates (service_id, title, description, image_url, status, display_order) VALUES ($service_id, '$title', '$description', '$image_url', '$status', $display_order)";
    }
    
    if ($conn->query($sql)) {
        $success = $id > 0 ? "Template updated!" : "Template added!";
    } else {
        $error = "Failed to save template: " . $conn->error;
    }
}

// Get all services for dropdown
$services = $conn->query("SELECT id, title FROM services WHERE status='active' ORDER BY title");

// Get all templates with service names
$templates = $conn->query("SELECT t.*, s.title as service_title FROM service_templates t LEFT JOIN services s ON t.service_id = s.id ORDER BY t.service_id, t.display_order ASC, t.id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Service Templates | Admin</title>
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
        .top-bar { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .btn-add { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .templates-table { background: white; border-radius: 12px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eef2f6; }
        th { background: #f8f9fa; font-weight: 600; }
        .template-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .action-buttons a { margin-right: 10px; text-decoration: none; padding: 5px 12px; border-radius: 5px; font-size: 12px; }
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
        .btn-submit { background: #ff6b6b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .error-message { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
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
                <a href="manage-service-templates.php" class="active"><i class="fas fa-images"></i> Templates</a>
                <a href="view-template-orders.php"><i class="fas fa-shopping-cart"></i> Template Orders</a>
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
                <h2>Manage Service Templates</h2>
                <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Template</button>
            </div>
            
            <div class="reorder-info">
                <i class="fas fa-arrows-alt"></i> Drag and drop rows to reorder templates
            </div>
            
            <?php if(isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="templates-table">
                <table id="templates-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th>Service</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th style="width: 60px;">Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="templates-tbody">
                        <?php 
                        $current_service = '';
                        $order_counter = 1;
                        while($t = $templates->fetch_assoc()): 
                            if($current_service != $t['service_id']) {
                                $order_counter = 1;
                                $current_service = $t['service_id'];
                            }
                        ?>
                        <tr data-id="<?php echo $t['id']; ?>" data-service-id="<?php echo $t['service_id']; ?>">
                            <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                            <td><?php echo $t['id']; ?></td>
                            <td><img src="<?php echo $t['image_url']; ?>" class="template-img" onerror="this.src='https://via.placeholder.com/60'"></td>
                            <td><strong><?php echo htmlspecialchars($t['service_title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($t['title']); ?></td>
                            <td><span class="status-badge <?php echo $t['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $t['status']; ?></span></td>
                            <td class="order-display"><?php echo $order_counter++; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditModal(<?php echo $t['id']; ?>)">Edit</a>
                                <a href="?toggle=<?php echo $t['id']; ?>" class="btn-toggle">Toggle</a>
                                <a href="?delete=<?php echo $t['id']; ?>" class="btn-delete" onclick="return confirm('Delete this template?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="templateModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 id="modalTitle">Add Template</h3>
                <span onclick="closeModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
            </div>
            <form method="POST" id="templateForm">
                <input type="hidden" name="id" id="template_id">
                <div class="form-group">
                    <label>Service *</label>
                    <select name="service_id" id="service_id" required>
                        <option value="">Select Service</option>
                        <?php 
                        $services->data_seek(0);
                        while($s = $services->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Template Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Image *</label>
                    <div class="img-field-wrap">
                        <input type="text" name="image_url" id="image_url" placeholder="Upload below, or paste an image URL" required oninput="syncImgPreview('image_url')">
                        <div class="img-upload-row">
                            <button type="button" class="img-upload-btn" onclick="document.getElementById('image_url_file').click()"><i class="fas fa-upload"></i> Upload image</button>
                            <input type="file" id="image_url_file" accept="image/*" style="display:none" onchange="uploadImageFor('image_url', this)">
                            <span class="img-upload-status" id="image_url_status"></span>
                        </div>
                        <img class="img-field-preview" id="image_url_preview" src="" style="display:none">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Save Template</button>
            </form>
        </div>
    </div>
    
    <script>
        let templatesData = {};
        <?php
        $all_templates = $conn->query("SELECT * FROM service_templates");
        while($row = $all_templates->fetch_assoc()) {
            echo "templatesData[{$row['id']}] = " . json_encode($row) . ";\n";
        }
        ?>
        
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Template';
            document.getElementById('templateForm').reset();
            syncImgPreview('image_url');
            document.getElementById('template_id').value = '';
            document.getElementById('templateModal').style.display = 'block';
        }
        
        function openEditModal(id) {
            let t = templatesData[id];
            if(t) {
                document.getElementById('modalTitle').innerText = 'Edit Template';
                document.getElementById('template_id').value = t.id;
                document.getElementById('service_id').value = t.service_id;
                document.getElementById('title').value = t.title;
                document.getElementById('description').value = t.description;
                document.getElementById('image_url').value = t.image_url;
                syncImgPreview('image_url');
                document.getElementById('status').value = t.status;
                document.getElementById('templateModal').style.display = 'block';
            }
        }
        
        function closeModal() {
            document.getElementById('templateModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('templateModal')) {
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
            
            if (dragSrcElement !== this) {
                const tbody = document.getElementById('templates-tbody');
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
            const rows = document.querySelectorAll('#templates-tbody tr');
            rows.forEach(row => row.classList.remove('drag-over'));
        }
        
        function updateOrderNumbers() {
            const rows = document.querySelectorAll('#templates-tbody tr');
            rows.forEach((row, index) => {
                const orderCell = row.querySelector('.order-display');
                if (orderCell) orderCell.textContent = index + 1;
            });
        }
        
        function saveOrder() {
            const rows = document.querySelectorAll('#templates-tbody tr');
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
            .catch(error => showToast('✗ Network error', 'error'));
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
            const rows = document.querySelectorAll('#templates-tbody tr');
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

<style>
.img-field-wrap { border: 1.5px dashed #e2e8f0; border-radius: 10px; padding: 10px; }
.img-field-wrap input[type=text] { margin-bottom: 8px; }
.img-upload-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.img-upload-btn { background: #f1f3f7; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #444; }
.img-upload-btn:hover { background: #e6e9ef; }
.img-upload-status { font-size: 12px; color: #718096; }
.img-field-preview { display: block; margin-top: 10px; max-height: 120px; border-radius: 8px; }
</style>
<script>
function uploadImageFor(fieldId, fileInputEl) {
    const file = fileInputEl.files[0];
    if (!file) return;
    const statusEl = document.getElementById(fieldId + '_status');
    const isVideo = file.type.startsWith('video/');
    const maxSize = isVideo ? 20 * 1024 * 1024 : 5 * 1024 * 1024;
    if (file.size > maxSize) {
        statusEl.textContent = '✗ Too large (max ' + (isVideo ? '20MB' : '5MB') + ')';
        statusEl.style.color = '#c0392b';
        return;
    }
    statusEl.textContent = 'Uploading...';
    statusEl.style.color = '#718096';
    const fd = new FormData();
    fd.append('file', file);
    fetch('upload_image.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                document.getElementById(fieldId).value = data.path;
                statusEl.textContent = '✓ Uploaded';
                statusEl.style.color = '#28a745';
                syncImgPreview(fieldId);
            } else {
                statusEl.textContent = '✗ ' + data.message;
                statusEl.style.color = '#c0392b';
            }
        })
        .catch(() => { statusEl.textContent = '✗ Upload failed, try again'; statusEl.style.color = '#c0392b'; });
}
function syncImgPreview(fieldId) {
    const val = document.getElementById(fieldId).value;
    const img = document.getElementById(fieldId + '_preview');
    if (!img) return;
    if (val && !/\.(mp4|webm)(\?|$)/i.test(val)) {
        img.src = val.startsWith('uploads/') ? '../' + val : val;
        img.style.display = 'block';
        img.onerror = function(){ img.style.display = 'none'; };
    } else {
        img.style.display = 'none';
    }
}
</script>
</body>
</html>