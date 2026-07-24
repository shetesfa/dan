<?php
require_once 'config.php';

$selected_course = isset($_GET['course']) ? $_GET['course'] : '';
$success_message = '';
$error_message = '';

$upload_dir = 'uploads/payment_receipts/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name'] ?? '');
    $last_name = $conn->real_escape_string($_POST['last_name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $course = $conn->real_escape_string($_POST['course'] ?? '');
    
    $payment_receipt_path = '';
    $upload_errors = [];
    
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] == 0) {
        $receipt = $_FILES['payment_receipt'];
        $receipt_ext = strtolower(pathinfo($receipt['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        
        if (in_array($receipt_ext, $allowed_ext)) {
            $receipt_new_name = time() . '_' . rand(1000, 9999) . '.' . $receipt_ext;
            $receipt_upload_path = $upload_dir . $receipt_new_name;
            
            if (move_uploaded_file($receipt['tmp_name'], $receipt_upload_path)) {
                $payment_receipt_path = $receipt_upload_path;
            } else {
                $upload_errors[] = "Failed to upload payment receipt.";
            }
        } else {
            $upload_errors[] = "Payment receipt must be JPG, PNG, GIF, WEBP, or PDF.";
        }
    }
    
    if ($first_name && $last_name && $phone && $course) {
        $registered_date = getEthiopiaTime();
        $insert = "INSERT INTO registrations (first_name, last_name, phone, email, course, payment_receipt, registered_date, status) 
                   VALUES ('$first_name', '$last_name', '$phone', '$email', '$course', '$payment_receipt_path', '$registered_date', 'pending')";
        
        if ($conn->query($insert)) {
            $registration_id = $conn->insert_id;
            
            $botToken = "8653253928:AAE2cpCsRhuSYI1DZZzHLdzBHMNUrUpU_0s";
            $chatId = "6823964923";
            
            $message = "🎨 *NEW STUDENT REGISTRATION* 🎨\n\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *Registration ID:* #{$registration_id}\n";
            $message .= "👤 *Name:* $first_name $last_name\n";
            $message .= "📞 *Phone:* $phone\n";
            if(!empty($email)) {
                $message .= "📧 *Email:* $email\n";
            }
            $message .= "🎓 *Course:* $course\n";
            if ($payment_receipt_path) {
                $message .= "💰 *Payment Receipt:* Uploaded\n";
            }
            $message .= "📅 *Registered:* " . getEthiopiaDateTime() . "\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "✅ *Status:* Pending Review\n\n";
            $message .= "#DanCreatives #NewStudent #Registration";
            
            $url = "https://api.telegram.org/bot$botToken/sendMessage";
            $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'Markdown'];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
            
            $success_message = "Registration Successful! Your payment receipt has been uploaded. We will verify and contact you soon.";
            
            $first_name = $last_name = $phone = $email = $course = '';
        } else {
            $error_message = "Failed to register. Please try again. Error: " . $conn->error;
        }
    } else {
        $error_message = "Please fill in all required fields (First Name, Last Name, Phone, Course)!";
    }
    
    if (!empty($upload_errors)) {
        $error_message = implode(' ', $upload_errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Dan Creatives</title>
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
            background: #ffffff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
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
            text-decoration: none;
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
            color: #ff4757;
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
            color: #ff4757;
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #ff4757;
        }

        .register-page {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        }

        .register-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            background: white;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .register-info {
            padding: 48px;
            background: linear-gradient(135deg, #2d3436 0%, #1e272e 100%);
            color: white;
        }

        .info-badge {
            display: inline-block;
            background: rgba(255, 71, 87, 0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            margin-bottom: 24px;
        }

        .register-info h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .register-info h1 span {
            color: #ff4757;
        }

        .register-info p {
            margin-bottom: 32px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .course-highlight {
            background: rgba(255,255,255,0.1);
            padding: 24px;
            border-radius: 24px;
            margin-bottom: 32px;
        }

        .course-highlight h3 {
            margin-bottom: 16px;
        }

        .course-highlight ul {
            list-style: none;
        }

        .course-highlight li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .course-highlight i {
            color: #ff4757;
        }

        .contact-info {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .contact-info p {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .register-form {
            padding: 48px;
        }

        .register-form h2 {
            margin-bottom: 32px;
            color: #1a2a3a;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: 0.3s;
        }

        .input-group input:focus, .input-group select:focus {
            outline: none;
            border-color: #ff4757;
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
        }

        .file-input-group {
            margin-bottom: 20px;
        }

        .file-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a2a3a;
        }

        .file-input-group label i {
            color: #ff4757;
        }

        .file-input-group input[type="file"] { display:none; }

        .upload-zone {
            border: 2px dashed #d9dee5; border-radius: 16px; background:#f8f9fa;
            padding: 26px 18px; text-align:center; cursor:pointer; transition:.2s;
        }
        .upload-zone:hover, .upload-zone.dragover { border-color:#ff4757; background:#fff5f6; }
        .upload-zone .uz-icon { font-size:30px; color:#ff4757; margin-bottom:8px; }
        .upload-zone .uz-title { font-weight:600; color:#1a2a3a; font-size:14px; }
        .upload-zone .uz-sub { font-size:12px; color:#a0aec0; margin-top:4px; }
        .upload-preview {
            display:none; align-items:center; gap:12px; background:#f8f9fa; border:1px solid #e2e8f0;
            border-radius:14px; padding:12px 14px; margin-top:10px;
        }
        .upload-preview.show { display:flex; }
        .upload-preview img { width:44px; height:44px; object-fit:cover; border-radius:8px; }
        .upload-preview .uz-file-icon { width:44px; height:44px; border-radius:8px; background:#ffe1e3; color:#ff4757; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .upload-preview .uz-info { flex:1; min-width:0; }
        .upload-preview .uz-name { font-size:13px; font-weight:600; color:#1a2a3a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .upload-preview .uz-size { font-size:11px; color:#a0aec0; }
        .upload-preview .uz-remove { color:#e74c3c; cursor:pointer; font-size:16px; padding:6px; flex-shrink:0; }

        .file-hint {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 8px;
        }

        /* Updated Bank Details Styles with Copy Feature */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 20px 0;
        }

        .bank-card {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 18px;
            border-left: 4px solid #ff4757;
            transition: all 0.3s ease;
        }

        .bank-card h4 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: #1a2a3a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bank-card h4 i {
            color: #ff4757;
            font-size: 1.1rem;
        }

        .account-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
        }

        .account-detail span:first-child {
            font-size: 0.85rem;
            color: #4a5568;
            font-weight: 500;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 1rem;
            font-weight: 700;
            color: #1a2a3a;
            letter-spacing: 1px;
        }

        .copy-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #ff4757;
            font-size: 1rem;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .copy-btn:hover {
            background: #ff4757;
            color: white;
        }

        .copy-btn.copied {
            background: #10b981;
            color: white;
        }

        .toast-message {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1a2a3a;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .toast-message.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .btn-submit {
            width: 100%;
            background: #ff4757;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #e63946;
            transform: translateY(-2px);
        }

        .form-note {
            font-size: 0.75rem;
            text-align: center;
            margin-top: 16px;
            color: #718096;
        }

        .form-note a {
            color: #ff4757;
            text-decoration: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .footer {
            background: #1e272e;
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
            color: #ff4757;
        }

        .footer-section a {
            display: block;
            color: #cbd5e0;
            text-decoration: none;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .footer-section a:hover {
            color: #ff4757;
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
                z-index: 999;
            }
            
            .register-container {
                grid-template-columns: 1fr;
            }
            
            .register-info, .register-form {
                padding: 32px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .toast-message {
                white-space: nowrap;
                font-size: 0.75rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <img src="images/logo.png" alt="Dan Creatives Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/40x40/ff4757/white?text=DC'">
            <div class="logo-text">Dan<span>Creatives</span></div>
        </a>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="register.php" class="active">Register</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="hamburger" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<section class="register-page">
    <div class="container">
        <div class="register-container">
            <div class="register-info">
                <div class="info-badge">
                    <i class="fas fa-graduation-cap"></i> Limited Seats Available
                </div>
                <h1>Start Your <span>Creative Journey</span> Today</h1>
                <p>Join Dan Creatives and unlock your potential as a professional graphics designer.</p>
                
                <div class="course-highlight">
                    <h3>What You'll Get:</h3>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Professional Certificate</li>
                        <li><i class="fas fa-check-circle"></i> Lifetime Access to Course Materials</li>
                        <li><i class="fas fa-check-circle"></i> 1-on-1 Mentorship Support</li>
                        <li><i class="fas fa-check-circle"></i> Practical Projects & Portfolio Building</li>
                        <li><i class="fas fa-check-circle"></i> Job Placement Assistance</li>
                    </ul>
                </div>
                
                <div class="contact-info">
                    <p><i class="fas fa-phone"></i> +251 920188600</p>
                    <p><i class="fab fa-telegram"></i> @genesis306</p>
                    <p><i class="fas fa-envelope"></i> dangraphics@gmail.com</p>
                </div>
            </div>
            
            <div class="register-form">
                <h2>Register Now</h2>
                
                <?php if($success_message): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error_message): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" name="first_name" placeholder="First Name" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" required>
                        </div>
                        <div class="input-group">
                            <input type="text" name="last_name" placeholder="Last Name" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email Address (Optional)" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="input-group">
                        <select name="course" required>
                            <option value="">Select a Course</option>
                            <?php
                            $courses_query = "SELECT title FROM courses WHERE status = 'active'";
                            $courses_result = $conn->query($courses_query);
                            if($courses_result && $courses_result->num_rows > 0):
                            while($course_row = $courses_result->fetch_assoc()):
                                $selected = ($selected_course == $course_row['title']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($course_row['title']); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($course_row['title']); ?>
                            </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    
                    <div class="file-input-group">
                        <label><i class="fas fa-receipt"></i> Payment Receipt / Bank Transfer Proof</label>
                        <input type="file" name="payment_receipt" id="receiptInput" accept="image/jpeg,image/png,image/gif,image/webp,.pdf">
                        <div class="upload-zone" id="uploadZone">
                            <div class="uz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <div class="uz-title">Tap to upload, or drag a file here</div>
                            <div class="uz-sub">JPG, PNG, GIF, WEBP or PDF — max 10MB</div>
                        </div>
                        <div class="upload-preview" id="uploadPreview">
                            <div class="uz-file-icon" id="uzFileIcon"><i class="fas fa-file"></i></div>
                            <img id="uzImgThumb" style="display:none;">
                            <div class="uz-info">
                                <div class="uz-name" id="uzFileName"></div>
                                <div class="uz-size" id="uzFileSize"></div>
                            </div>
                            <i class="fas fa-times uz-remove" id="uzRemove"></i>
                        </div>
                        <div class="file-hint">Your receipt stays private and is only visible to our team.</div>
                    </div>
                    <script>
                    (function(){
                        const input = document.getElementById('receiptInput');
                        const zone = document.getElementById('uploadZone');
                        const preview = document.getElementById('uploadPreview');
                        const fileName = document.getElementById('uzFileName');
                        const fileSize = document.getElementById('uzFileSize');
                        const imgThumb = document.getElementById('uzImgThumb');
                        const fileIcon = document.getElementById('uzFileIcon');
                        const removeBtn = document.getElementById('uzRemove');

                        function formatSize(bytes){
                            if (bytes < 1024) return bytes + ' B';
                            if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
                            return (bytes/(1024*1024)).toFixed(1) + ' MB';
                        }
                        function showFile(file){
                            fileName.textContent = file.name;
                            fileSize.textContent = formatSize(file.size);
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = e => { imgThumb.src = e.target.result; imgThumb.style.display='block'; fileIcon.style.display='none'; };
                                reader.readAsDataURL(file);
                            } else {
                                imgThumb.style.display='none'; fileIcon.style.display='flex';
                            }
                            zone.style.display='none';
                            preview.classList.add('show');
                        }
                        zone.addEventListener('click', () => input.click());
                        input.addEventListener('change', () => { if (input.files[0]) showFile(input.files[0]); });
                        ['dragover','dragenter'].forEach(evt => zone.addEventListener(evt, e => { e.preventDefault(); zone.classList.add('dragover'); }));
                        ['dragleave','drop'].forEach(evt => zone.addEventListener(evt, e => { e.preventDefault(); zone.classList.remove('dragover'); }));
                        zone.addEventListener('drop', e => {
                            if (e.dataTransfer.files[0]) { input.files = e.dataTransfer.files; showFile(e.dataTransfer.files[0]); }
                        });
                        removeBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            input.value = ''; preview.classList.remove('show'); zone.style.display='block';
                        });
                    })();
                    </script>
                    
                    <!-- Updated Payment Details with Copy Functionality -->
                    <div class="payment-methods">
                        <div class="bank-card">
                            <h4><i class="fas fa-university"></i> Commercial Bank of Ethiopia (CBE)</h4>
                            <div class="account-detail">
                                <span>Account Number:</span>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="account-number">1000597182407</span>
                                    <button type="button" class="copy-btn" data-copy="1000597182407">
                                        <i class="far fa-copy"></i> <span class="copy-text">Copy</span>
                                    </button>
                                </div>
                            </div>
                            <div class="account-detail">
                                <span>Account Name:</span>
                                <span style="font-weight: 600;">Daniel Asrat</span>
                            </div>
                        </div>
                        
                        <div class="bank-card">
                            <h4><i class="fas fa-mobile-alt"></i> Telebirr</h4>
                            <div class="account-detail">
                                <span>Telebirr Number:</span>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="account-number">0924026011</span>
                                    <button type="button" class="copy-btn" data-copy="0924026011">
                                        <i class="far fa-copy"></i> <span class="copy-text">Copy</span>
                                    </button>
                                </div>
                            </div>
                            <div class="account-detail">
                                <span>Account Name:</span>
                                <span style="font-weight: 600;">Daniel Asrat</span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Register Now <i class="fas fa-arrow-right"></i></button>
                    <p class="form-note">By registering, you agree to our <a href="#">Terms & Conditions</a> and <a href="#">Data Protection Policy</a>.</p>
                </form>
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
                    <a href="https://t.me/genesis306" target="_blank"><i class="fab fa-telegram"></i></a>
                    <a href="https://www.tiktok.com/@dancreative30_6" target="_blank"><i class="fab fa-tiktok"></i></a>
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

<!-- Toast Notification for Copy -->
<div id="copyToast" class="toast-message">
    <i class="fas fa-check-circle"></i> Copied to clipboard!
</div>

<script>
// Mobile menu toggle

// Copy functionality for bank details and telebirr
document.querySelectorAll('.copy-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        const textToCopy = this.getAttribute('data-copy');
        const originalText = this.innerHTML;
        const copySpan = this.querySelector('.copy-text');
        const originalSpanText = copySpan ? copySpan.innerText : 'Copy';
        
        try {
            // Modern copy method
            await navigator.clipboard.writeText(textToCopy);
            showToast('Copied: ' + textToCopy);
            
            // Update button style temporarily
            this.classList.add('copied');
            if (copySpan) copySpan.innerText = 'Copied!';
            
            // Reset after 2 seconds
            setTimeout(() => {
                this.classList.remove('copied');
                if (copySpan) copySpan.innerText = originalSpanText;
            }, 2000);
        } catch (err) {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = textToCopy;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('Copied: ' + textToCopy);
            
            this.classList.add('copied');
            if (copySpan) copySpan.innerText = 'Copied!';
            setTimeout(() => {
                this.classList.remove('copied');
                if (copySpan) copySpan.innerText = originalSpanText;
            }, 2000);
        }
    });
});

// Toast notification function
function showToast(message) {
    const toast = document.getElementById('copyToast');
    if (toast) {
        toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2000);
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const navMenu = document.querySelector('.nav-menu');
    const hamburger = document.querySelector('.hamburger');
    if (navMenu && navMenu.classList.contains('active') && 
        !navMenu.contains(event.target) && 
        !hamburger.contains(event.target)) {
        navMenu.classList.remove('active');
    }
});
</script>
<?php include 'includes/ai-chat-widget.php'; ?>
<div class="nav-backdrop" id="navBackdrop"></div>
<script src="assets/interactions.js"></script>
</body>
</html>