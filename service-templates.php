<?php
require_once 'config.php';

$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

$service_query = "SELECT * FROM services WHERE id = $service_id AND status = 'active'";
$service_result = $conn->query($service_query);
$service = $service_result->fetch_assoc();

if (!$service) {
    header("Location: services.php");
    exit();
}

$templates_query = "SELECT * FROM service_templates 
                    WHERE service_id = $service_id AND status = 'active' 
                    ORDER BY display_order ASC, id ASC";
$templates = $conn->query($templates_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($service['title']); ?> Templates | Dan Creatives</title>
    <link rel="icon" href="images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/responsive-nav.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1a2a3a;
            line-height: 1.5;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.5s ease;
        }

        .toast-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 450px;
        }

        .toast-success i {
            font-size: 24px;
        }

        .toast-close {
            cursor: pointer;
            font-size: 20px;
            opacity: 0.7;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1a2a3a;
        }

        .logo-text span {
            color: #ff6b6b;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-menu a:hover, .nav-menu a.active {
            color: #ff6b6b;
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #ff6b6b;
        }

        .templates-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            text-align: center;
            color: white;
        }

        .templates-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .templates-hero h1 span {
            color: #ffd700;
        }

        .templates-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 50px;
            transition: 0.3s;
        }

        .back-link:hover {
            background: rgba(255,255,255,0.3);
        }

        .templates-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
        }

        .template-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .template-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .template-image {
            position: relative;
            overflow: hidden;
            height: 250px;
        }

        .template-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .template-card:hover .template-image img {
            transform: scale(1.05);
        }

        .template-info {
            padding: 24px;
        }

        .template-info h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #1a2a3a;
        }

        .template-info p {
            color: #718096;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .btn-choose {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            cursor: pointer;
            border: none;
            width: 100%;
            text-align: center;
        }

        .btn-choose:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .order-modal {
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

        .modal-container {
            background: white;
            width: 100%;
            max-width: 550px;
            border-radius: 24px;
            overflow: hidden;
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
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

        .modal-header {
            background: linear-gradient(135deg, #1a2a3a, #2d3e4e);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .modal-header h3 {
            font-size: 1.2rem;
            margin: 0;
        }

        .modal-header h3 i {
            color: #ff6b6b;
            margin-right: 10px;
        }

        .close-modal {
            font-size: 28px;
            cursor: pointer;
            transition: 0.3s;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .close-modal:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 20px 25px;
            overflow-y: auto;
            flex: 1;
        }

        .template-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .template-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
        }

        .template-preview h4 {
            color: #1a2a3a;
            margin-bottom: 5px;
        }

        .template-preview p {
            font-size: 0.85rem;
            color: #718096;
        }

        .contact-info-modal {
            background: #fff3cd;
            padding: 12px;
            border-radius: 12px;
            margin: 15px 0;
            text-align: center;
        }

        .contact-info-modal p {
            margin: 4px 0;
            color: #856404;
            font-size: 0.8rem;
        }

        .form-group-modal {
            margin-bottom: 15px;
        }

        .form-group-modal label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #1a2a3a;
            font-size: 0.85rem;
        }

        .form-group-modal input,
        .form-group-modal textarea,
        .form-group-modal select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .form-group-modal input:focus,
        .form-group-modal textarea:focus {
            outline: none;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255,107,107,0.1);
        }

        .form-group-modal textarea {
            min-height: 70px;
            resize: vertical;
        }

        .btn-submit-order {
            width: 100%;
            background: #ff6b6b;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit-order:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .btn-submit-order:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .order-feedback {
            margin-top: 12px;
            padding: 10px;
            border-radius: 8px;
            display: none;
            font-size: 0.85rem;
        }

        .order-feedback.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }

        .order-feedback.error {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }

        .footer {
            background: #1a2a3a;
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            margin-bottom: 20px;
            color: #ff8e53;
        }

        .footer-section a {
            display: block;
            color: #cbd5e0;
            text-decoration: none;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .footer-section a:hover {
            color: #ff6b6b;
        }

        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 20px;
            }
            
            .nav-menu {
                display: none;
            }
            
            .hamburger {
                display: block;
            }
            
            .nav-menu.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background: white;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                text-align: center;
                gap: 20px;
                z-index: 999;
            }
            
            .templates-hero h1 {
                font-size: 1.8rem;
            }
            
            .templates-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-container {
                max-width: 95%;
            }
            
            .template-preview {
                flex-direction: column;
                text-align: center;
            }
            
            .toast-notification {
                top: 10px;
                right: 10px;
                left: 10px;
            }
            
            .toast-success {
                min-width: auto;
                width: calc(100% - 20px);
                max-width: none;
            }
        }
    </style>
</head>
<body>

<div id="toastContainer"></div>

<nav class="navbar">
    <div class="container nav-container">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="images/logo.png" alt="Dan Creatives Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/40x40/ff6b6b/white?text=DC'">
            <div class="logo-text">Dan<span>Creatives</span></div>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<section class="templates-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($service['title']); ?> <span>Templates</span></h1>
        <p>Browse our collection of professional designs and choose your favorite style</p>
        <a href="services.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Services</a>
    </div>
</section>

<section class="templates-section">
    <div class="container">
        <?php if($templates && $templates->num_rows > 0): ?>
        <div class="templates-grid">
            <?php while($template = $templates->fetch_assoc()): ?>
            <div class="template-card">
                <div class="template-image">
                    <img src="<?php echo htmlspecialchars($template['image_url']); ?>" alt="<?php echo htmlspecialchars($template['title']); ?>">
                </div>
                <div class="template-info">
                    <h3><?php echo htmlspecialchars($template['title']); ?></h3>
                    <p><?php echo htmlspecialchars($template['description']); ?></p>
                    <button class="btn-choose" onclick="openOrderModal(<?php echo $template['id']; ?>, '<?php echo addslashes($template['title']); ?>', '<?php echo addslashes($template['image_url']); ?>', <?php echo $service_id; ?>, '<?php echo addslashes($service['title']); ?>')">
                        Choose This Style <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-templates" style="text-align: center; padding: 60px; background: white; border-radius: 20px;">
            <i class="fas fa-images" style="font-size: 64px; color: #ccc;"></i>
            <h3 style="margin-top: 20px;">No Templates Available Yet</h3>
            <p>Check back soon for design templates!</p>
            <a href="services.php" class="btn-choose" style="display: inline-block; width: auto; margin-top: 20px;">Back to Services</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<div id="orderModal" class="order-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-paint-brush"></i> Request This Design</h3>
            <span class="close-modal" onclick="closeOrderModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="template-preview" id="templatePreview">
                <img id="previewImage" src="" alt="Template">
                <div>
                    <h4 id="previewTitle">Template Name</h4>
                    <p id="previewService">Service Name</p>
                </div>
            </div>
            
            <div class="contact-info-modal">
                <p><i class="fab fa-telegram"></i> Telegram: <strong>@genesis306</strong></p>
                <p><i class="fas fa-phone"></i> Phone: <strong>+251 920188600</strong></p>
                <p><i class="fas fa-envelope"></i> Email: <strong>dangraphics@gmail.com</strong></p>
            </div>
            
            <form id="orderForm" onsubmit="return false;">
                <input type="hidden" id="service_id" name="service_id">
                <input type="hidden" id="service_name" name="service_name">
                <input type="hidden" id="template_id" name="template_id">
                <input type="hidden" id="template_name" name="template_name">
                <input type="hidden" id="template_image" name="template_image">
                
                <div class="form-group-modal">
                    <label><i class="fas fa-user"></i> Your Full Name *</label>
                    <input type="text" id="customer_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="customer_email" placeholder="We'll send proposal here (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" id="customer_phone" placeholder="Your active phone number" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fab fa-telegram"></i> Telegram Username</label>
                    <input type="text" id="customer_telegram" placeholder="@username (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-dollar-sign"></i> Budget Range *</label>
                    <select id="budget" required>
                        <option value="Under 1,000 Birr">Under 1,000 Birr</option>
                        <option value="1,000 - 3,000 Birr">1,000 - 3,000 Birr</option>
                        <option value="3,000 - 5,000 Birr">3,000 - 5,000 Birr</option>
                        <option value="5,000 - 10,000 Birr">5,000 - 10,000 Birr</option>
                        <option value="10,000+ Birr">10,000+ Birr</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-calendar"></i> Deadline *</label>
                    <select id="deadline" required>
                        <option value="Within 1 week">Within 1 week</option>
                        <option value="Within 2 weeks">Within 2 weeks</option>
                        <option value="Within 1 month">Within 1 month</option>
                        <option value="Flexible">Flexible</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-file-alt"></i> Additional Requirements</label>
                    <textarea id="requirements" placeholder="Any specific details, colors, or ideas..."></textarea>
                </div>
                <div id="orderFeedback" class="order-feedback"></div>
                <button type="button" class="btn-submit-order" id="submitOrderBtn" onclick="submitOrder()">
                    <i class="fas fa-paper-plane"></i> Send Request
                </button>
            </form>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Dan Creatives</h4>
                <p>Empowering the next generation of digital creators with professional design education and premium products.</p>
                <div class="social-links">
                    <a href="https://www.youtube.com/@DanGraphics1" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="https://t.me/genesis306"><i class="fab fa-telegram"></i></a>
                    <a href="https://www.tiktok.com/@dancreative30_6"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="index.php">Home</a>
                <a href="courses.php">Courses</a>
                <a href="products.php">Products</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="register.php">Register</a>
            </div>
            <div class="footer-section">
                <h4>Services</h4>
                <a href="services.php">Logo Design</a>
                <a href="services.php">Brand Design</a>
                <a href="services.php">Social Media Posters</a>
                <a href="services.php">Thumbnail Design</a>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <a href="#">+251 920188600</a>
                <a href="#">dangraphics@gmail.com</a>
                <a href="https://t.me/genesis306">Telegram</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Dan Creatives - All rights reserved | ዳን ክሬቲቭስ</p>
        </div>
    </div>
</footer>

<script>
function showSuccessToast(title, message) {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast_' + Date.now();
    
    const toastHTML = `
        <div id="${toastId}" class="toast-notification">
            <div class="toast-success">
                <i class="fas fa-check-circle"></i>
                <div class="toast-content">
                    <div class="toast-title"><strong>${title}</strong></div>
                    <div class="toast-message">${message}</div>
                </div>
                <i class="fas fa-times toast-close" onclick="closeToast('${toastId}')"></i>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.animation = 'slideOutRight 0.5s ease';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }
}


function openOrderModal(templateId, templateTitle, templateImage, serviceId, serviceTitle) {
    document.getElementById('previewImage').src = templateImage;
    document.getElementById('previewTitle').innerHTML = templateTitle;
    document.getElementById('previewService').innerHTML = serviceTitle;
    document.getElementById('template_id').value = templateId;
    document.getElementById('template_name').value = templateTitle;
    document.getElementById('template_image').value = templateImage;
    document.getElementById('service_id').value = serviceId;
    document.getElementById('service_name').value = serviceTitle;
    document.getElementById('orderModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    const feedback = document.getElementById('orderFeedback');
    feedback.style.display = 'none';
    feedback.className = 'order-feedback';
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('orderForm').reset();
    const feedback = document.getElementById('orderFeedback');
    feedback.style.display = 'none';
    feedback.className = 'order-feedback';
}

window.onclick = function(event) {
    let modal = document.getElementById('orderModal');
    if (event.target == modal) {
        closeOrderModal();
    }
}

async function submitOrder() {
    const service_id = document.getElementById('service_id').value;
    const service_name = document.getElementById('service_name').value;
    const template_id = document.getElementById('template_id').value;
    const template_name = document.getElementById('template_name').value;
    const template_image = document.getElementById('template_image').value;
    const customer_name = document.getElementById('customer_name').value.trim();
    const customer_email = document.getElementById('customer_email').value.trim();
    const customer_phone = document.getElementById('customer_phone').value.trim();
    const customer_telegram = document.getElementById('customer_telegram').value.trim();
    const budget = document.getElementById('budget').value;
    const deadline = document.getElementById('deadline').value;
    const requirements = document.getElementById('requirements').value.trim();
    const feedback = document.getElementById('orderFeedback');
    const submitBtn = document.getElementById('submitOrderBtn');
    
    if (!customer_name) {
        showFeedback(feedback, 'Please enter your full name', 'error');
        return;
    }
    
    if (!customer_phone) {
        showFeedback(feedback, 'Please enter your phone number', 'error');
        return;
    }
    
    if (customer_phone.length < 9) {
        showFeedback(feedback, 'Please enter a valid phone number', 'error');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (customer_email && !emailRegex.test(customer_email)) {
        showFeedback(feedback, 'Please enter a valid email address', 'error');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    const formData = new URLSearchParams();
    formData.append('service_id', service_id);
    formData.append('service_name', service_name);
    formData.append('template_id', template_id);
    formData.append('template_name', template_name);
    formData.append('template_image', template_image);
    formData.append('customer_name', customer_name);
    formData.append('customer_email', customer_email);
    formData.append('customer_phone', customer_phone);
    formData.append('customer_telegram', customer_telegram);
    formData.append('budget', budget);
    formData.append('deadline', deadline);
    formData.append('requirements', requirements);
    
    try {
        const response = await fetch('submit_template_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessToast('🎉 Request Sent Successfully!', result.message);
            closeOrderModal();
        } else {
            showFeedback(feedback, result.message || 'Submission failed', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showFeedback(feedback, 'Network error. Please check your connection and try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Request';
    }
}

function showFeedback(element, message, type) {
    element.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
    element.className = 'order-feedback ' + type;
    element.style.display = 'block';
}
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>