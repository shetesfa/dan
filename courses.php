<?php
require_once 'config.php';

$courses_query = "SELECT * FROM courses ORDER BY id DESC";
$courses = $conn->query($courses_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | Dan Creatives</title>
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
            color: #231F20;
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #231F20;
        }

        .courses-page {
            padding: 80px 0;
            background: #fafbfe;
        }

        .page-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 12px;
            color: #1a2a3a;
        }

        .page-header span {
            color: #231F20;
        }

        .page-header p {
            color: #718096;
        }

        .course-detail {
            background: white;
            border-radius: 32px;
            margin-bottom: 40px;
            overflow: hidden;
            border: 1px solid #eef2f6;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }

        .course-header {
            background: linear-gradient(135deg, #1a2a3a 0%, #2d3e4e 100%);
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-badge {
            background: #231F20;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .course-badge.coming-soon {
            background: #231F20;
        }

        .course-content {
            padding: 32px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        .course-description {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .course-contents {
            list-style: none;
            margin-top: 20px;
        }

        .course-contents li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #4a5568;
        }

        .course-contents i {
            color: #231F20;
        }

        .course-info {
            background: #fafbfe;
            padding: 24px;
            border-radius: 20px;
            text-align: center;
        }

        .price {
            font-size: 1.8rem;
            font-weight: 800;
            color: #231F20;
            margin-bottom: 16px;
        }

        .date, .duration {
            color: #718096;
            margin-bottom: 12px;
        }

        .btn-enroll {
            display: inline-block;
            background: #231F20;
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-enroll:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .btn-enroll.disabled {
            background: #cbd5e0;
            cursor: not-allowed;
        }

        .btn-enroll.disabled:hover {
            transform: none;
        }

        .question-btn-container {
            text-align: center;
            margin: 40px 0 20px;
        }

        .btn-question {
            background: linear-gradient(135deg, #ab3a3a, #1ea706);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 5px 20px rgba(255,107,107,0.3);
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .btn-question:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255,107,107,0.4);
        }

        .question-modal {
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
        }

        .modal-container {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 24px;
            overflow: hidden;
            animation: slideUp 0.3s ease;
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
        }

        .modal-header h3 {
            font-size: 1.3rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header h3 i {
            color: #ff6b6b;
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
        }

        .close-modal:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 25px;
        }

        .form-group-modal {
            margin-bottom: 18px;
        }

        .form-group-modal label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a2a3a;
        }

        .form-group-modal input,
        .form-group-modal textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            transition: 0.3s;
        }

        .form-group-modal input:focus,
        .form-group-modal textarea:focus {
            outline: none;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255,107,107,0.1);
        }

        .form-group-modal textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-submit-question {
            width: 100%;
            background: #ff6b6b;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-question:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .modal-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            text-align: center;
            font-size: 0.8rem;
            color: #718096;
            border-top: 1px solid #eef2f6;
        }

        .modal-footer i {
            color: #ff6b6b;
        }

        .question-feedback {
            margin-top: 15px;
            padding: 12px;
            border-radius: 8px;
            display: none;
        }

        .question-feedback.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }

        .question-feedback.error {
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
            
            .course-content {
                grid-template-columns: 1fr;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .modal-container {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header h3 {
                font-size: 1.1rem;
            }
            
            .btn-question {
                padding: 12px 30px;
                font-size: 1rem;
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

<section class="courses-page">
    <div class="container">
        <div class="page-header">
            <h1>Our <span>Courses</span></h1>
            <p>Choose the perfect course to start your creative journey</p>
        </div>

        <div class="courses-list">
            <?php while($course = $courses->fetch_assoc()): ?>
            <div class="course-detail" id="course-<?php echo $course['id']; ?>">
                <div class="course-header">
                    <h2><i class="<?php echo $course['icon_class']; ?>"></i> <?php echo htmlspecialchars($course['title']); ?></h2>
                    <div class="course-badge <?php echo $course['status'] == 'coming_soon' ? 'coming-soon' : ''; ?>">
                        <?php echo $course['status'] == 'coming_soon' ? 'Coming Soon' : ($course['badge_text'] ?: 'Popular'); ?>
                    </div>
                </div>
                <div class="course-content">
                    <div>
                        <div class="course-description">
                            <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                        </div>
                        <ul class="course-contents">
                            <li><i class="fas fa-check-circle"></i> Complete training with hands-on projects</li>
                            <li><i class="fas fa-check-circle"></i> Certificate of completion</li>
                            <li><i class="fas fa-check-circle"></i> Lifetime access to materials</li>
                            <li><i class="fas fa-check-circle"></i> 24/7 Support from instructors</li>
                        </ul>
                    </div>
                    <div class="course-info">
                        <div class="price"><?php echo htmlspecialchars($course['price']); ?></div>
                        <div class="date"><i class="far fa-calendar"></i> Start: <?php echo htmlspecialchars($course['start_date']); ?></div>
                        <div class="duration"><i class="far fa-clock"></i> Duration: <?php echo htmlspecialchars($course['duration']); ?></div>
                        <?php if($course['status'] == 'active'): ?>
                            <a href="register.php?course=<?php echo urlencode($course['title']); ?>" class="btn-enroll">Enroll Now →</a>
                        <?php else: ?>
                            <button class="btn-enroll disabled" disabled>Coming Soon</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="question-btn-container">
            <button class="btn-question" onclick="openQuestionModal()">
                <i class="fas fa-question-circle"></i> Have a Question? Ask the Instructor
            </button>
        </div>
    </div>
</section>

<div id="questionModal" class="question-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-envelope"></i> Ask Your Question</h3>
            <span class="close-modal" onclick="closeQuestionModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="questionForm">
                <div class="form-group-modal">
                    <label><i class="fas fa-user"></i> Your Name *</label>
                    <input type="text" id="q_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="q_email" placeholder="We'll send answer here (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fab fa-telegram"></i> Telegram Username</label>
                    <input type="text" id="q_telegram" placeholder="@username or phone number (optional)">
                </div>
                <div class="form-group-modal">
                    <label><i class="fas fa-question"></i> Your Question *</label>
                    <textarea id="q_question" placeholder="Type your question here..." required></textarea>
                </div>
                <div id="questionFeedback" class="question-feedback"></div>
                <button type="submit" class="btn-submit-question" id="submitQuestionBtn">
                    <i class="fas fa-paper-plane"></i> Send Question
                </button>
            </form>
        </div>
        <div class="modal-footer">
            <i class="fas fa-clock"></i> We'll answer within 24 hours via Telegram or Email
        </div>
    </div>
</div>

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

function openQuestionModal() {
    document.getElementById('questionModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQuestionModal() {
    document.getElementById('questionModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('questionForm').reset();
    document.getElementById('questionFeedback').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('questionModal');
    if (event.target == modal) {
        closeQuestionModal();
    }
}

document.getElementById('questionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const name = document.getElementById('q_name').value.trim();
    const email = document.getElementById('q_email').value.trim();
    const telegram = document.getElementById('q_telegram').value.trim();
    const question = document.getElementById('q_question').value.trim();
    const feedbackDiv = document.getElementById('questionFeedback');
    const submitBtn = document.getElementById('submitQuestionBtn');
    
    if (!name || !question) {
        feedbackDiv.innerHTML = 'Please fill in your name and question';
        feedbackDiv.className = 'question-feedback error';
        return;
    }
    
    if (!telegram && !email) {
        feedbackDiv.innerHTML = 'Please provide either Email OR Telegram username for us to answer you';
        feedbackDiv.className = 'question-feedback error';
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    try {
        const response = await fetch('submit_question.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&telegram=${encodeURIComponent(telegram)}&question=${encodeURIComponent(question)}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            feedbackDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + result.message;
            feedbackDiv.className = 'question-feedback success';
            
            setTimeout(() => {
                closeQuestionModal();
                feedbackDiv.style.display = 'none';
            }, 2000);
        } else {
            feedbackDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + result.message;
            feedbackDiv.className = 'question-feedback error';
        }
    } catch (error) {
        feedbackDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Network error. Please try again.';
        feedbackDiv.className = 'question-feedback error';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Question';
    }
});
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>