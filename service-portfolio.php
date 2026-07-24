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

// Get portfolio items for this service
$portfolio_query = "SELECT * FROM portfolio_items 
                    WHERE service_id = $service_id AND status = 'active' 
                    ORDER BY display_order ASC, id ASC";
$portfolio_items = $conn->query($portfolio_query);

// Get packages for this service from database
$packages_query = "SELECT * FROM service_packages 
                   WHERE service_id = $service_id AND status = 'active' 
                   ORDER BY display_order ASC";
$packages_result = $conn->query($packages_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($service['title']); ?> Portfolio | Dan Creatives</title>
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
            max-width: 1400px;
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

        .toast-success i { font-size: 24px; }
        .toast-close { cursor: pointer; font-size: 20px; opacity: 0.7; }

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

        .logo-text span { color: #ff6b6b; }

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

        .nav-menu a:hover, .nav-menu a.active { color: #ff6b6b; }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #ff6b6b;
        }

        .portfolio-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            text-align: center;
            color: white;
        }

        .portfolio-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .portfolio-hero h1 span { color: #ffd700; }

        .portfolio-hero p { font-size: 1.1rem; opacity: 0.9; }

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

        .back-link:hover { background: rgba(255,255,255,0.3); }

        /* Auto-sliding Horizontal Gallery */
        .gallery-section {
            padding: 60px 0 40px;
            background: #f8f9fa;
            overflow: hidden;
        }

        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .slider-wrapper {
            overflow: hidden;
            width: 100%;
        }

        .slider-track {
            display: flex;
            gap: 30px;
            animation: scroll 25s linear infinite;
            width: fit-content;
        }

        .slider-track:hover {
            animation-play-state: paused;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .portfolio-card {
            width: 350px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .portfolio-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .portfolio-media {
            position: relative;
            overflow: hidden;
            height: 250px;
            background: #000;
        }

        .portfolio-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .portfolio-card:hover .portfolio-media img {
            transform: scale(1.05);
        }

        .portfolio-info {
            padding: 20px;
        }

        .portfolio-info h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #1a2a3a;
        }

        .portfolio-info p {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Packages Section */
        .packages-section {
            padding: 60px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2rem;
            color: #1a2a3a;
        }

        .section-title h2 span {
            color: #ff6b6b;
        }

        .section-title p {
            color: #718096;
            margin-top: 10px;
        }

        .packages-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .package-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            width: 320px;
            position: relative;
            border: 1px solid #eef2f6;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .package-header {
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .package-header h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .package-price {
            font-size: 2rem;
            font-weight: 800;
        }

        .package-price small {
            font-size: 0.8rem;
            font-weight: normal;
        }

        .package-body {
            padding: 25px;
        }

        .package-features {
            list-style: none;
            margin-bottom: 25px;
        }

        .package-features li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4a5568;
            border-bottom: 1px solid #f0f0f0;
        }

        .package-features i {
            color: #ff6b6b;
            font-size: 0.9rem;
        }

        .btn-select-package {
            width: 100%;
            background: #ff6b6b;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-select-package:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        /* Registration Popup Modal */
        .register-modal {
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
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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

        .modal-header h3 { font-size: 1.2rem; margin: 0; }
        .modal-header h3 i { color: #ff6b6b; margin-right: 10px; }

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

        .selected-package-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #ff6b6b;
        }

        .selected-package-info h4 {
            color: #1a2a3a;
            margin-bottom: 5px;
        }

        .selected-package-info p {
            color: #ff6b6b;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .contact-info-modal {
            background: #fff3cd;
            padding: 12px;
            border-radius: 12px;
            margin: 15px 0;
            text-align: center;
        }

        .contact-info-modal p { margin: 4px 0; color: #856404; font-size: 0.8rem; }

        .form-group-modal { margin-bottom: 15px; }

        .form-group-modal label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #1a2a3a;
            font-size: 0.85rem;
        }

        .form-group-modal input,
        .form-group-modal textarea {
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

        .btn-submit-register {
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

        .btn-submit-register:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .btn-submit-register:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .register-feedback {
            margin-top: 12px;
            padding: 10px;
            border-radius: 8px;
            display: none;
            font-size: 0.85rem;
        }

        .register-feedback.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }

        .register-feedback.error {
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

        .footer-section a:hover { color: #ff6b6b; }

        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }

        .social-links a { font-size: 1.3rem; }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .container { padding: 0 20px; }
            .nav-menu { display: none; }
            .hamburger { display: block; }
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
            .portfolio-hero h1 { font-size: 1.8rem; }
            .portfolio-card { width: 280px; }
            .portfolio-media { height: 200px; }
            .packages-grid { gap: 20px; }
            .package-card { width: 100%; max-width: 320px; }
            .modal-container { max-width: 95%; }
            .toast-notification { top: 10px; right: 10px; left: 10px; }
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
            <li><a href="services.php" class="active">Services</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<section class="portfolio-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($service['title']); ?> <span>Portfolio</span></h1>
        <p>Browse our collection of previous work</p>
        <a href="services.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Services</a>
    </div>
</section>

<!-- Gallery Section - Previous Work First -->
<section class="gallery-section">
    <div class="container">
        <?php if($portfolio_items && $portfolio_items->num_rows > 0): ?>
        <div class="slider-container">
            <div class="slider-wrapper">
                <div class="slider-track" id="sliderTrack">
                    <?php 
                    $items = [];
                    while($item = $portfolio_items->fetch_assoc()) {
                        $items[] = $item;
                    }
                    $all_items = array_merge($items, $items);
                    foreach($all_items as $item): 
                    ?>
                    <div class="portfolio-card">
                        <div class="portfolio-media">
                            <img src="<?php echo $item['media_url']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="portfolio-info">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="no-portfolio" style="text-align: center; padding: 60px; background: white; border-radius: 20px;">
            <i class="fas fa-images" style="font-size: 48px; color: #ccc;"></i>
            <h3 style="margin-top: 20px;">No Portfolio Items Yet</h3>
            <p>Check back soon for our latest work!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Packages Section - Dynamic from Database -->
<?php if($packages_result && $packages_result->num_rows > 0): ?>
<section class="packages-section">
    <div class="container">
        <div class="section-title">
            <h2>Choose Your <span>Package</span></h2>
            <p>Select the perfect package for your <?php echo htmlspecialchars($service['title']); ?> project</p>
        </div>
        <div class="packages-grid">
            <?php 
            $package_colors = ['#4CAF50', '#2196F3', '#9C27B0', '#FF9800', '#E91E63', '#00BCD4'];
            $color_index = 0;
            while($pkg = $packages_result->fetch_assoc()): 
                $features = explode('|', $pkg['features']);
                $color = $package_colors[$color_index % count($package_colors)];
                $color_index++;
            ?>
            <div class="package-card">
                <div class="package-header" style="background: linear-gradient(135deg, <?php echo $color; ?>, <?php echo $color; ?>cc);">
                    <h3><?php echo htmlspecialchars($pkg['package_name']); ?></h3>
                    <div class="package-price"><?php echo htmlspecialchars($pkg['package_price']); ?></div>
                </div>
                <div class="package-body">
                    <ul class="package-features">
                        <?php foreach($features as $feature): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="btn-select-package" onclick="openRegisterPopup('<?php echo htmlspecialchars($service['title']); ?>', '<?php echo htmlspecialchars($pkg['package_name']); ?>', '<?php echo htmlspecialchars($pkg['package_price']); ?>')">
                        Select Package <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Registration Popup Modal -->
<div id="registerModal" class="register-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Register for Service</h3>
            <span class="close-modal" onclick="closeRegisterPopup()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="selected-package-info" id="selectedPackageInfo">
                <h4 id="selectedService">Service Name</h4>
                <p id="selectedPackage">Package Name - Price</p>
            </div>
            
            <div class="contact-info-modal">
                <p><i class="fab fa-telegram"></i> Telegram: <strong>@genesis306</strong></p>
                <p><i class="fas fa-phone"></i> Phone: <strong>+251 920188600</strong></p>
                <p><i class="fas fa-envelope"></i> Email: <strong>dangraphics@gmail.com</strong></p>
            </div>
            
            <form id="registerForm" onsubmit="return false;">
                <input type="hidden" id="reg_service" name="service">
                <input type="hidden" id="reg_package_name" name="package_name">
                <input type="hidden" id="reg_package_price" name="package_price">
                
                <div class="form-group-modal">
                    <label><i class="fas fa-user"></i> Full Name *</label>
                    <input type="text" id="reg_fullname" placeholder="Enter your full name" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" id="reg_phone" placeholder="Your active phone number" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="reg_email" placeholder="Your email address (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fab fa-telegram"></i> Telegram Username</label>
                    <input type="text" id="reg_telegram" placeholder="@username (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-comment"></i> Additional Notes</label>
                    <textarea id="reg_notes" rows="3" placeholder="Any specific requirements or questions..."></textarea>
                </div>
                <div id="registerFeedback" class="register-feedback"></div>
                <button type="button" class="btn-submit-register" id="submitRegisterBtn" onclick="submitRegistration()">
                    <i class="fas fa-paper-plane"></i> Submit Registration
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
        setTimeout(() => toast.remove(), 500);
    }
}


// Auto-slider speed
const sliderTrack = document.getElementById('sliderTrack');
if (sliderTrack) {
    const itemCount = <?php echo count($items ?? []); ?>;
    const speed = Math.max(20, 50 - itemCount);
    sliderTrack.style.animation = `scroll ${speed}s linear infinite`;
}

// Registration Popup Functions
function openRegisterPopup(serviceName, packageName, packagePrice) {
    document.getElementById('selectedService').innerHTML = serviceName;
    document.getElementById('selectedPackage').innerHTML = packageName + ' - ' + packagePrice;
    document.getElementById('reg_service').value = serviceName;
    document.getElementById('reg_package_name').value = packageName;
    document.getElementById('reg_package_price').value = packagePrice;
    document.getElementById('registerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    const feedback = document.getElementById('registerFeedback');
    feedback.style.display = 'none';
    feedback.className = 'register-feedback';
}

function closeRegisterPopup() {
    document.getElementById('registerModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('registerForm').reset();
    const feedback = document.getElementById('registerFeedback');
    feedback.style.display = 'none';
    feedback.className = 'register-feedback';
}

window.onclick = function(event) {
    let modal = document.getElementById('registerModal');
    if (event.target == modal) {
        closeRegisterPopup();
    }
}

async function submitRegistration() {
    const service = document.getElementById('reg_service').value;
    const package_name = document.getElementById('reg_package_name').value;
    const package_price = document.getElementById('reg_package_price').value;
    const fullname = document.getElementById('reg_fullname').value.trim();
    const phone = document.getElementById('reg_phone').value.trim();
    const email = document.getElementById('reg_email').value.trim();
    const telegram = document.getElementById('reg_telegram').value.trim();
    const notes = document.getElementById('reg_notes').value.trim();
    const feedback = document.getElementById('registerFeedback');
    const submitBtn = document.getElementById('submitRegisterBtn');
    
    if (!fullname) {
        showFeedback(feedback, 'Please enter your full name', 'error');
        return;
    }
    
    if (!phone) {
        showFeedback(feedback, 'Please enter your phone number', 'error');
        return;
    }
    
    if (phone.length < 9) {
        showFeedback(feedback, 'Please enter a valid phone number', 'error');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        showFeedback(feedback, 'Please enter a valid email address', 'error');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    const formData = new URLSearchParams();
    formData.append('service', service);
    formData.append('package_name', package_name);
    formData.append('package_price', package_price);
    formData.append('fullname', fullname);
    formData.append('phone', phone);
    formData.append('email', email);
    formData.append('telegram', telegram);
    formData.append('notes', notes);
    
    try {
        const response = await fetch('submit_service_registration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessToast('🎉 Registration Submitted!', result.message);
            closeRegisterPopup();
        } else {
            showFeedback(feedback, result.message || 'Submission failed', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showFeedback(feedback, 'Network error. Please check your connection and try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Registration';
    }
}

function showFeedback(element, message, type) {
    element.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
    element.className = 'register-feedback ' + type;
    element.style.display = 'block';
}
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>