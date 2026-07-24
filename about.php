<?php
require_once 'config.php';

$about_query = "SELECT * FROM about_content WHERE id = 1";
$about = $conn->query($about_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Dan Creatives</title>
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
            color: #231F20;
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
            color: #231F20;
        }

        .about-hero {
            padding: 80px 0;
            background: linear-gradient(135deg, #fff9f0 0%, #ffffff 100%);
        }

        .about-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .youtube-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e03a15;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .about-content h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #1a2a3a;
        }

        .about-content h1 span {
            color: #231F20;
        }

        .about-content p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .youtube-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: red;
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .youtube-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,0,0,0.3);
        }

        .channel-stats {
            display: flex;
            gap: 24px;
        }

        .channel-stats span {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #718096;
        }

        .youtube-embed {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
        }

        .youtube-embed img {
            width: 100%;
            border-radius: 24px;
        }

        .youtube-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
            border-radius: 24px;
        }

        .youtube-embed:hover .youtube-overlay {
            opacity: 1;
        }

        .youtube-overlay a {
            background: #231F20;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .featured-videos {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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
            color: #231F20;
        }

        .section-header p {
            color: #718096;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .video-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            overflow: hidden;
            background: #000;
        }

        .video-thumbnail img {
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .video-card:hover .video-thumbnail img {
            transform: scale(1.05);
        }

        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: #231F20;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 0.9;
        }

        .video-card:hover .play-overlay {
            transform: translate(-50%, -50%) scale(1.1);
            background: #ff0000;
        }

        .play-overlay i {
            color: white;
            font-size: 24px;
            margin-left: 4px;
        }

        .video-info {
            padding: 20px;
        }

        .video-info h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.4;
        }

        .video-meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #666;
            margin-top: 10px;
        }

        .video-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .mission {
            padding: 80px 0;
            background: white;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .mission-card {
            text-align: center;
            padding: 40px 24px;
            background: #fafbfe;
            border-radius: 24px;
            border: 1px solid #eef2f6;
            transition: 0.3s;
        }

        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-color: #ff6b6b;
        }

        .mission-card i {
            font-size: 2.5rem;
            color: #231F20;
            margin-bottom: 20px;
        }

        .mission-card h3 {
            margin-bottom: 16px;
            color: #1a2a3a;
        }

        .mission-card p {
            color: #718096;
        }

        .team {
            padding: 80px 0;
            background: white;
        }

        .team-grid {
            max-width: 600px;
            margin: 0 auto;
        }

        .team-card {
            display: flex;
            gap: 32px;
            background: #fafbfe;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #eef2f6;
            padding: 32px;
            transition: 0.3s;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-color: #ff6b6b;
        }

        .team-image img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .team-info h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: #1a2a3a;
        }

        .team-info p {
            color: #231F20;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .team-bio {
            color: #718096;
            font-weight: normal;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .team-social {
            display: flex;
            gap: 16px;
        }

        .team-social a {
            color: #4a5568;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .team-social a:hover {
            color: #ff6b6b;
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
            color: #231F20;
        }

        .footer-section a {
            display: block;
            color: #cbd5e0;
            text-decoration: none;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .footer-section a:hover {
            color: #231F20;
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
            }
            
            .about-container {
                grid-template-columns: 1fr;
            }
            
            .about-content h1 {
                font-size: 2rem;
            }
            
            .team-card {
                flex-direction: column;
                text-align: center;
            }
            
            .team-image img {
                margin: 0 auto;
            }
            
            .team-social {
                justify-content: center;
            }
            
            .videos-grid {
                grid-template-columns: 1fr;
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
            <li><a href="services.php">Services</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<section class="about-hero">
    <div class="container about-container">
        <div class="about-content">
            <div class="youtube-badge">
                <i class="fab fa-youtube"></i> <?php echo $about['youtube_badge_text'] ?? 'YouTube Channel'; ?>
            </div>
            <h1>About <span>Dan Creatives</span></h1>
            <p>Dan Creatives (ዳን ክሬቲቭስ) is a premier graphics design education platform dedicated to helping aspiring creators master the art of visual storytelling.</p>
            <div class="channel-info">
                <a href="<?php echo $about['youtube_channel_url']; ?>" target="_blank" class="youtube-link">
                    <i class="fab fa-youtube"></i> Subscribe on YouTube
                </a>
                <div class="channel-stats">
                    <span><i class="fas fa-users"></i> 649+ Subscribers</span>
                    <span><i class="fas fa-video"></i> 21+ Videos</span>
                </div>
            </div>
        </div>
        <div class="about-image">
            <div class="youtube-embed">
                <img src="<?php echo $about['youtube_thumbnail']; ?>" alt="Dan Creatives">
                <div class="youtube-overlay">
                    <a href="<?php echo $about['youtube_channel_url']; ?>" target="_blank">
                        <i class="fab fa-youtube"></i>
                        <span>Visit Channel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="featured-videos">
    <div class="container">
        <div class="section-header">
            <h2>Featured <span>Tutorials</span></h2>
            <p>Watch our most popular graphics design tutorials on YouTube</p>
        </div>
        <div class="videos-grid">
            <div class="video-card" onclick="window.open('<?php echo $about['youtube_video_1_url']; ?>', '_blank')">
                <div class="video-thumbnail">
                    <img src="<?php echo $about['youtube_video_1_thumbnail']; ?>" alt="Video Thumbnail">
                    <div class="play-overlay">
                        <i class="fab fa-youtube"></i>
                    </div>
                </div>
                <div class="video-info">
                    <h3><?php echo $about['youtube_video_1_title']; ?></h3>
                    <div class="video-meta">
                        <span><i class="fab fa-youtube"></i> YouTube</span>
                        <span><i class="fas fa-eye"></i> <?php echo $about['youtube_video_1_views']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="video-card" onclick="window.open('<?php echo $about['youtube_video_2_url']; ?>', '_blank')">
                <div class="video-thumbnail">
                    <img src="<?php echo $about['youtube_video_2_thumbnail']; ?>" alt="Video Thumbnail">
                    <div class="play-overlay">
                        <i class="fab fa-youtube"></i>
                    </div>
                </div>
                <div class="video-info">
                    <h3><?php echo $about['youtube_video_2_title']; ?></h3>
                    <div class="video-meta">
                        <span><i class="fab fa-youtube"></i> YouTube</span>
                        <span><i class="fas fa-eye"></i> <?php echo $about['youtube_video_2_views']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="video-card" onclick="window.open('<?php echo $about['youtube_video_3_url']; ?>', '_blank')">
                <div class="video-thumbnail">
                    <img src="<?php echo $about['youtube_video_3_thumbnail']; ?>" alt="Video Thumbnail">
                    <div class="play-overlay">
                        <i class="fab fa-youtube"></i>
                    </div>
                </div>
                <div class="video-info">
                    <h3><?php echo $about['youtube_video_3_title']; ?></h3>
                    <div class="video-meta">
                        <span><i class="fab fa-youtube"></i> YouTube</span>
                        <span><i class="fas fa-eye"></i> <?php echo $about['youtube_video_3_views']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mission">
    <div class="container">
        <div class="mission-grid">
            <div class="mission-card">
                <i class="fas fa-bullseye"></i>
                <h3>Our Mission</h3>
                <p>To provide accessible, high-quality graphics design education that empowers creators worldwide to turn their passion into profession.</p>
            </div>
            <div class="mission-card">
                <i class="fas fa-eye"></i>
                <h3>Our Vision</h3>
                <p>To become Ethiopia's leading creative education platform, nurturing the next generation of digital artists and designers.</p>
            </div>
            <div class="mission-card">
                <i class="fas fa-heart"></i>
                <h3>Our Values</h3>
                <p>Excellence, creativity, community, and continuous learning - shaping the future of design education.</p>
            </div>
        </div>
    </div>
</section>

<section class="team">
    <div class="container">
        <div class="section-header">
            <h2>Meet Our <span>Instructor</span></h2>
            <p>The creative mind behind Dan Creatives</p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-image">
                    <img src="images/dani.png">
                </div>
                <div class="team-info">
                    <!-- CHANGED: Instructor name updated from "Dan" to "Daniel Asrat" -->
                    <h3>Daniel Asrat</h3>
                    <p><?php echo $about['instructor_title']; ?></p>
                    <p class="team-bio"><?php echo $about['instructor_bio']; ?></p>
                    <div class="team-social">
                        <a href="<?php echo $about['youtube_channel_url']; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="https://t.me/genesis306"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
            </div>
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
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>