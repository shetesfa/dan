<?php
require_once 'config.php';

$services_query = "SELECT * FROM services WHERE status = 'active' ORDER BY display_order ASC, id DESC";
$services = $conn->query($services_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Our Services | Dan Creatives</title>
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

        .services-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            text-align: center;
            color: white;
        }

        .services-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .services-hero h1 span {
            color: #ffd700;
        }

        .services-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .services-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: #1a2a3a;
        }

        .section-header span {
            color: #ff6b6b;
        }

        .section-header p {
            color: #718096;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-align: center;
            padding: 40px 30px;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6b6b, #ff8e53);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .service-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1a2a3a;
        }

        .service-card p {
            color: #718096;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .service-features {
            list-style: none;
            margin: 20px 0;
            text-align: left;
        }

        .service-features li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4a5568;
        }

        .service-features i {
            color: #ff6b6b;
            font-size: 0.9rem;
        }

        .service-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #ff6b6b;
            margin: 20px 0;
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

        .social-links a {
            font-size: 1.3rem;
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
            
            .services-hero {
                padding: 50px 0;
            }
            
            .services-hero h1 {
                font-size: 1.8rem;
            }
            
            .services-hero p {
                font-size: 1rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }
            
            .section-header h2 {
                font-size: 1.8rem;
            }
            
            .service-card {
                padding: 30px 20px;
            }
            
            .service-card h3 {
                font-size: 1.3rem;
            }
            
            .service-icon {
                width: 70px;
                height: 70px;
            }
            
            .service-icon i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

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

<section class="services-hero">
    <div class="container">
        <h1>Our <span>Creative Services</span></h1>
        <p>Professional design services to elevate your brand and business</p>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <div class="section-header">
            <h2>What We <span>Offer</span></h2>
            <p>Click any service to see our previous work</p>
        </div>
        <div class="services-grid">
            <?php while($service = $services->fetch_assoc()): 
                $features = explode('|', $service['features']);
            ?>
            <div class="service-card" onclick="window.location.href='service-portfolio.php?service_id=<?php echo $service['id']; ?>'">
                <div class="service-icon">
                    <i class="<?php echo $service['icon_class']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
                <ul class="service-features">
                    <?php foreach($features as $feature): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="service-price"><?php echo $service['price']; ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

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
  <div       class="footer-bottom">
            <p>© 2026 Dan Creatives - All rights reserved | ዳን ክሬቲቭስ</p>
        </div>
    </div>
</footer>

<script>
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>