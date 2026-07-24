<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

// Renders a text input + direct-upload button + live preview for an image/video field.
// Keeps the same input name/id so all existing form-submit and edit-populate JS keeps working untouched.
function img_upload_field($name, $value = '', $label = '', $required = false) {
    $id = $name;
    $safeVal = htmlspecialchars($value);
    $req = $required ? 'required' : '';
    $showPreview = !empty($value) ? 'display:block;' : 'display:none;';
    echo "<div class=\"form-group\"><label>{$label}</label><div class=\"img-field-wrap\">
        <input type=\"text\" name=\"{$name}\" id=\"{$id}\" value=\"{$safeVal}\" placeholder=\"Upload below, or paste an image URL\" {$req} oninput=\"syncImgPreview('{$id}')\">
        <div class=\"img-upload-row\">
            <button type=\"button\" class=\"img-upload-btn\" onclick=\"document.getElementById('{$id}_file').click()\"><i class=\"fas fa-upload\"></i> Upload image</button>
            <input type=\"file\" id=\"{$id}_file\" accept=\"image/*\" style=\"display:none\" onchange=\"uploadImageFor('{$id}', this)\">
            <span class=\"img-upload-status\" id=\"{$id}_status\"></span>
        </div>
        <img class=\"img-field-preview\" id=\"{$id}_preview\" src=\"{$safeVal}\" style=\"{$showPreview}\" onerror=\"this.style.display='none'\">
    </div></div>";
}

// =============================================
// HANDLE ALL POST REQUESTS (ADD/EDIT)
// =============================================

// Handle add course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $status = $conn->real_escape_string($_POST['status']);
    $badge_text = $conn->real_escape_string($_POST['badge_text']);
    
    $sql = "INSERT INTO courses (title, description, price, duration, start_date, icon_class, status, badge_text) 
            VALUES ('$title', '$description', '$price', '$duration', '$start_date', '$icon_class', '$status', '$badge_text')";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle edit course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_course'])) {
    $id = (int)$_POST['course_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $status = $conn->real_escape_string($_POST['status']);
    $badge_text = $conn->real_escape_string($_POST['badge_text']);
    
    $sql = "UPDATE courses SET title='$title', description='$description', price='$price', duration='$duration', start_date='$start_date', icon_class='$icon_class', status='$status', badge_text='$badge_text' WHERE id=$id";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle add service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $features = $conn->real_escape_string($_POST['features']);
    $price = $conn->real_escape_string($_POST['price']);
    $status = $conn->real_escape_string($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    $sql = "INSERT INTO services (title, description, icon_class, features, price, status, display_order) 
            VALUES ('$title', '$description', '$icon_class', '$features', '$price', '$status', $display_order)";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle edit service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_service'])) {
    $id = (int)$_POST['service_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $features = $conn->real_escape_string($_POST['features']);
    $price = $conn->real_escape_string($_POST['price']);
    $status = $conn->real_escape_string($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    $sql = "UPDATE services SET title='$title', description='$description', icon_class='$icon_class', features='$features', price='$price', status='$status', display_order=$display_order WHERE id=$id";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle add product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    $sql = "INSERT INTO products (title, description, price, image_url, category, status, display_order) 
            VALUES ('$title', '$description', '$price', '$image_url', '$category', '$status', $display_order)";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle edit product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['product_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    $sql = "UPDATE products SET title='$title', description='$description', price='$price', image_url='$image_url', category='$category', status='$status', display_order=$display_order WHERE id=$id";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle add portfolio item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_portfolio'])) {
    $service_id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $media_url = $conn->real_escape_string($_POST['media_url']);
    $media_type = $conn->real_escape_string($_POST['media_type']);
    
    $result = $conn->query("SELECT MAX(display_order) as max FROM portfolio_items WHERE service_id=$service_id");
    $max_order = $result->fetch_assoc()['max'];
    $display_order = ($max_order !== null) ? $max_order + 1 : 1;
    
    $sql = "INSERT INTO portfolio_items (service_id, service_name, title, description, media_url, media_type, display_order) 
            VALUES ($service_id, '$service_name', '$title', '$description', '$media_url', '$media_type', $display_order)";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle edit portfolio item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_portfolio'])) {
    $id = (int)$_POST['portfolio_id'];
    $service_id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $media_url = $conn->real_escape_string($_POST['media_url']);
    $media_type = $conn->real_escape_string($_POST['media_type']);
    
    $sql = "UPDATE portfolio_items SET service_id=$service_id, service_name='$service_name', title='$title', description='$description', media_url='$media_url', media_type='$media_type' WHERE id=$id";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle add package
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_package'])) {
    $service_id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $package_name = $conn->real_escape_string($_POST['package_name']);
    $package_price = $conn->real_escape_string($_POST['package_price']);
    $features = $conn->real_escape_string($_POST['features']);
    
    $result = $conn->query("SELECT MAX(display_order) as max FROM service_packages WHERE service_id=$service_id");
    $max_order = $result->fetch_assoc()['max'];
    $display_order = ($max_order !== null) ? $max_order + 1 : 1;
    
    $sql = "INSERT INTO service_packages (service_id, service_name, package_name, package_price, features, display_order) 
            VALUES ($service_id, '$service_name', '$package_name', '$package_price', '$features', $display_order)";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle edit package
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_package'])) {
    $id = (int)$_POST['package_id'];
    $service_id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $package_name = $conn->real_escape_string($_POST['package_name']);
    $package_price = $conn->real_escape_string($_POST['package_price']);
    $features = $conn->real_escape_string($_POST['features']);
    
    $sql = "UPDATE service_packages SET service_id=$service_id, service_name='$service_name', package_name='$package_name', package_price='$package_price', features='$features' WHERE id=$id";
    $conn->query($sql);
    redirect('dashboard.php');
}

// Handle about page update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_about'])) {
    $youtube_channel_url = $conn->real_escape_string($_POST['youtube_channel_url']);
    $youtube_thumbnail = $conn->real_escape_string($_POST['youtube_thumbnail']);
    $youtube_badge_text = $conn->real_escape_string($_POST['youtube_badge_text']);
    $channel_description = $conn->real_escape_string($_POST['channel_description']);
    $instructor_name = $conn->real_escape_string($_POST['instructor_name']);
    $instructor_title = $conn->real_escape_string($_POST['instructor_title']);
    $instructor_bio = $conn->real_escape_string($_POST['instructor_bio']);
    $instructor_image = $conn->real_escape_string($_POST['instructor_image']);
    
    $video1_url = $conn->real_escape_string($_POST['video1_url']);
    $video1_thumbnail = $conn->real_escape_string($_POST['video1_thumbnail']);
    $video1_title = $conn->real_escape_string($_POST['video1_title']);
    $video1_views = $conn->real_escape_string($_POST['video1_views']);
    
    $video2_url = $conn->real_escape_string($_POST['video2_url']);
    $video2_thumbnail = $conn->real_escape_string($_POST['video2_thumbnail']);
    $video2_title = $conn->real_escape_string($_POST['video2_title']);
    $video2_views = $conn->real_escape_string($_POST['video2_views']);
    
    $video3_url = $conn->real_escape_string($_POST['video3_url']);
    $video3_thumbnail = $conn->real_escape_string($_POST['video3_thumbnail']);
    $video3_title = $conn->real_escape_string($_POST['video3_title']);
    $video3_views = $conn->real_escape_string($_POST['video3_views']);
    
    $sql = "UPDATE about_content SET 
            youtube_channel_url='$youtube_channel_url', youtube_thumbnail='$youtube_thumbnail', youtube_badge_text='$youtube_badge_text',
            channel_description='$channel_description', instructor_name='$instructor_name', instructor_title='$instructor_title',
            instructor_bio='$instructor_bio', instructor_image='$instructor_image',
            youtube_video_1_url='$video1_url', youtube_video_1_thumbnail='$video1_thumbnail', youtube_video_1_title='$video1_title', youtube_video_1_views='$video1_views',
            youtube_video_2_url='$video2_url', youtube_video_2_thumbnail='$video2_thumbnail', youtube_video_2_title='$video2_title', youtube_video_2_views='$video2_views',
            youtube_video_3_url='$video3_url', youtube_video_3_thumbnail='$video3_thumbnail', youtube_video_3_title='$video3_title', youtube_video_3_views='$video3_views'
            WHERE id=1";
    $conn->query($sql);
    redirect('dashboard.php');
}

// =============================================
// HANDLE ALL GET REQUESTS (DELETE/TOGGLE/STATUS)
// =============================================

// Course actions
if (isset($_GET['delete_course'])) { $conn->query("DELETE FROM courses WHERE id=".(int)$_GET['delete_course']); redirect('dashboard.php'); }
if (isset($_GET['toggle_course'])) { $conn->query("UPDATE courses SET status = IF(status='active', 'coming_soon', 'active') WHERE id=".(int)$_GET['toggle_course']); redirect('dashboard.php'); }

// Service actions
if (isset($_GET['delete_service'])) { $conn->query("DELETE FROM services WHERE id=".(int)$_GET['delete_service']); redirect('dashboard.php'); }
if (isset($_GET['toggle_service'])) { $conn->query("UPDATE services SET status = IF(status='active', 'inactive', 'active') WHERE id=".(int)$_GET['toggle_service']); redirect('dashboard.php'); }

// Product actions
if (isset($_GET['delete_product'])) { $conn->query("DELETE FROM products WHERE id=".(int)$_GET['delete_product']); redirect('dashboard.php'); }
if (isset($_GET['toggle_product'])) { $conn->query("UPDATE products SET status = IF(status='active', 'inactive', 'active') WHERE id=".(int)$_GET['toggle_product']); redirect('dashboard.php'); }

// Portfolio actions
if (isset($_GET['delete_portfolio'])) { $conn->query("DELETE FROM portfolio_items WHERE id=".(int)$_GET['delete_portfolio']); redirect('dashboard.php'); }
if (isset($_GET['toggle_portfolio'])) { $conn->query("UPDATE portfolio_items SET status = IF(status='active', 'inactive', 'active') WHERE id=".(int)$_GET['toggle_portfolio']); redirect('dashboard.php'); }

// Package actions
if (isset($_GET['delete_package'])) { $conn->query("DELETE FROM service_packages WHERE id=".(int)$_GET['delete_package']); redirect('dashboard.php'); }
if (isset($_GET['toggle_package'])) { $conn->query("UPDATE service_packages SET status = IF(status='active', 'inactive', 'active') WHERE id=".(int)$_GET['toggle_package']); redirect('dashboard.php'); }

// Order actions
if (isset($_GET['order_status']) && isset($_GET['order_id'])) {
    $conn->query("UPDATE product_orders SET status='".$conn->real_escape_string($_GET['order_status'])."' WHERE id=".(int)$_GET['order_id']);
    redirect('dashboard.php');
}
if (isset($_GET['delete_order'])) { $conn->query("DELETE FROM product_orders WHERE id=".(int)$_GET['delete_order']); redirect('dashboard.php'); }

// Service order actions
if (isset($_GET['service_order_status']) && isset($_GET['service_order_id'])) {
    $conn->query("UPDATE service_registrations SET status='".$conn->real_escape_string($_GET['service_order_status'])."' WHERE id=".(int)$_GET['service_order_id']);
    redirect('dashboard.php');
}
if (isset($_GET['delete_service_order'])) { $conn->query("DELETE FROM service_registrations WHERE id=".(int)$_GET['delete_service_order']); redirect('dashboard.php'); }

// Request actions
if (isset($_GET['request_status']) && isset($_GET['request_id'])) {
    $conn->query("UPDATE service_requests SET status='".$conn->real_escape_string($_GET['request_status'])."' WHERE id=".(int)$_GET['request_id']);
    redirect('dashboard.php');
}
if (isset($_GET['delete_request'])) { $conn->query("DELETE FROM service_requests WHERE id=".(int)$_GET['delete_request']); redirect('dashboard.php'); }

// Question actions
if (isset($_GET['delete_question'])) { $conn->query("DELETE FROM questions WHERE id=".(int)$_GET['delete_question']); redirect('dashboard.php'); }
if (isset($_GET['answered_question'])) { $conn->query("UPDATE questions SET status='answered', answered_at=NOW() WHERE id=".(int)$_GET['answered_question']); redirect('dashboard.php'); }

// Registration actions
if (isset($_GET['delete_registration'])) {
    $id = (int)$_GET['delete_registration'];
    $result = $conn->query("SELECT payment_receipt FROM registrations WHERE id=$id");
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['payment_receipt'] && file_exists('../' . $row['payment_receipt'])) {
            unlink('../' . $row['payment_receipt']);
        }
    }
    $conn->query("DELETE FROM registrations WHERE id=$id");
    redirect('dashboard.php');
}
if (isset($_GET['approve_reg'])) { $conn->query("UPDATE registrations SET status='approved' WHERE id=".(int)$_GET['approve_reg']); redirect('dashboard.php'); }
if (isset($_GET['reject_reg'])) { $conn->query("UPDATE registrations SET status='rejected' WHERE id=".(int)$_GET['reject_reg']); redirect('dashboard.php'); }

// =============================================
// FETCH ALL DATA
// =============================================
$courses = $conn->query("SELECT * FROM courses ORDER BY id DESC");
$services = $conn->query("SELECT * FROM services ORDER BY display_order ASC, id DESC");
$products = $conn->query("SELECT * FROM products ORDER BY display_order ASC, id DESC");
$portfolio_items = $conn->query("SELECT p.*, s.title as service_title FROM portfolio_items p LEFT JOIN services s ON p.service_id = s.id ORDER BY p.service_id, p.display_order ASC");
$packages = $conn->query("SELECT p.*, s.title as service_title FROM service_packages p LEFT JOIN services s ON p.service_id = s.id ORDER BY p.service_id, p.display_order ASC");
$product_orders = $conn->query("SELECT * FROM product_orders ORDER BY id DESC LIMIT 20");
$service_orders = $conn->query("SELECT * FROM service_registrations ORDER BY id DESC LIMIT 20");
$service_requests = $conn->query("SELECT * FROM service_requests ORDER BY id DESC LIMIT 20");
$questions = $conn->query("SELECT * FROM questions ORDER BY id DESC LIMIT 20");
$registrations = $conn->query("SELECT * FROM registrations ORDER BY id DESC LIMIT 20");
$about = $conn->query("SELECT * FROM about_content WHERE id=1")->fetch_assoc();

// Get all products for product gallery grouping
$all_products_list = $conn->query("SELECT id, title FROM products ORDER BY title");
$product_templates = $conn->query("SELECT t.*, p.title as product_title FROM product_templates t LEFT JOIN products p ON t.product_id = p.id ORDER BY t.product_id, t.display_order ASC");

// Stats
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$total_services = $conn->query("SELECT COUNT(*) as count FROM services WHERE status='active'")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status='active'")->fetch_assoc()['count'];
$total_portfolio = $conn->query("SELECT COUNT(*) as count FROM portfolio_items WHERE status='active'")->fetch_assoc()['count'];
$total_packages = $conn->query("SELECT COUNT(*) as count FROM service_packages WHERE status='active'")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM product_orders WHERE status='pending'")->fetch_assoc()['count'];
$pending_service_orders = $conn->query("SELECT COUNT(*) as count FROM service_registrations WHERE status='pending'")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='pending'")->fetch_assoc()['count'];
$pending_messages = $conn->query("SELECT COUNT(*) as count FROM questions WHERE status='pending'")->fetch_assoc()['count'];
$pending_registrations = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status='pending'")->fetch_assoc()['count'];
$total_students = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status='approved'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Dan Creatives</title>
    <link rel="icon" href="../images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        
        /* Sidebar */
        .sidebar { width: 300px; background: #1a1a2e; color: white; position: fixed; height: 100vh; overflow-y: auto; transition: 0.3s; z-index: 100; }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { font-size: 24px; }
        .sidebar-header span { color: #ff6b6b; }
        .sidebar-header p { font-size: 12px; opacity: 0.7; margin-top: 8px; }
        
        /* Dropdown Menu */
        .sidebar-menu { padding: 15px 0; }
        .menu-item { margin-bottom: 5px; }
        .menu-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 25px; cursor: pointer; transition: 0.3s; color: #cbd5e0; }
        .menu-header:hover { background: rgba(255,107,107,0.1); color: #ff6b6b; }
        .menu-header.active { background: rgba(255,107,107,0.15); color: #ff6b6b; }
        .menu-title { display: flex; align-items: center; gap: 12px; }
        .menu-title i { width: 24px; }
        .menu-arrow { transition: 0.3s; }
        .menu-arrow.rotated { transform: rotate(90deg); }
        .menu-submenu { display: none; background: rgba(0,0,0,0.2); padding: 5px 0; }
        .menu-submenu.show { display: block; }
        .menu-submenu a { display: flex; align-items: center; gap: 12px; padding: 10px 25px 10px 55px; color: #a0aec0; text-decoration: none; transition: 0.3s; font-size: 0.9rem; }
        .menu-submenu a:hover { background: rgba(255,107,107,0.1); color: #ff6b6b; }
        .menu-submenu a i { width: 20px; }
        .menu-link { display: flex; align-items: center; gap: 12px; padding: 12px 25px; color: #cbd5e0; text-decoration: none; transition: 0.3s; }
        .menu-link:hover { background: rgba(255,107,107,0.1); color: #ff6b6b; }
        
        /* Main Content */
        .main-content { margin-left: 300px; padding: 20px; transition: 0.3s; }
        .top-bar { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logout-btn { background: #ff6b6b; color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; }
        .logout-btn:hover { background: #ff5252; }
        
        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .stat-info h3 { font-size: 13px; color: #718096; margin-bottom: 8px; }
        .stat-info .number { font-size: 28px; font-weight: 800; color: #1a2a3a; }
        .stat-icon { font-size: 40px; color: #ff6b6b; opacity: 0.7; }
        
        /* Sections */
        .section { background: white; border-radius: 12px; margin-bottom: 30px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .section-header { padding: 20px 25px; background: #f8f9fa; border-bottom: 1px solid #eef2f6; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .section-header h3 { color: #1a2a3a; font-size: 18px; }
        .btn-add { background: #ff6b6b; color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-add:hover { background: #ff5252; transform: translateY(-2px); }
        .btn-view { background: #17a2b8; color: white; padding: 5px 12px; border-radius: 5px; font-size: 11px; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: #138496; }
        
        /* Tables */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eef2f6; }
        th { background: #fafbfe; font-weight: 600; color: #1a2a3a; }
        tr:hover { background: #fafbfe; }
        
        /* Badges */
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .status-active, .status-approved, .status-completed { background: #d4edda; color: #155724; }
        .status-coming, .status-pending { background: #fff3cd; color: #856404; }
        .status-inactive, .status-rejected { background: #f8d7da; color: #721c24; }
        .status-processing { background: #cce5ff; color: #004085; }
        
        /* Action Buttons */
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-edit, .btn-toggle, .btn-delete, .btn-processing, .btn-completed, .btn-answered, .btn-approve, .btn-reject {
            padding: 5px 12px; border-radius: 5px; font-size: 11px; text-decoration: none; display: inline-block; transition: 0.3s;
        }
        .btn-edit { background: #007bff; color: white; }
        .btn-toggle { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-processing { background: #17a2b8; color: white; }
        .btn-completed { background: #28a745; color: white; }
        .btn-answered { background: #28a745; color: white; }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-edit:hover, .btn-toggle:hover, .btn-delete:hover,
        .btn-processing:hover, .btn-completed:hover, .btn-answered:hover,
        .btn-approve:hover, .btn-reject:hover { opacity: 0.8; transform: translateY(-1px); }
        
        /* Images */
        .img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        
        /* Grouped Gallery */
        .gallery-group { margin-bottom: 30px; border: 1px solid #eef2f6; border-radius: 12px; overflow: hidden; }
        .gallery-group-header { background: #f8f9fa; padding: 15px 20px; font-weight: 700; color: #1a2a3a; border-bottom: 1px solid #eef2f6; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .gallery-group-header i { transition: 0.3s; }
        .gallery-group-header .rotated { transform: rotate(90deg); }
        .gallery-group-body { display: none; }
        .gallery-group-body.show { display: block; }
        
        /* Modals */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto; }
        .modal-content { background: white; width: 90%; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 12px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eef2f6; }
        .close-modal { cursor: pointer; font-size: 24px; color: #718096; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #1a2a3a; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .btn-submit { background: #ff6b6b; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: #ff5252; transform: translateY(-2px); }
        
        .menu-toggle { display: none; position: fixed; top: 20px; left: 20px; z-index: 101; background: #ff6b6b; color: white; padding: 10px; border-radius: 8px; cursor: pointer; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } .menu-toggle { display: block; } }
    </style>
</head>
<body>

<div class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Dan<span>Creatives</span></h3>
        <p>Admin Panel</p>
    </div>
    
    <div class="sidebar-menu">
        <a href="#" class="menu-link" onclick="showSection('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

        <!-- SERVICES Dropdown -->
        <div class="menu-item">
            <div class="menu-header" onclick="toggleMenu(this)">
                <div class="menu-title"><i class="fas fa-paint-brush"></i><span>Services</span></div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
            <div class="menu-submenu">
                <a href="#" onclick="showSection('services')"><i class="fas fa-list"></i> Manage Services</a>
                <a href="#" onclick="showSection('service_gallery')"><i class="fas fa-images"></i> Service Gallery</a>
                <a href="#" onclick="showSection('service_packages')"><i class="fas fa-tags"></i> Service Packages</a>
            </div>
        </div>

        <!-- PRODUCTS Dropdown -->
        <div class="menu-item">
            <div class="menu-header" onclick="toggleMenu(this)">
                <div class="menu-title"><i class="fas fa-box"></i><span>Products</span></div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
            <div class="menu-submenu">
                <a href="#" onclick="showSection('products')"><i class="fas fa-list"></i> Manage Products</a>
                <a href="#" onclick="showSection('product_gallery')"><i class="fas fa-images"></i> Product Gallery</a>
            </div>
        </div>

        <!-- COURSES Dropdown -->
        <div class="menu-item">
            <div class="menu-header" onclick="toggleMenu(this)">
                <div class="menu-title"><i class="fas fa-book"></i><span>Courses</span></div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
            <div class="menu-submenu">
                <a href="#" onclick="showSection('courses')"><i class="fas fa-list"></i> Manage Courses</a>
            </div>
        </div>

        <!-- ORDERS Dropdown -->
        <div class="menu-item">
            <div class="menu-header" onclick="toggleMenu(this)">
                <div class="menu-title"><i class="fas fa-shopping-cart"></i><span>Orders</span></div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
            <div class="menu-submenu">
                <a href="#" onclick="showSection('product_orders')"><i class="fas fa-shopping-cart"></i> Product Orders</a>
                <a href="#" onclick="showSection('service_orders')"><i class="fas fa-paint-brush"></i> Service Orders</a>
            </div>
        </div>

        <!-- USERS Dropdown -->
        <div class="menu-item">
            <div class="menu-header" onclick="toggleMenu(this)">
                <div class="menu-title"><i class="fas fa-users"></i><span>Users</span></div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
            <div class="menu-submenu">
                <a href="#" onclick="showSection('registrations')"><i class="fas fa-user-plus"></i> Registrations</a>
                <a href="#" onclick="showSection('questions')"><i class="fas fa-question-circle"></i> Questions</a>
            </div>
        </div>

        <!-- SETTINGS -->
        <a href="#" class="menu-link" onclick="showSection('about')"><i class="fas fa-cog"></i> Settings</a>
        
        <!-- LOGOUT -->
        <a href="ai-settings.php" class="menu-link"><i class="fas fa-robot"></i> AI Assistant</a>
        <a href="change-password.php" class="menu-link"><i class="fas fa-key"></i> Change Password</a>
        <a href="logout.php" class="menu-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <div class="top-bar">
        <h2>Welcome, Admin!</h2>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- ==================== DASHBOARD SECTION ==================== -->
    <div id="dashboardSection">
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-info"><h3>Total Courses</h3><div class="number"><?php echo $total_courses; ?></div></div><div class="stat-icon"><i class="fas fa-book"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Active Services</h3><div class="number"><?php echo $total_services; ?></div></div><div class="stat-icon"><i class="fas fa-paint-brush"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Active Products</h3><div class="number"><?php echo $total_products; ?></div></div><div class="stat-icon"><i class="fas fa-box"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Portfolio Items</h3><div class="number"><?php echo $total_portfolio; ?></div></div><div class="stat-icon"><i class="fas fa-images"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Packages</h3><div class="number"><?php echo $total_packages; ?></div></div><div class="stat-icon"><i class="fas fa-tags"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Approved Students</h3><div class="number"><?php echo $total_students; ?></div></div><div class="stat-icon"><i class="fas fa-users"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Pending Orders</h3><div class="number"><?php echo $pending_orders; ?></div></div><div class="stat-icon"><i class="fas fa-shopping-cart"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Pending Registrations</h3><div class="number"><?php echo $pending_registrations; ?></div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div>
            <div class="stat-card"><div class="stat-info"><h3>Pending Messages</h3><div class="number"><?php echo $pending_messages; ?></div></div><div class="stat-icon"><i class="fas fa-envelope"></i></div></div>
        </div>
    </div>

    <!-- ==================== SERVICES SECTION ==================== -->
    <div id="servicesSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-paint-brush"></i> Manage Services</h3><button class="btn-add" onclick="openAddServiceModal()"><i class="fas fa-plus"></i> Add Service</button></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Icon</th><th>Title</th><th>Price</th><th>Status</th><th>Actions</th><th>View</th></tr></thead>
                    <tbody>
                        <?php $services->data_seek(0); while($s = $services->fetch_assoc()): ?>
                        <tr data-id="<?php echo $s['id']; ?>">
                            <td><?php echo $s['id']; ?></td>
                            <td><i class="<?php echo $s['icon_class']; ?>" style="font-size:24px;color:#ff6b6b;"></i></td>
                            <td><?php echo htmlspecialchars($s['title']); ?></td>
                            <td><?php echo $s['price']; ?></td>
                            <td><span class="status-badge <?php echo $s['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $s['status']; ?></span></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditServiceModal(<?php echo $s['id']; ?>)">Edit</a>
                                <a href="?toggle_service=<?php echo $s['id']; ?>" class="btn-toggle" onclick="return confirm('Toggle status?')">Toggle</a>
                                <a href="?delete_service=<?php echo $s['id']; ?>" class="btn-delete" onclick="return confirm('Delete this service?')">Delete</a>
                            </td>
                            <td><a href="../services.php" target="_blank" class="btn-view"><i class="fas fa-eye"></i> View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== SERVICE GALLERY SECTION (GROUPED BY SERVICE) ==================== -->
    <div id="service_gallerySection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-images"></i> Service Gallery</h3><button class="btn-add" onclick="openAddPortfolioModal()"><i class="fas fa-plus"></i> Add Gallery Item</button></div>
            <div class="table-container">
                <?php
                $services_list = $conn->query("SELECT id, title FROM services ORDER BY title");
                while($service_item = $services_list->fetch_assoc()):
                    $service_portfolio = $conn->query("SELECT * FROM portfolio_items WHERE service_id = ".$service_item['id']." ORDER BY display_order ASC");
                    if($service_portfolio->num_rows > 0):
                ?>
                <div class="gallery-group">
                    <div class="gallery-group-header" onclick="toggleGroup(this)">
                        <span><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($service_item['title']); ?> (<?php echo $service_portfolio->num_rows; ?> items)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="gallery-group-body">
                        <table>
                            <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php while($item = $service_portfolio->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><img src="<?php echo $item['media_url']; ?>" class="img-preview" onerror="this.src='https://via.placeholder.com/50'"></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo ucfirst($item['media_type']); ?></td>
                                    <td><span class="status-badge <?php echo $item['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $item['status']; ?></span></td>
                                    <td class="action-buttons">
                                        <a href="#" class="btn-edit" onclick="openEditPortfolioModal(<?php echo $item['id']; ?>)">Edit</a>
                                        <a href="?toggle_portfolio=<?php echo $item['id']; ?>" class="btn-toggle" onclick="return confirm('Toggle status?')">Toggle</a>
                                        <a href="?delete_portfolio=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Delete this item?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; endwhile; ?>
                <?php if($conn->query("SELECT COUNT(*) as count FROM portfolio_items")->fetch_assoc()['count'] == 0): ?>
                <div style="text-align:center; padding:60px;"><i class="fas fa-images" style="font-size:48px; color:#ccc;"></i><h3>No Gallery Items</h3><p>Click "Add Gallery Item" to showcase your work.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ==================== SERVICE PACKAGES SECTION ==================== -->
    <div id="service_packagesSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-tags"></i> Service Packages</h3><button class="btn-add" onclick="openAddPackageModal()"><i class="fas fa-plus"></i> Add Package</button></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Service</th><th>Package Name</th><th>Price</th><th>Features</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $packages->data_seek(0); while($pkg = $packages->fetch_assoc()): 
                            $features_short = substr(str_replace('|', ', ', $pkg['features']), 0, 60);
                        ?>
                        <tr>
                            <td><?php echo $pkg['id']; ?></td>
                            <td><?php echo htmlspecialchars($pkg['service_title']); ?></td>
                            <td><?php echo htmlspecialchars($pkg['package_name']); ?></td>
                            <td><?php echo $pkg['package_price']; ?></td>
                            <td><?php echo $features_short; ?>...</td>
                            <td><span class="status-badge <?php echo $pkg['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $pkg['status']; ?></span></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditPackageModal(<?php echo $pkg['id']; ?>)">Edit</a>
                                <a href="?toggle_package=<?php echo $pkg['id']; ?>" class="btn-toggle" onclick="return confirm('Toggle status?')">Toggle</a>
                                <a href="?delete_package=<?php echo $pkg['id']; ?>" class="btn-delete" onclick="return confirm('Delete this package?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== PRODUCTS SECTION ==================== -->
    <div id="productsSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-box"></i> Manage Products</h3><button class="btn-add" onclick="openAddProductModal()"><i class="fas fa-plus"></i> Add Product</button></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Price</th><th>Category</th><th>Status</th><th>Actions</th><th>View</th></tr></thead>
                    <tbody>
                        <?php $products->data_seek(0); while($p = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><img src="<?php echo $p['image_url']; ?>" class="img-preview" onerror="this.src='https://via.placeholder.com/50'"></td>
                            <td><?php echo htmlspecialchars($p['title']); ?></td>
                            <td><?php echo $p['price']; ?></td>
                            <td><?php echo ucfirst($p['category']); ?></td>
                            <td><span class="status-badge <?php echo $p['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['status']; ?></span></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditProductModal(<?php echo $p['id']; ?>)">Edit</a>
                                <a href="?toggle_product=<?php echo $p['id']; ?>" class="btn-toggle" onclick="return confirm('Toggle status?')">Toggle</a>
                                <a href="?delete_product=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                            <td><a href="../products.php" target="_blank" class="btn-view"><i class="fas fa-eye"></i> View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== PRODUCT GALLERY SECTION (GROUPED BY PRODUCT) ==================== -->
    <div id="product_gallerySection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-images"></i> Product Gallery</h3><button class="btn-add" onclick="alert('Add product template feature - Will be added soon')"><i class="fas fa-plus"></i> Add Template</button></div>
            <div class="table-container">
                <?php
                $all_products_gallery = $conn->query("SELECT id, title FROM products ORDER BY title");
                while($product_item = $all_products_gallery->fetch_assoc()):
                    $product_templates_list = $conn->query("SELECT * FROM product_templates WHERE product_id = ".$product_item['id']." ORDER BY display_order ASC");
                    if($product_templates_list->num_rows > 0):
                ?>
                <div class="gallery-group">
                    <div class="gallery-group-header" onclick="toggleGroup(this)">
                        <span><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($product_item['title']); ?> (<?php echo $product_templates_list->num_rows; ?> items)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="gallery-group-body">
                        <table>
                            <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php while($tmpl = $product_templates_list->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $tmpl['id']; ?></td>
                                    <td><img src="<?php echo $tmpl['image_url']; ?>" class="img-preview" onerror="this.src='https://via.placeholder.com/50'"></td>
                                    <td><?php echo htmlspecialchars($tmpl['title']); ?></td>
                                    <td><span class="status-badge <?php echo $tmpl['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"><?php echo $tmpl['status']; ?></span></td>
                                    <td class="action-buttons">
                                        <a href="#" class="btn-edit" onclick="alert('Edit feature coming soon')">Edit</a>
                                        <a href="#" class="btn-toggle" onclick="alert('Toggle feature coming soon')">Toggle</a>
                                        <a href="#" class="btn-delete" onclick="alert('Delete feature coming soon')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; endwhile; ?>
                <?php if($conn->query("SELECT COUNT(*) as count FROM product_templates")->fetch_assoc()['count'] == 0): ?>
                <div style="text-align:center; padding:60px;"><i class="fas fa-images" style="font-size:48px; color:#ccc;"></i><h3>No Product Gallery Items</h3><p>Add product templates to showcase your designs.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ==================== COURSES SECTION ==================== -->
    <div id="coursesSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-book"></i> Manage Courses</h3><button class="btn-add" onclick="openAddCourseModal()"><i class="fas fa-plus"></i> Add Course</button></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Title</th><th>Price</th><th>Duration</th><th>Start Date</th><th>Status</th><th>Actions</th><th>View</th></tr></thead>
                    <tbody>
                        <?php $courses->data_seek(0); while($c = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td><?php echo htmlspecialchars($c['title']); ?></td>
                            <td><?php echo $c['price']; ?></td>
                            <td><?php echo $c['duration']; ?></td>
                            <td><?php echo $c['start_date']; ?></td>
                            <td><span class="status-badge <?php echo $c['status'] == 'active' ? 'status-active' : 'status-coming'; ?>"><?php echo $c['status']; ?></span></td>
                            <td class="action-buttons">
                                <a href="#" class="btn-edit" onclick="openEditCourseModal(<?php echo $c['id']; ?>)">Edit</a>
                                <a href="?toggle_course=<?php echo $c['id']; ?>" class="btn-toggle" onclick="return confirm('Toggle status?')">Toggle</a>
                                <a href="?delete_course=<?php echo $c['id']; ?>" class="btn-delete" onclick="return confirm('Delete this course?')">Delete</a>
                            </td>
                            <td><a href="../courses.php" target="_blank" class="btn-view"><i class="fas fa-eye"></i> View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== PRODUCT ORDERS SECTION ==================== -->
    <div id="product_ordersSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-shopping-cart"></i> Product Orders</h3><a href="view-orders.php" class="btn-add">View All Orders</a></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Product</th><th>Customer</th><th>Phone</th><th>Qty</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while($o = $product_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                            <td><?php echo $o['customer_phone']; ?></td>
                            <td><?php echo $o['quantity']; ?></td>
                            <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                            <td><?php echo date('M d', strtotime($o['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($o['status'] == 'pending'): ?><a href="?order_status=processing&order_id=<?php echo $o['id']; ?>" class="btn-processing" onclick="return confirm('Mark as processing?')">Process</a><?php endif; ?>
                                <?php if($o['status'] == 'processing'): ?><a href="?order_status=completed&order_id=<?php echo $o['id']; ?>" class="btn-completed" onclick="return confirm('Mark as completed?')">Complete</a><?php endif; ?>
                                <a href="?delete_order=<?php echo $o['id']; ?>" class="btn-delete" onclick="return confirm('Delete this order?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== SERVICE ORDERS SECTION ==================== -->
    <div id="service_ordersSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-paint-brush"></i> Service Orders (from Portfolio)</h3><a href="view-service-orders.php" class="btn-add">View All Service Orders</a></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Service</th><th>Package</th><th>Customer</th><th>Phone</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while($so = $service_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $so['id']; ?></td>
                            <td><?php echo htmlspecialchars($so['service']); ?></td>
                            <td><?php echo htmlspecialchars($so['package_name']); ?></td>
                            <td><?php echo htmlspecialchars($so['fullname']); ?></td>
                            <td><?php echo $so['phone']; ?></td>
                            <td><span class="status-badge status-<?php echo $so['status']; ?>"><?php echo ucfirst($so['status']); ?></span></td>
                            <td><?php echo date('M d', strtotime($so['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($so['status'] == 'pending'): ?><a href="?service_order_status=processing&service_order_id=<?php echo $so['id']; ?>" class="btn-processing" onclick="return confirm('Mark as processing?')">Process</a><?php endif; ?>
                                <?php if($so['status'] == 'processing'): ?><a href="?service_order_status=completed&service_order_id=<?php echo $so['id']; ?>" class="btn-completed" onclick="return confirm('Mark as completed?')">Complete</a><?php endif; ?>
                                <a href="?delete_service_order=<?php echo $so['id']; ?>" class="btn-delete" onclick="return confirm('Delete this order?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== REGISTRATIONS SECTION ==================== -->
    <div id="registrationsSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-user-plus"></i> Student Registrations</h3><a href="view-registrations.php" class="btn-add">View All Registrations</a></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Course</th><th>Receipt</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while($reg = $registrations->fetch_assoc()): 
                            $has_receipt = !empty($reg['payment_receipt']) && file_exists('../' . $reg['payment_receipt']);
                        ?>
                        <tr>
                            <td>#<?php echo $reg['id']; ?></td>
                            <td><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                            <td><?php echo $reg['phone']; ?></td>
                            <td><?php echo htmlspecialchars($reg['course']); ?></td>
                            <td><?php echo $has_receipt ? '<i class="fas fa-check-circle" style="color:#28a745;"></i>' : '<i class="fas fa-times-circle" style="color:#dc3545;"></i>'; ?></td>
                            <td><span class="status-badge status-<?php echo $reg['status']; ?>"><?php echo ucfirst($reg['status']); ?></span></td>
                            <td><?php echo date('M d', strtotime($reg['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($reg['status'] == 'pending'): ?>
                                    <a href="?approve_reg=<?php echo $reg['id']; ?>" class="btn-approve" onclick="return confirm('Approve this registration?')">Approve</a>
                                    <a href="?reject_reg=<?php echo $reg['id']; ?>" class="btn-reject" onclick="return confirm('Reject this registration?')">Reject</a>
                                <?php endif; ?>
                                <a href="?delete_registration=<?php echo $reg['id']; ?>" class="btn-delete" onclick="return confirm('Delete this registration?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== QUESTIONS SECTION ==================== -->
    <div id="questionsSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-question-circle"></i> User Questions</h3><a href="manage-questions.php" class="btn-add">View All Questions</a></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>ID</th><th>Type</th><th>Name</th><th>Question</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while($q = $questions->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $q['id']; ?></td>
                            <td><?php echo $q['is_registered'] ? '<span class="status-badge status-active">Registered</span>' : '<span class="status-badge status-pending">Guest</span>'; ?></td>
                            <td><?php echo htmlspecialchars($q['name']); ?></td>
                            <td style="max-width:250px;"><?php echo htmlspecialchars(substr($q['question'], 0, 60)); ?>...</td>
                            <td><span class="status-badge <?php echo $q['status'] == 'pending' ? 'status-pending' : 'status-active'; ?>"><?php echo ucfirst($q['status']); ?></span></td>
                            <td><?php echo date('M d', strtotime($q['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if($q['status'] == 'pending'): ?><a href="?answered_question=<?php echo $q['id']; ?>" class="btn-answered" onclick="return confirm('Mark as answered?')">Mark Answered</a><?php endif; ?>
                                <a href="?delete_question=<?php echo $q['id']; ?>" class="btn-delete" onclick="return confirm('Delete this question?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== ABOUT SETTINGS SECTION ==================== -->
    <div id="aboutSection" style="display: none;">
        <div class="section">
            <div class="section-header"><h3><i class="fas fa-info-circle"></i> About Page Settings</h3></div>
            <div style="padding:25px;">
                <form method="POST">
                    <h4 style="margin:20px 0 15px 0;color:#ff6b6b;">YouTube Channel Info</h4>
                    <div class="form-group"><label>YouTube Channel URL</label><input type="text" name="youtube_channel_url" value="<?php echo htmlspecialchars($about['youtube_channel_url']); ?>" required></div>
                    <?php img_upload_field('youtube_thumbnail', $about['youtube_thumbnail'], 'Channel Thumbnail', true); ?>
                    <div class="form-group"><label>Badge Text</label><input type="text" name="youtube_badge_text" value="<?php echo htmlspecialchars($about['youtube_badge_text']); ?>"></div>
                    
                    <h4 style="margin:20px 0 15px 0;color:#ff6b6b;">Instructor Info</h4>
                    <div class="form-group"><label>Instructor Name</label><input type="text" name="instructor_name" value="<?php echo htmlspecialchars($about['instructor_name']); ?>" required></div>
                    <div class="form-group"><label>Instructor Title</label><input type="text" name="instructor_title" value="<?php echo htmlspecialchars($about['instructor_title']); ?>" required></div>
                    <div class="form-group"><label>Instructor Bio</label><textarea name="instructor_bio"><?php echo htmlspecialchars($about['instructor_bio']); ?></textarea></div>
                    <?php img_upload_field('instructor_image', $about['instructor_image'], 'Instructor Image', true); ?>
                    
                    <h4 style="margin:20px 0 15px 0;color:#ff6b6b;">Featured Videos</h4>
                    <div class="form-group"><label>Video 1 URL</label><input type="text" name="video1_url" value="<?php echo htmlspecialchars($about['youtube_video_1_url']); ?>"></div>
                    <?php img_upload_field('video1_thumbnail', $about['youtube_video_1_thumbnail'], 'Video 1 Thumbnail'); ?>
                    <div class="form-group"><label>Video 1 Title</label><input type="text" name="video1_title" value="<?php echo htmlspecialchars($about['youtube_video_1_title']); ?>"></div>
                    <div class="form-group"><label>Video 1 Views</label><input type="text" name="video1_views" value="<?php echo htmlspecialchars($about['youtube_video_1_views']); ?>"></div>
                    
                    <div class="form-group"><label>Video 2 URL</label><input type="text" name="video2_url" value="<?php echo htmlspecialchars($about['youtube_video_2_url']); ?>"></div>
                    <?php img_upload_field('video2_thumbnail', $about['youtube_video_2_thumbnail'], 'Video 2 Thumbnail'); ?>
                    <div class="form-group"><label>Video 2 Title</label><input type="text" name="video2_title" value="<?php echo htmlspecialchars($about['youtube_video_2_title']); ?>"></div>
                    <div class="form-group"><label>Video 2 Views</label><input type="text" name="video2_views" value="<?php echo htmlspecialchars($about['youtube_video_2_views']); ?>"></div>
                    
                    <div class="form-group"><label>Video 3 URL</label><input type="text" name="video3_url" value="<?php echo htmlspecialchars($about['youtube_video_3_url']); ?>"></div>
                    <?php img_upload_field('video3_thumbnail', $about['youtube_video_3_thumbnail'], 'Video 3 Thumbnail'); ?>
                    <div class="form-group"><label>Video 3 Title</label><input type="text" name="video3_title" value="<?php echo htmlspecialchars($about['youtube_video_3_title']); ?>"></div>
                    <div class="form-group"><label>Video 3 Views</label><input type="text" name="video3_views" value="<?php echo htmlspecialchars($about['youtube_video_3_views']); ?>"></div>
                    
                    <button type="submit" name="update_about" class="btn-submit">Update About Page</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==================== COURSE MODAL ==================== -->
<div id="courseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3 id="courseModalTitle">Add Course</h3><span class="close-modal" onclick="closeCourseModal()">&times;</span></div>
        <form method="POST" id="courseForm">
            <input type="hidden" name="course_id" id="course_id">
            <div class="form-group"><label>Title *</label><input type="text" name="title" id="course_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="course_description" rows="4" required></textarea></div>
            <div class="form-group"><label>Price</label><input type="text" name="price" id="course_price" placeholder="e.g., 2,990 Birr" required></div>
            <div class="form-group"><label>Duration</label><input type="text" name="duration" id="course_duration" placeholder="e.g., 8 Weeks" required></div>
            <div class="form-group"><label>Start Date</label><input type="text" name="start_date" id="course_start_date" placeholder="e.g., June 12, 2025" required></div>
            <div class="form-group"><label>Icon Class</label><input type="text" name="icon_class" id="course_icon" value="fas fa-palette"></div>
            <div class="form-group"><label>Status</label><select name="status" id="course_status"><option value="active">Active</option><option value="coming_soon">Coming Soon</option></select></div>
            <div class="form-group"><label>Badge Text</label><input type="text" name="badge_text" id="course_badge" placeholder="e.g., Most Popular"></div>
            <button type="submit" name="add_course" id="courseSubmitBtn" class="btn-submit">Save Course</button>
        </form>
    </div>
</div>

<!-- ==================== SERVICE MODAL ==================== -->
<div id="serviceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3 id="serviceModalTitle">Add Service</h3><span class="close-modal" onclick="closeServiceModal()">&times;</span></div>
        <form method="POST" id="serviceForm">
            <input type="hidden" name="service_id" id="service_id_val">
            <div class="form-group"><label>Title *</label><input type="text" name="title" id="service_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="service_description" rows="3"></textarea></div>
            <div class="form-group"><label>Icon Class *</label><input type="text" name="icon_class" id="service_icon" placeholder="fas fa-paint-brush" required></div>
            <div class="form-group"><label>Features (separate with | )</label><textarea name="features" id="service_features" rows="3" placeholder="Feature 1|Feature 2|Feature 3"></textarea></div>
            <div class="form-group"><label>Price *</label><input type="text" name="price" id="service_price" placeholder="From 1,500 Birr" required></div>
            <div class="form-group"><label>Status</label><select name="status" id="service_status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="form-group"><label>Display Order</label><input type="number" name="display_order" id="service_order" value="0"></div>
            <button type="submit" name="add_service" id="serviceSubmitBtn" class="btn-submit">Save Service</button>
        </form>
    </div>
</div>

<!-- ==================== PRODUCT MODAL ==================== -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3 id="productModalTitle">Add Product</h3><span class="close-modal" onclick="closeProductModal()">&times;</span></div>
        <form method="POST" id="productForm">
            <input type="hidden" name="product_id" id="product_id_val">
            <div class="form-group"><label>Title *</label><input type="text" name="title" id="product_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="product_description" rows="3"></textarea></div>
            <div class="form-group"><label>Price *</label><input type="text" name="price" id="product_price" placeholder="From 450 Birr" required></div>
            <div class="form-group"><label>Image *</label><div class="img-field-wrap">
                <input type="text" name="image_url" id="product_image" placeholder="Upload below, or paste an image URL" required oninput="syncImgPreview('product_image')">
                <div class="img-upload-row">
                    <button type="button" class="img-upload-btn" onclick="document.getElementById('product_image_file').click()"><i class="fas fa-upload"></i> Upload image</button>
                    <input type="file" id="product_image_file" accept="image/*" style="display:none" onchange="uploadImageFor('product_image', this)">
                    <span class="img-upload-status" id="product_image_status"></span>
                </div>
                <img class="img-field-preview" id="product_image_preview" src="" style="display:none">
            </div></div>
            <div class="form-group"><label>Category</label><select name="category" id="product_category"><option value="apparel">Apparel</option><option value="accessories">Accessories</option><option value="gifts">Gifts</option><option value="decor">Decor</option></select></div>
            <div class="form-group"><label>Status</label><select name="status" id="product_status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="form-group"><label>Display Order</label><input type="number" name="display_order" id="product_order" value="0"></div>
            <button type="submit" name="add_product" id="productSubmitBtn" class="btn-submit">Save Product</button>
        </form>
    </div>
</div>

<!-- ==================== PORTFOLIO MODAL ==================== -->
<div id="portfolioModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3 id="portfolioModalTitle">Add Gallery Item</h3><span class="close-modal" onclick="closePortfolioModal()">&times;</span></div>
        <form method="POST" id="portfolioForm">
            <input type="hidden" name="portfolio_id" id="portfolio_id_val">
            <div class="form-group"><label>Service *</label><select name="service_id" id="portfolio_service_id" required onchange="updatePortfolioServiceName()">
                <option value="">Select Service</option>
                <?php $services_list = $conn->query("SELECT id, title FROM services WHERE status='active'"); while($s = $services_list->fetch_assoc()): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
                <?php endwhile; ?>
            </select></div>
            <input type="hidden" name="service_name" id="portfolio_service_name">
            <div class="form-group"><label>Title *</label><input type="text" name="title" id="portfolio_title" placeholder="e.g., Ethiopian Coffee Brand Identity" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="portfolio_description" rows="3"></textarea></div>
            <div class="form-group"><label>Media *</label><div class="img-field-wrap">
                <input type="text" name="media_url" id="portfolio_media_url" placeholder="Upload below, or paste an image/video URL" required oninput="syncImgPreview('portfolio_media_url')">
                <div class="img-upload-row">
                    <button type="button" class="img-upload-btn" onclick="document.getElementById('portfolio_media_url_file').click()"><i class="fas fa-upload"></i> Upload image or video</button>
                    <input type="file" id="portfolio_media_url_file" accept="image/*,video/mp4,video/webm" style="display:none" onchange="uploadImageFor('portfolio_media_url', this)">
                    <span class="img-upload-status" id="portfolio_media_url_status"></span>
                </div>
                <img class="img-field-preview" id="portfolio_media_url_preview" src="" style="display:none">
            </div></div>
            <div class="form-group"><label>Media Type</label><select name="media_type" id="portfolio_media_type"><option value="image">Image</option><option value="video">Video</option></select></div>
            <button type="submit" name="add_portfolio" id="portfolioSubmitBtn" class="btn-submit">Save Gallery Item</button>
        </form>
    </div>
</div>

<!-- ==================== PACKAGE MODAL ==================== -->
<div id="packageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3 id="packageModalTitle">Add Package</h3><span class="close-modal" onclick="closePackageModal()">&times;</span></div>
        <form method="POST" id="packageForm">
            <input type="hidden" name="package_id" id="package_id_val">
            <div class="form-group"><label>Service *</label><select name="service_id" id="package_service_id" required onchange="updatePackageServiceName()">
                <option value="">Select Service</option>
                <?php $services_list = $conn->query("SELECT id, title FROM services WHERE status='active'"); while($s = $services_list->fetch_assoc()): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
                <?php endwhile; ?>
            </select></div>
            <input type="hidden" name="service_name" id="package_service_name">
            <div class="form-group"><label>Package Name *</label><input type="text" name="package_name" id="package_name" placeholder="e.g., Standard Package" required></div>
            <div class="form-group"><label>Price *</label><input type="text" name="package_price" id="package_price" placeholder="e.g., 1,500 Birr" required></div>
            <div class="form-group"><label>Features (separate with | )</label><textarea name="features" id="package_features" rows="4" placeholder="Logo Design|Basic Brand Colors|2 Revisions|3 Days Delivery" required></textarea></div>
            <button type="submit" name="add_package" id="packageSubmitBtn" class="btn-submit">Save Package</button>
        </form>
    </div>
</div>

<script>
// Store data for editing
let coursesData = <?php $courses_json = []; $all_c = $conn->query("SELECT * FROM courses"); while($c = $all_c->fetch_assoc()) { $courses_json[$c['id']] = $c; } echo json_encode($courses_json); ?>;
let servicesData = <?php $services_json = []; $all_s = $conn->query("SELECT * FROM services"); while($s = $all_s->fetch_assoc()) { $services_json[$s['id']] = $s; } echo json_encode($services_json); ?>;
let productsData = <?php $products_json = []; $all_p = $conn->query("SELECT * FROM products"); while($p = $all_p->fetch_assoc()) { $products_json[$p['id']] = $p; } echo json_encode($products_json); ?>;
let portfolioData = <?php $portfolio_json = []; $all_pt = $conn->query("SELECT * FROM portfolio_items"); while($pt = $all_pt->fetch_assoc()) { $portfolio_json[$pt['id']] = $pt; } echo json_encode($portfolio_json); ?>;
let packagesData = <?php $packages_json = []; $all_pkg = $conn->query("SELECT * FROM service_packages"); while($pkg = $all_pkg->fetch_assoc()) { $packages_json[$pkg['id']] = $pkg; } echo json_encode($packages_json); ?>;

// Toggle functions
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }
function toggleMenu(element) { 
    element.nextElementSibling.classList.toggle('show'); 
    element.querySelector('.menu-arrow').classList.toggle('rotated'); 
    element.classList.toggle('active'); 
}
function toggleGroup(element) { 
    element.nextElementSibling.classList.toggle('show'); 
    element.querySelector('i:last-child').classList.toggle('rotated'); 
}

// Show sections
function showSection(section) {
    const sections = ['dashboard', 'services', 'service_gallery', 'service_packages', 'products', 'product_gallery', 'courses', 'product_orders', 'service_orders', 'registrations', 'questions', 'about'];
    sections.forEach(s => { document.getElementById(s + 'Section').style.display = 'none'; });
    document.getElementById(section + 'Section').style.display = 'block';
    if(window.innerWidth <= 768) document.getElementById('sidebar').classList.remove('active');
}

// ==================== COURSE MODALS ====================
function openAddCourseModal() { 
    document.getElementById('courseModalTitle').innerText = 'Add Course'; 
    document.getElementById('courseForm').reset(); 
    document.getElementById('course_id').value = ''; 
    document.getElementById('courseSubmitBtn').name = 'add_course'; 
    document.getElementById('courseModal').style.display = 'block'; 
}
function openEditCourseModal(id) { 
    let c = coursesData[id]; 
    if(c) { 
        document.getElementById('courseModalTitle').innerText = 'Edit Course'; 
        document.getElementById('course_id').value = c.id; 
        document.getElementById('course_title').value = c.title; 
        document.getElementById('course_description').value = c.description; 
        document.getElementById('course_price').value = c.price; 
        document.getElementById('course_duration').value = c.duration; 
        document.getElementById('course_start_date').value = c.start_date; 
        document.getElementById('course_icon').value = c.icon_class; 
        document.getElementById('course_status').value = c.status; 
        document.getElementById('course_badge').value = c.badge_text; 
        document.getElementById('courseSubmitBtn').name = 'edit_course'; 
        document.getElementById('courseModal').style.display = 'block'; 
    } 
}
function closeCourseModal() { document.getElementById('courseModal').style.display = 'none'; }

// ==================== SERVICE MODALS ====================
function openAddServiceModal() { 
    document.getElementById('serviceModalTitle').innerText = 'Add Service'; 
    document.getElementById('serviceForm').reset(); 
    document.getElementById('service_id_val').value = ''; 
    document.getElementById('serviceSubmitBtn').name = 'add_service'; 
    document.getElementById('serviceModal').style.display = 'block'; 
}
function openEditServiceModal(id) { 
    let s = servicesData[id]; 
    if(s) { 
        document.getElementById('serviceModalTitle').innerText = 'Edit Service'; 
        document.getElementById('service_id_val').value = s.id; 
        document.getElementById('service_title').value = s.title; 
        document.getElementById('service_description').value = s.description; 
        document.getElementById('service_icon').value = s.icon_class; 
        document.getElementById('service_features').value = s.features; 
        document.getElementById('service_price').value = s.price; 
        document.getElementById('service_status').value = s.status; 
        document.getElementById('service_order').value = s.display_order; 
        document.getElementById('serviceSubmitBtn').name = 'edit_service'; 
        document.getElementById('serviceModal').style.display = 'block'; 
    } 
}
function closeServiceModal() { document.getElementById('serviceModal').style.display = 'none'; }

// ==================== PRODUCT MODALS ====================
function openAddProductModal() { 
    document.getElementById('productModalTitle').innerText = 'Add Product'; 
    document.getElementById('productForm').reset(); 
    syncImgPreview('product_image');
    document.getElementById('product_id_val').value = ''; 
    document.getElementById('productSubmitBtn').name = 'add_product'; 
    document.getElementById('productModal').style.display = 'block'; 
}
function openEditProductModal(id) { 
    let p = productsData[id]; 
    if(p) { 
        document.getElementById('productModalTitle').innerText = 'Edit Product'; 
        document.getElementById('product_id_val').value = p.id; 
        document.getElementById('product_title').value = p.title; 
        document.getElementById('product_description').value = p.description; 
        document.getElementById('product_price').value = p.price; 
        document.getElementById('product_image').value = p.image_url; 
        syncImgPreview('product_image');
        document.getElementById('product_category').value = p.category; 
        document.getElementById('product_status').value = p.status; 
        document.getElementById('product_order').value = p.display_order; 
        document.getElementById('productSubmitBtn').name = 'edit_product'; 
        document.getElementById('productModal').style.display = 'block'; 
    } 
}
function closeProductModal() { document.getElementById('productModal').style.display = 'none'; }

// ==================== PORTFOLIO MODALS ====================
function openAddPortfolioModal() { 
    document.getElementById('portfolioModalTitle').innerText = 'Add Gallery Item'; 
    document.getElementById('portfolioForm').reset(); 
    syncImgPreview('portfolio_media_url');
    document.getElementById('portfolio_id_val').value = ''; 
    document.getElementById('portfolioSubmitBtn').name = 'add_portfolio'; 
    document.getElementById('portfolioModal').style.display = 'block'; 
}
function openEditPortfolioModal(id) { 
    let pt = portfolioData[id]; 
    if(pt) { 
        document.getElementById('portfolioModalTitle').innerText = 'Edit Gallery Item'; 
        document.getElementById('portfolio_id_val').value = pt.id; 
        document.getElementById('portfolio_service_id').value = pt.service_id; 
        document.getElementById('portfolio_service_name').value = pt.service_name; 
        document.getElementById('portfolio_title').value = pt.title; 
        document.getElementById('portfolio_description').value = pt.description; 
        document.getElementById('portfolio_media_url').value = pt.media_url; 
        syncImgPreview('portfolio_media_url');
        document.getElementById('portfolio_media_type').value = pt.media_type; 
        document.getElementById('portfolioSubmitBtn').name = 'edit_portfolio'; 
        document.getElementById('portfolioModal').style.display = 'block'; 
    } 
}
function closePortfolioModal() { document.getElementById('portfolioModal').style.display = 'none'; }
function updatePortfolioServiceName() { 
    let select = document.getElementById('portfolio_service_id'); 
    document.getElementById('portfolio_service_name').value = select.options[select.selectedIndex]?.text || ''; 
}

// ==================== PACKAGE MODALS ====================
function openAddPackageModal() { 
    document.getElementById('packageModalTitle').innerText = 'Add Package'; 
    document.getElementById('packageForm').reset(); 
    document.getElementById('package_id_val').value = ''; 
    document.getElementById('packageSubmitBtn').name = 'add_package'; 
    document.getElementById('packageModal').style.display = 'block'; 
}
function openEditPackageModal(id) { 
    let pkg = packagesData[id]; 
    if(pkg) { 
        document.getElementById('packageModalTitle').innerText = 'Edit Package'; 
        document.getElementById('package_id_val').value = pkg.id; 
        document.getElementById('package_service_id').value = pkg.service_id; 
        document.getElementById('package_service_name').value = pkg.service_name; 
        document.getElementById('package_name').value = pkg.package_name; 
        document.getElementById('package_price').value = pkg.package_price; 
        document.getElementById('package_features').value = pkg.features; 
        document.getElementById('packageSubmitBtn').name = 'edit_package'; 
        document.getElementById('packageModal').style.display = 'block'; 
    } 
}
function closePackageModal() { document.getElementById('packageModal').style.display = 'none'; }
function updatePackageServiceName() { 
    let select = document.getElementById('package_service_id'); 
    document.getElementById('package_service_name').value = select.options[select.selectedIndex]?.text || ''; 
}

// Close modals on outside click
window.onclick = function(event) {
    if (event.target == document.getElementById('courseModal')) closeCourseModal();
    if (event.target == document.getElementById('serviceModal')) closeServiceModal();
    if (event.target == document.getElementById('productModal')) closeProductModal();
    if (event.target == document.getElementById('portfolioModal')) closePortfolioModal();
    if (event.target == document.getElementById('packageModal')) closePackageModal();
}

// Default show dashboard
showSection('dashboard');
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