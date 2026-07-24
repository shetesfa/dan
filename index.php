<?php
require_once 'config.php';

$featured_courses_query = "SELECT * FROM courses WHERE status = 'active' LIMIT 3";
$featured_courses = $conn->query($featured_courses_query);

$stats_query = "SELECT stat_name, stat_value FROM site_stats";
$stats_result = $conn->query($stats_query);
$stats = [];
while($row = $stats_result->fetch_assoc()) {
    $stats[$row['stat_name']] = $row['stat_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dan Creatives | Graphics Design Studio</title>
    <link rel="icon" href="images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --maroon: #8b0426;
            --maroon-dark: #5c0219;
            --ink: #1a2a3a;
            --coral: #ff6b6b;
            --muted: #718096;
            --space-section: clamp(48px, 8vw, 96px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html { scroll-behavior: smooth; overflow-x: hidden; max-width: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1a2a3a;
            line-height: 1.5;
            overflow-x: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 clamp(18px, 4vw, 32px);
        }

        /* ---------- NAV ---------- */
        .navbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(139,4,38,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: box-shadow .3s ease, padding .3s ease;
        }
        .navbar.scrolled { box-shadow: 0 4px 24px rgba(139,4,38,0.14); }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px clamp(18px, 4vw, 32px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .logo-img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
        }

        .logo-text {
            font-size: clamp(1.25rem, 4vw, 1.6rem);
            font-weight: 800;
            color: #1a2a3a;
        }

        .logo-text span { color: var(--maroon); }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 36px;
        }

        .nav-menu a {
            position: relative;
            text-decoration: none;
            color: #4a5568;
            font-weight: 600;
            transition: 0.3s;
            padding-bottom: 4px;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            left: 0; bottom: -2px;
            width: 0; height: 2px;
            background: var(--maroon);
            transition: width .25s ease;
        }

        .nav-menu a:hover, .nav-menu a.active { color: var(--ink); }
        .nav-menu a:hover::after, .nav-menu a.active::after { width: 100%; }

        .hamburger {
            display: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: #231F20;
            width: 42px; height: 42px;
            align-items: center; justify-content: center;
            border-radius: 10px;
            transition: background .2s ease;
        }
        .hamburger:active { background: #f1f3f7; }

        /* ---------- HERO ---------- */
        .hero {
            padding: clamp(40px, 8vw, 80px) 0;
            background: radial-gradient(circle at 85% 15%, #fff0ed 0%, #fff9f0 45%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: clamp(28px, 6vw, 60px);
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff0ed;
            padding: 8px 18px;
            border-radius: 50px;
            color: #8a0404ad;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .hero-content h1 {
            font-size: clamp(2rem, 5.4vw, 3.5rem);
            font-weight: 800;
            line-height: 1.18;
            margin-bottom: 20px;
            color: #0c0000;
            letter-spacing: -0.5px;
        }

        .hero-content h1 span {
            color: var(--maroon);
            position: relative;
        }

        .hero-content p {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            color: #718096;
            margin-bottom: 28px;
            line-height: 1.6;
            max-width: 46ch;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 40px;
        }

        .btn-primary, .btn-outline {
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--maroon);
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: var(--maroon-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(139,4,38,0.25);
        }
        .btn-primary:active { transform: translateY(0) scale(0.98); }

        .btn-outline {
            border: 2px solid #231F20;
            color: #231F20;
            padding: 12px 26px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.25s ease;
            background: transparent;
        }

        .btn-outline:hover { background: #231F20; color: white; transform: translateY(-2px); }
        .btn-outline:active { transform: translateY(0) scale(0.98); }

        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: clamp(24px, 5vw, 48px);
        }

        .stat { display: flex; flex-direction: column; }

        .stat-number {
            font-size: clamp(1.4rem, 3.2vw, 1.8rem);
            font-weight: 800;
            color: #1a2a3a;
        }

        .stat-label { font-size: 0.85rem; color: #718096; }

        .hero-image { perspective: 1200px; }

        .hero-image img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 28px;
            box-shadow: 0 24px 50px rgba(139,4,38,0.16);
            transition: transform .4s ease, box-shadow .4s ease;
            will-change: transform;
        }

        /* ---------- SECTION HEADERS ---------- */
        .section-header {
            text-align: center;
            margin-bottom: clamp(32px, 6vw, 56px);
        }

        .section-header h2 {
            font-size: clamp(1.6rem, 4.4vw, 2.5rem);
            margin-bottom: 10px;
            color: #1a2a3a;
        }

        .section-header span { color: var(--maroon); }
        .section-header p { color: #718096; font-size: clamp(0.9rem, 2vw, 1rem); }

        /* ---------- FEATURED COURSES ---------- */
        .featured { padding: var(--space-section) 0; background: white; }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: clamp(18px, 3vw, 32px);
        }

        .course-card, .service-card, .testimonial-card {
            transform-style: preserve-3d;
        }

        .course-card {
            background: white;
            padding: clamp(28px, 4vw, 40px) clamp(20px, 3vw, 32px);
            border-radius: 24px;
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
            border: 1px solid #eef2f6;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }

        .course-card:hover {
            box-shadow: 0 24px 44px rgba(139,4,38,0.1);
            border-color: #231F20;
        }

        .course-icon { font-size: clamp(2.2rem, 4vw, 3rem); color: #231F20; margin-bottom: 18px; }
        .course-card h3 { margin-bottom: 14px; color: #1a2a3a; font-size: clamp(1.05rem, 2.2vw, 1.25rem); }
        .course-card p { color: #718096; margin-bottom: 22px; font-size: 0.92rem; }
        .card-link { color: #231F20; text-decoration: none; font-weight: 600; }

        /* ---------- SERVICES ---------- */
        .services { padding: var(--space-section) 0; background: #fafbfe; }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: clamp(18px, 3vw, 32px);
        }

        .service-card {
            text-align: center;
            padding: clamp(28px, 4vw, 40px) 22px;
            background: white;
            border-radius: 24px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
            border: 1px solid #eef2f6;
        }

        .service-card:hover { box-shadow: 0 18px 38px rgba(255,107,107,0.14); border-color: var(--coral); }
        .service-card i { font-size: clamp(2rem, 3.6vw, 2.5rem); color: #231F20; margin-bottom: 18px; }
        .service-card h3 { margin-bottom: 10px; color: #1a2a3a; font-size: clamp(1rem, 2vw, 1.15rem); }
        .service-card p { color: #718096; font-size: 0.9rem; }

        /* ---------- TESTIMONIALS ---------- */
        .testimonials { padding: var(--space-section) 0; background: white; }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: clamp(18px, 3vw, 32px);
        }

        .testimonial-card {
            background: #fafbfe;
            padding: clamp(24px, 3vw, 32px);
            border-radius: 24px;
            border: 1px solid #eef2f6;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .testimonial-card:hover { box-shadow: 0 16px 32px rgba(0,0,0,0.06); }

        .testimonial-card i { font-size: 1.7rem; color: #231F20; margin-bottom: 16px; opacity: 0.5; }
        .testimonial-card p { color: #4a5568; margin-bottom: 18px; line-height: 1.6; font-size: 0.94rem; }
        .testimonial-card h4 { color: #1a2a3a; }

        /* ---------- CTA ---------- */
        .cta {
            padding: var(--space-section) 0;
            background: linear-gradient(135deg, #8c77d8, #411bb6);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-container { text-align: center; position: relative; z-index: 1; }
        .cta-content h2 { font-size: clamp(1.5rem, 4vw, 2.5rem); margin-bottom: 14px; }
        .cta-content p { margin-bottom: 28px; font-size: clamp(0.95rem, 2vw, 1.1rem); opacity: 0.9; }

        .btn-cta {
            background: linear-gradient(135deg, #e72214, #411bb6);
            color: #f6f5fa;
            padding: 14px 38px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-cta:hover { transform: scale(1.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }

        /* ---------- FOOTER ---------- */
        .footer { background: #1a2a3a; color: white; padding: 50px 0 20px; }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: clamp(24px, 4vw, 40px);
            margin-bottom: 36px;
        }

        .footer-section h4 { margin-bottom: 18px; color: #ff8e53; font-size: 1.02rem; }
        .footer-section a { display: block; color: #cbd5e0; text-decoration: none; margin-bottom: 12px; transition: 0.2s; font-size: 0.92rem; }
        .footer-section a:hover { color: var(--coral); padding-left: 3px; }

        .social-links { display: flex; gap: 16px; margin-top: 18px; }
        .social-links a {
            font-size: 1.1rem;
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            transition: .25s;
        }
        .social-links a:hover { background: var(--maroon); transform: translateY(-3px); }

        .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.85rem; }

        /* ---------- SCROLL REVEAL ---------- */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.in-view { opacity: 1; transform: translateY(0); }
        .reveal-stagger.in-view > * { animation: fadeUp .6s ease both; }
        .reveal-stagger.in-view > *:nth-child(1) { animation-delay: .05s; }
        .reveal-stagger.in-view > *:nth-child(2) { animation-delay: .15s; }
        .reveal-stagger.in-view > *:nth-child(3) { animation-delay: .25s; }
        .reveal-stagger.in-view > *:nth-child(4) { animation-delay: .35s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }

        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-stagger > * { animation: none !important; transition: none !important; opacity: 1 !important; transform: none !important; }
            html { scroll-behavior: auto; }
        }

        /* ---------- TABLET / MOBILE ---------- */
        @media (max-width: 900px) {
            .hero-container { grid-template-columns: 1fr; }
            .hero-image { order: -1; max-width: 420px; margin: 0 auto; }
            .hero-image img { max-height: 280px; }
            .hero-content p { max-width: none; }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 64px;
                left: 0; right: 0; bottom: 0;
                background: white;
                padding: 28px;
                gap: 6px;
                text-align: left;
                transform: translateX(100%);
                transition: transform .3s ease;
                overflow-y: auto;
            }
            .nav-menu.active { transform: translateX(0); }
            .nav-menu li { border-bottom: 1px solid #f1f3f7; }
            .nav-menu a { display: block; padding: 14px 4px; font-size: 1.05rem; }
            .nav-menu a::after { display: none; }

            .hamburger { display: flex; }

            .nav-backdrop {
                display: none;
                position: fixed; inset: 0;
                background: rgba(10,10,15,0.4);
                z-index: 999;
            }
            .nav-backdrop.active { display: block; }

            .hero { padding: 32px 0 40px; }
            .hero-buttons { flex-direction: column; }
            .hero-buttons a { text-align: center; width: 100%; }
            .hero-stats { justify-content: space-between; gap: 12px; }

            .course-card, .service-card { padding: 26px 20px; }
        }

        @media (max-width: 480px) {
            .hero-badge { font-size: 0.78rem; padding: 7px 14px; }
            .stat-number { font-size: 1.3rem; }
            .stat-label { font-size: 0.78rem; }
            .courses-grid, .services-grid, .testimonials-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }

    </style>
</head>
<body>

<nav class="navbar">
    <div class="container nav-container">
        <div class="logo" id="adminLogo">
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
<div class="nav-backdrop" id="navBackdrop"></div>

<section class="hero">
    <div class="container hero-container">
        <div class="hero-content reveal">
            <div class="hero-badge">
                <i class="fas fa-paint-brush"></i> dan creatives page
            </div>
            <h1>Master <span>Graphics Design</span> Like a Pro</h1>
            <p>Empower the next generation of Ethiopian designers with Dan Creatives: Master Photoshop, Illustrator, and all the essential graphic design skills you need for a successful creative career.</p>
            <div class="hero-buttons">
                <a href="services.php" class="btn-primary">Our Services →</a>
                <a href="products.php" class="btn-outline">Our Products</a>
                <a href="courses.php" class="btn-primary">Our Courses</a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number" data-count="<?php echo isset($stats['students']) ? preg_replace('/[^0-9]/', '', $stats['students']) : '10000'; ?>" data-suffix="<?php echo isset($stats['students']) ? preg_replace('/[0-9]/', '', $stats['students']) : '+'; ?>">0</span>
                    <span class="stat-label">Students</span>
                </div>
                <div class="stat">
                    <span class="stat-number" data-count="<?php echo isset($stats['projects']) ? preg_replace('/[^0-9]/', '', $stats['projects']) : '500'; ?>" data-suffix="<?php echo isset($stats['projects']) ? preg_replace('/[0-9]/', '', $stats['projects']) : '+'; ?>">0</span>
                    <span class="stat-label">Projects</span>
                </div>
                <div class="stat">
                    <span class="stat-number" data-count="<?php echo isset($stats['mentors']) ? preg_replace('/[^0-9]/', '', $stats['mentors']) : '50'; ?>" data-suffix="<?php echo isset($stats['mentors']) ? preg_replace('/[0-9]/', '', $stats['mentors']) : '+'; ?>">0</span>
                    <span class="stat-label">Expert Mentors</span>
                </div>
            </div>
        </div>
        <div class="hero-image reveal" id="tiltImage">
            <img src="images/logo.jpg" alt="Graphics Design">
        </div>
    </div>
</section>

<section class="featured">
    <div class="container">
        <div class="section-header reveal">
            <h2>Popular <span>Courses</span></h2>
            <p>Choose your path to creative mastery</p>
        </div>
        <div class="courses-grid reveal-stagger reveal">
            <?php while($course = $featured_courses->fetch_assoc()): ?>
            <div class="course-card">
                <div class="course-icon"><i class="<?php echo $course['icon_class']; ?>"></i></div>
                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                <p><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                <a href="courses.php" class="card-link">Learn More →</a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="services">
    <div class="container">
        <div class="section-header reveal">
            <h2>What We <span>Offer</span></h2>
            <p>Comprehensive design services for all your needs</p>
        </div>
        <div class="services-grid reveal-stagger reveal">
            <div class="service-card">
                <i class="fas fa-film"></i>
                <h3>Motion Graphics</h3>
                <p>Dynamic animations and visual effects</p>
            </div>
            <div class="service-card">
                <i class="fas fa-pen-fancy"></i>
                <h3>Logo Design</h3>
                <p>Unique brand identity and logo creation</p>
            </div>
            <div class="service-card">
                <i class="fas fa-image"></i>
                <h3>Photo Editing</h3>
                <p>Professional image retouching and manipulation</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <div class="section-header reveal">
            <h2>What <span>Students Say</span></h2>
            <p>Join thousands of successful creators</p>
        </div>
        <div class="testimonials-grid reveal-stagger reveal">
            <div class="testimonial-card">
                <i class="fas fa-quote-left"></i>
                <p>Dan's course transformed my design skills! Now I create professional graphics that clients love.</p>
                <h4>- Sara</h4>
            </div>
            <div class="testimonial-card">
                <i class="fas fa-quote-left"></i>
                <p>The Upwork bonus session alone paid for the course 10x. Best investment ever!</p>
                <h4>- Alex </h4>
            </div>
            <div class="testimonial-card">
                <i class="fas fa-quote-left"></i>
                <p>From zero to pro — Dan makes complex design simple and fun to learn.</p>
                <h4>- Michael</h4>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container cta-container">
        <div class="cta-content reveal">
            <h2>Ready to Start Your Creative Journey?</h2>
            <p>Join Dan Creatives today and learn from industry experts</p>
            <a href="register.php" class="btn-cta">Register Now →</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Dan Creatives</h4>
                <p>Empowering the next generation of digital creators with professional design education.</p>
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
                <a href="about.php">About</a>
                <a href="register.php">Register</a>
            </div>
            <div class="footer-section">
                <h4>Courses</h4>
                <a href="courses.php">Graphics Design</a>
                <a href="courses.php">Thumbnail Design</a>
                <a href="courses.php">Content Creator</a>
                <a href="courses.php">Upwork Freelancing</a>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="#">Imprint</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">Data Protection</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Dan Creatives - All rights reserved | ዳን ክሬቲቭስ</p>
        </div>
    </div>
</footer>

<script>
// ---- Mobile nav with backdrop + icon swap ----
const hamburger = document.getElementById('hamburgerBtn');
const navMenu = document.querySelector('.nav-menu');
const navBackdrop = document.getElementById('navBackdrop');

function closeMenu() {
    navMenu.classList.remove('active');
    navBackdrop.classList.remove('active');
    hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.style.overflow = '';
}
function toggleMenu() {
    const open = navMenu.classList.toggle('active');
    navBackdrop.classList.toggle('active', open);
    hamburger.innerHTML = open ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    document.body.style.overflow = open ? 'hidden' : '';
}
if (hamburger) {
    hamburger.addEventListener('click', toggleMenu);
    navBackdrop.addEventListener('click', closeMenu);
    navMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
}

// ---- Sticky nav shadow on scroll ----
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
}, { passive: true });

// ---- Scroll reveal ----
const revealEls = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    revealEls.forEach(el => io.observe(el));
} else {
    revealEls.forEach(el => el.classList.add('in-view'));
}

// ---- Animated stat counters ----
const counters = document.querySelectorAll('.stat-number[data-count]');
function animateCounter(el) {
    const target = parseInt(el.dataset.count, 10) || 0;
    const suffix = el.dataset.suffix || '';
    const duration = 1200;
    const start = performance.now();
    function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased).toLocaleString() + suffix;
        if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}
if (counters.length) {
    if ('IntersectionObserver' in window) {
        const counterIo = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) { animateCounter(entry.target); counterIo.unobserve(entry.target); }
            });
        }, { threshold: 0.5 });
        counters.forEach(c => counterIo.observe(c));
    } else {
        counters.forEach(animateCounter);
    }
}

// ---- Subtle 3D tilt on hero image (pointer devices only) ----
const tiltImage = document.getElementById('tiltImage');
if (tiltImage && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    const img = tiltImage.querySelector('img');
    tiltImage.addEventListener('mousemove', (e) => {
        const rect = tiltImage.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        img.style.transform = `rotateY(${x * 10}deg) rotateX(${-y * 10}deg) scale(1.03)`;
    });
    tiltImage.addEventListener('mouseleave', () => {
        img.style.transform = 'rotateY(0) rotateX(0) scale(1)';
    });
}

// ---- Card tilt on hover (course/service cards) ----
document.querySelectorAll('.course-card, .service-card').forEach(card => {
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        card.style.transform = `perspective(800px) rotateY(${x * 6}deg) rotateX(${-y * 6}deg) translateY(-5px)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});

const adminLogo = document.getElementById('adminLogo');

if (adminLogo) {
    adminLogo.addEventListener('click', function(event) {
        if (event.altKey && event.shiftKey) {
            event.preventDefault();
            window.location.href = 'admin_login_handler.php';
            console.log('🔐 Admin access granted - Redirecting to admin panel');
        } else {
            event.preventDefault();
            window.location.reload();
            console.log('🔄 Page reloaded');
        }
    });

    adminLogo.style.cursor = 'pointer';
}
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
</body>
</html>