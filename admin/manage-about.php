<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

function img_upload_field_row($name, $value, $label, $required = false) {
    $safeVal = htmlspecialchars($value);
    $req = $required ? 'required' : '';
    $showPreview = !empty($value) ? 'display:block;' : 'display:none;';
    echo "<div class=\"form-group\">
        <label>{$label}</label>
        <div class=\"img-field-wrap\">
            <input type=\"text\" name=\"{$name}\" id=\"{$name}\" value=\"{$safeVal}\" placeholder=\"Upload below, or paste an image URL\" {$req} oninput=\"syncImgPreview('{$name}')\">
            <div class=\"img-upload-row\">
                <button type=\"button\" class=\"img-upload-btn\" onclick=\"document.getElementById('{$name}_file').click()\"><i class=\"fas fa-upload\"></i> Upload image</button>
                <input type=\"file\" id=\"{$name}_file\" accept=\"image/*\" style=\"display:none\" onchange=\"uploadImageFor('{$name}', this)\">
                <span class=\"img-upload-status\" id=\"{$name}_status\"></span>
            </div>
            <img class=\"img-field-preview\" id=\"{$name}_preview\" src=\"{$safeVal}\" style=\"{$showPreview}\" onerror=\"this.style.display='none'\">
        </div>
    </div>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            youtube_channel_url='$youtube_channel_url',
            youtube_thumbnail='$youtube_thumbnail',
            youtube_badge_text='$youtube_badge_text',
            channel_description='$channel_description',
            instructor_name='$instructor_name',
            instructor_title='$instructor_title',
            instructor_bio='$instructor_bio',
            instructor_image='$instructor_image',
            youtube_video_1_url='$video1_url',
            youtube_video_1_thumbnail='$video1_thumbnail',
            youtube_video_1_title='$video1_title',
            youtube_video_1_views='$video1_views',
            youtube_video_2_url='$video2_url',
            youtube_video_2_thumbnail='$video2_thumbnail',
            youtube_video_2_title='$video2_title',
            youtube_video_2_views='$video2_views',
            youtube_video_3_url='$video3_url',
            youtube_video_3_thumbnail='$video3_thumbnail',
            youtube_video_3_title='$video3_title',
            youtube_video_3_views='$video3_views'
            WHERE id=1";
    
    if ($conn->query($sql)) {
        $success = "About page updated successfully!";
    } else {
        $error = "Failed to update about page.";
    }
}

$about = $conn->query("SELECT * FROM about_content WHERE id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Page - Admin Panel</title>
    <link rel="icon" href="../images/logo.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: #1a2a3a;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            font-size: 24px;
        }
        
        .sidebar-header span {
            color: #ff6b6b;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 30px;
            color: #cbd5e0;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,107,107,0.1);
            color: #ff6b6b;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 30px;
        }
        
        .top-bar {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #eef2f6;
            border-radius: 12px;
        }
        
        .form-section h3 {
            margin-bottom: 20px;
            color: #ff6b6b;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .btn-submit {
            background: #ff6b6b;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Dan<span>Creatives</span></h3>
                <p style="font-size: 12px; margin-top: 10px;">Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
                <a href="manage-comments.php"><i class="fas fa-comments"></i> Manage Comments</a>
                <a href="manage-about.php" class="active"><i class="fas fa-info-circle"></i> About Page</a>
                <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
                <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h2>Manage About Page</h2>
            </div>
            
            <div class="form-container">
                <?php if(isset($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-section">
                        <h3>YouTube Channel Info</h3>
                        <div class="form-group">
                            <label>YouTube Channel URL</label>
                            <input type="text" name="youtube_channel_url" value="<?php echo htmlspecialchars($about['youtube_channel_url']); ?>" required>
                        </div>
                        <?php img_upload_field_row('youtube_thumbnail', $about['youtube_thumbnail'], 'Channel Thumbnail Image Path', true); ?>
                        <div class="form-group">
                            <label>Badge Text</label>
                            <input type="text" name="youtube_badge_text" value="<?php echo htmlspecialchars($about['youtube_badge_text']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Channel Description</label>
                            <textarea name="channel_description"><?php echo htmlspecialchars($about['channel_description']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Instructor Info</h3>
                        <div class="form-group">
                            <label>Instructor Name</label>
                            <input type="text" name="instructor_name" value="<?php echo htmlspecialchars($about['instructor_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Instructor Title</label>
                            <input type="text" name="instructor_title" value="<?php echo htmlspecialchars($about['instructor_title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Instructor Bio</label>
                            <textarea name="instructor_bio"><?php echo htmlspecialchars($about['instructor_bio']); ?></textarea>
                        </div>
                        <?php img_upload_field_row('instructor_image', $about['instructor_image'], 'Instructor Image Path', true); ?>
                    </div>
                    
                    <div class="form-section">
                        <h3>Video 1</h3>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="text" name="video1_url" value="<?php echo htmlspecialchars($about['youtube_video_1_url']); ?>">
                        </div>
                        <?php img_upload_field_row('video1_thumbnail', $about['youtube_video_1_thumbnail'], 'Video Thumbnail', false); ?>
                        <div class="form-group">
                            <label>Video Title</label>
                            <input type="text" name="video1_title" value="<?php echo htmlspecialchars($about['youtube_video_1_title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Views</label>
                            <input type="text" name="video1_views" value="<?php echo htmlspecialchars($about['youtube_video_1_views']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Video 2</h3>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="text" name="video2_url" value="<?php echo htmlspecialchars($about['youtube_video_2_url']); ?>">
                        </div>
                        <?php img_upload_field_row('video2_thumbnail', $about['youtube_video_2_thumbnail'], 'Video Thumbnail', false); ?>
                        <div class="form-group">
                            <label>Video Title</label>
                            <input type="text" name="video2_title" value="<?php echo htmlspecialchars($about['youtube_video_2_title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Views</label>
                            <input type="text" name="video2_views" value="<?php echo htmlspecialchars($about['youtube_video_2_views']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Video 3</h3>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="text" name="video3_url" value="<?php echo htmlspecialchars($about['youtube_video_3_url']); ?>">
                        </div>
                        <?php img_upload_field_row('video3_thumbnail', $about['youtube_video_3_thumbnail'], 'Video Thumbnail', false); ?>
                        <div class="form-group">
                            <label>Video Title</label>
                            <input type="text" name="video3_title" value="<?php echo htmlspecialchars($about['youtube_video_3_title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Views</label>
                            <input type="text" name="video3_views" value="<?php echo htmlspecialchars($about['youtube_video_3_views']); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Update About Page</button>
                </form>
            </div>
        </div>
    </div>

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