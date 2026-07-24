<?php
require_once 'config.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

$product_query = "SELECT * FROM products WHERE id = $product_id AND status = 'active'";
$product_result = $conn->query($product_query);
$product = $product_result->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Get portfolio items for this product
$portfolio_query = "SELECT * FROM product_templates 
                    WHERE product_id = $product_id AND status = 'active' 
                    ORDER BY display_order ASC, id ASC";
$portfolio_items = $conn->query($portfolio_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($product['title']); ?> Design Gallery | Dan Creatives</title>
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
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .portfolio-card {
            width: 350px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            flex-shrink: 0;
            cursor: pointer;
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

        /* CTA Section Below Gallery */
        .cta-section {
            padding: 60px 0 80px;
            background: linear-gradient(135deg, #1a2a3a 0%, #2d3e4e 100%);
            color: white;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .cta-section h2 span {
            color: #ff6b6b;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #ff6b6b;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .cta-btn:hover {
            background: #ff5252;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,107,107,0.4);
        }

        /* Order Modal */
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

        .product-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .product-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
        }

        .product-preview h4 { color: #1a2a3a; margin-bottom: 5px; }
        .product-preview p { font-size: 0.85rem; color: #718096; }

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

        .form-group-modal textarea { min-height: 70px; resize: vertical; }

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
            .modal-container { max-width: 95%; }
            .product-preview { flex-direction: column; text-align: center; }
            .toast-notification { top: 10px; right: 10px; left: 10px; }
            .toast-success {
                min-width: auto;
                width: calc(100% - 20px);
                max-width: none;
            }
            .cta-section h2 { font-size: 1.5rem; }
            .cta-btn { padding: 12px 30px; font-size: 1rem; }
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

<section class="portfolio-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($product['title']); ?> <span>Design Gallery</span></h1>
        <p>Browse our collection of design styles for <?php echo htmlspecialchars($product['title']); ?></p>
        <a href="products.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Products</a>
    </div>
</section>

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
                    // Duplicate items for seamless loop
                    $all_items = array_merge($items, $items);
                    foreach($all_items as $item): 
                    ?>
                    <div class="portfolio-card" onclick="openOrderModal(<?php echo $item['id']; ?>, '<?php echo addslashes($item['title']); ?>', '<?php echo addslashes($item['description']); ?>', '<?php echo addslashes($item['image_url']); ?>', <?php echo $product_id; ?>, '<?php echo addslashes($product['title']); ?>')">
                        <div class="portfolio-media">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
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
        <div class="no-portfolio" style="text-align: center; padding: 80px; background: white; border-radius: 20px;">
            <i class="fas fa-images" style="font-size: 64px; color: #ccc;"></i>
            <h3 style="margin-top: 20px;">No Design Gallery Items Yet</h3>
            <p>Check back soon for our latest designs!</p>
            <a href="products.php" class="back-link" style="background: #ff6b6b; margin-top: 20px; display: inline-block;">Back to Products</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section - Below all photos -->
<section class="cta-section">
    <div class="container">
        <h2>Want to order <span><?php echo htmlspecialchars($product['title']); ?></span> with custom design?</h2>
        <p>Click below and fill your information. We will contact you within 24 hours to discuss your order.</p>
        <button class="cta-btn" onclick="openInquiryModal()">
            <i class="fas fa-paper-plane"></i> Order Now
        </button>
    </div>
</section>

<!-- Order Modal (click on design) -->
<div id="orderModal" class="order-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-paint-brush"></i> Order This Design</h3>
            <span class="close-modal" onclick="closeOrderModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="product-preview" id="productPreview">
                <img id="previewMedia" src="" alt="Preview">
                <div>
                    <h4 id="previewTitle">Design Title</h4>
                    <p id="previewDescription">Design description</p>
                </div>
            </div>
            
            <div class="contact-info-modal">
                <p><i class="fab fa-telegram"></i> Telegram: <strong>@genesis306</strong></p>
                <p><i class="fas fa-phone"></i> Phone: <strong>+251 920188600</strong></p>
                <p><i class="fas fa-envelope"></i> Email: <strong>dangraphics@gmail.com</strong></p>
            </div>
            
            <form id="orderForm" onsubmit="return false;">
                <input type="hidden" id="template_id" name="template_id">
                <input type="hidden" id="product_id" name="product_id">
                <input type="hidden" id="product_name" name="product_name">
                <input type="hidden" id="template_title" name="template_title">
                <input type="hidden" id="template_image" name="template_image">
                
                <div class="form-group-modal">
                    <label><i class="fas fa-user"></i> Your Full Name *</label>
                    <input type="text" id="customer_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="customer_email" placeholder="We'll send confirmation here (optional)">
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
                    <label><i class="fas fa-hashtag"></i> Quantity *</label>
                    <select id="quantity" required>
                        <option value="1">1 piece</option>
                        <option value="2">2 pieces</option>
                        <option value="3">3 pieces</option>
                        <option value="4">4 pieces</option>
                        <option value="5">5 pieces</option>
                        <option value="10">10 pieces (Bulk)</option>
                        <option value="20">20+ pieces (Contact for bulk)</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-ruler"></i> Size</label>
                    <select id="size">
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <option value="One Size">One Size</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-palette"></i> Color</label>
                    <input type="text" id="color" placeholder="e.g., Black, White, Blue, Red (or specify)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-comment"></i> Additional Requirements</label>
                    <textarea id="requirements" placeholder="Any special instructions, custom text, or design details..."></textarea>
                </div>
                <div id="orderFeedback" class="order-feedback"></div>
                <button type="button" class="btn-submit-order" id="submitOrderBtn" onclick="submitOrder()">
                    <i class="fas fa-paper-plane"></i> Send Order
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Inquiry Modal (click on Order Now button) -->
<div id="inquiryModal" class="order-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-shopping-cart"></i> Order <?php echo htmlspecialchars($product['title']); ?></h3>
            <span class="close-modal" onclick="closeInquiryModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="contact-info-modal">
                <p><i class="fab fa-telegram"></i> Telegram: <strong>@genesis306</strong></p>
                <p><i class="fas fa-phone"></i> Phone: <strong>+251 920188600</strong></p>
                <p><i class="fas fa-envelope"></i> Email: <strong>dangraphics@gmail.com</strong></p>
            </div>
            
            <form id="inquiryForm" onsubmit="return false;">
                <input type="hidden" id="inquiry_product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" id="inquiry_product_name" value="<?php echo htmlspecialchars($product['title']); ?>">
                
                <div class="form-group-modal">
                    <label><i class="fas fa-user"></i> Your Full Name *</label>
                    <input type="text" id="inquiry_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="inquiry_email" placeholder="Your email address (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" id="inquiry_phone" placeholder="Your active phone number" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fab fa-telegram"></i> Telegram Username</label>
                    <input type="text" id="inquiry_telegram" placeholder="@username (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-hashtag"></i> Quantity *</label>
                    <select id="inquiry_quantity" required>
                        <option value="1">1 piece</option>
                        <option value="2">2 pieces</option>
                        <option value="3">3 pieces</option>
                        <option value="4">4 pieces</option>
                        <option value="5">5 pieces</option>
                        <option value="10">10 pieces (Bulk)</option>
                        <option value="20">20+ pieces (Contact for bulk)</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-ruler"></i> Size</label>
                    <select id="inquiry_size">
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <option value="One Size">One Size</option>
                    </select>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-palette"></i> Preferred Color</label>
                    <input type="text" id="inquiry_color" placeholder="e.g., Black, White, Blue">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-file-alt"></i> Design Requirements</label>
                    <textarea id="inquiry_details" placeholder="Tell us about your design ideas, text, or special requests..."></textarea>
                </div>
                <div id="inquiryFeedback" class="order-feedback"></div>
                <button type="button" class="btn-submit-order" id="submitInquiryBtn" onclick="submitInquiry()">
                    <i class="fas fa-paper-plane"></i> Submit Order
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
                <h4>Products</h4>
                <a href="products.php">T-Shirts</a>
                <a href="products.php">Hoodies</a>
                <a href="products.php">Mugs</a>
                <a href="products.php">Gift Boxes</a>
                <a href="products.php">Neon Lights</a>
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


// Auto-slider - adjust speed based on number of items
const sliderTrack = document.getElementById('sliderTrack');
if (sliderTrack) {
    const itemCount = <?php echo count($items ?? []); ?>;
    const speed = Math.max(20, 50 - itemCount);
    sliderTrack.style.animation = `scroll ${speed}s linear infinite`;
}

let currentTemplateId = null;

function openOrderModal(templateId, title, description, imageUrl, productId, productTitle) {
    currentTemplateId = templateId;
    
    document.getElementById('previewMedia').src = imageUrl;
    document.getElementById('previewTitle').innerHTML = title;
    document.getElementById('previewDescription').innerHTML = description.substring(0, 100);
    document.getElementById('template_id').value = templateId;
    document.getElementById('template_title').value = title;
    document.getElementById('template_image').value = imageUrl;
    document.getElementById('product_id').value = productId;
    document.getElementById('product_name').value = productTitle;
    
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
    currentTemplateId = null;
}

function openInquiryModal() {
    document.getElementById('inquiryModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    const feedback = document.getElementById('inquiryFeedback');
    feedback.style.display = 'none';
    feedback.className = 'order-feedback';
}

function closeInquiryModal() {
    document.getElementById('inquiryModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('inquiryForm').reset();
    const feedback = document.getElementById('inquiryFeedback');
    feedback.style.display = 'none';
    feedback.className = 'order-feedback';
}

window.onclick = function(event) {
    let modal1 = document.getElementById('orderModal');
    let modal2 = document.getElementById('inquiryModal');
    if (event.target == modal1) {
        closeOrderModal();
    }
    if (event.target == modal2) {
        closeInquiryModal();
    }
}

async function submitOrder() {
    const template_id = document.getElementById('template_id').value;
    const template_title = document.getElementById('template_title').value;
    const template_image = document.getElementById('template_image').value;
    const product_id = document.getElementById('product_id').value;
    const product_name = document.getElementById('product_name').value;
    const customer_name = document.getElementById('customer_name').value.trim();
    const customer_email = document.getElementById('customer_email').value.trim();
    const customer_phone = document.getElementById('customer_phone').value.trim();
    const customer_telegram = document.getElementById('customer_telegram').value.trim();
    const quantity = document.getElementById('quantity').value;
    const size = document.getElementById('size').value;
    const color = document.getElementById('color').value.trim();
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
    formData.append('template_id', template_id);
    formData.append('template_title', template_title);
    formData.append('template_image', template_image);
    formData.append('product_id', product_id);
    formData.append('product_name', product_name);
    formData.append('customer_name', customer_name);
    formData.append('customer_email', customer_email);
    formData.append('customer_phone', customer_phone);
    formData.append('customer_telegram', customer_telegram);
    formData.append('quantity', quantity);
    formData.append('size', size);
    formData.append('color', color);
    formData.append('requirements', requirements);
    
    try {
        const response = await fetch('submit_product_template_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessToast('🎉 Order Placed Successfully!', result.message);
            closeOrderModal();
        } else {
            showFeedback(feedback, result.message || 'Submission failed', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showFeedback(feedback, 'Network error. Please check your connection and try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Order';
    }
}

async function submitInquiry() {
    const product_id = document.getElementById('inquiry_product_id').value;
    const product_name = document.getElementById('inquiry_product_name').value;
    const customer_name = document.getElementById('inquiry_name').value.trim();
    const customer_email = document.getElementById('inquiry_email').value.trim();
    const customer_phone = document.getElementById('inquiry_phone').value.trim();
    const customer_telegram = document.getElementById('inquiry_telegram').value.trim();
    const quantity = document.getElementById('inquiry_quantity').value;
    const size = document.getElementById('inquiry_size').value;
    const color = document.getElementById('inquiry_color').value.trim();
    const details = document.getElementById('inquiry_details').value.trim();
    const feedback = document.getElementById('inquiryFeedback');
    const submitBtn = document.getElementById('submitInquiryBtn');
    
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
    formData.append('product_id', product_id);
    formData.append('product_name', product_name);
    formData.append('customer_name', customer_name);
    formData.append('customer_email', customer_email);
    formData.append('customer_phone', customer_phone);
    formData.append('customer_telegram', customer_telegram);
    formData.append('quantity', quantity);
    formData.append('size', size);
    formData.append('color', color);
    formData.append('requirements', details);
    
    try {
        const response = await fetch('submit_product_inquiry.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessToast('🎉 Order Submitted Successfully!', result.message);
            closeInquiryModal();
        } else {
            showFeedback(feedback, result.message || 'Submission failed', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showFeedback(feedback, 'Network error. Please check your connection and try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Order';
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