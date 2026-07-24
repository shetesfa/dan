<?php
require_once '../config.php';
if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $username = $_SESSION['admin_username'] ?? 'admin';

    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || !password_verify($current, $row['password'])) {
        $error = "Your current password is incorrect.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($new !== $confirm) {
        $error = "New password and confirmation don't match.";
    } else {
        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $upd = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
        $upd->bind_param('si', $newHash, $row['id']);
        $upd->execute();
        $success = "Password updated! Use it next time you log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Inter',sans-serif; background:#f8f9fa; }
.admin-container { display:flex; min-height:100vh; }
.sidebar { width:280px; background:#1a2a3a; color:white; position:fixed; height:100vh; overflow-y:auto; }
.sidebar-header { padding:30px; text-align:center; border-bottom:1px solid rgba(255,255,255,0.1); }
.sidebar-header h3 { font-size:24px; } .sidebar-header span { color:#ff6b6b; }
.sidebar-menu { padding:20px 0; }
.sidebar-menu a { display:flex; align-items:center; gap:12px; padding:12px 30px; color:#cbd5e0; text-decoration:none; transition:.3s; }
.sidebar-menu a:hover, .sidebar-menu a.active { background:rgba(255,107,107,0.1); color:#ff6b6b; }
.main-content { margin-left:280px; flex:1; padding:30px; max-width:520px; }
.card { background:white; border-radius:12px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.form-row { margin-bottom:18px; }
.form-row label { display:block; font-weight:600; margin-bottom:6px; color:#1a2a3a; font-size:14px; }
.form-row input { width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; }
.form-row input:focus { outline:none; border-color:#ff6b6b; }
.btn { padding:11px 24px; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; background:#ff6b6b; color:white; }
.btn:hover { background:#ff5252; }
.msg { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:18px; }
.msg.success { background:#d4edda; color:#155724; }
.msg.error { background:#f8d7da; color:#721c24; }
.menu-toggle { display:none; position:fixed; top:20px; left:20px; z-index:101; background:#ff6b6b; color:white; padding:10px; border-radius:8px; cursor:pointer; }
@media (max-width:768px){ .sidebar{ transform:translateX(-100%);} .sidebar.active{ transform:translateX(0);} .main-content{ margin-left:0; padding:20px;} .menu-toggle{ display:block; } }
</style>
</head>
<body>
<div class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')"><i class="fas fa-bars"></i></div>
<div class="admin-container">
  <div class="sidebar">
    <div class="sidebar-header"><h3>Dan<span>Creatives</span></h3><p style="font-size:12px;margin-top:10px;">Admin Panel</p></div>
    <div class="sidebar-menu">
      <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
      <a href="ai-settings.php"><i class="fas fa-robot"></i> AI Assistant</a>
      <a href="change-password.php" class="active"><i class="fas fa-key"></i> Change Password</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
  <div class="main-content">
    <h2 style="margin-bottom:20px;">Change Password</h2>
    <div class="card">
      <?php if ($success): ?><div class="msg success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
      <?php if ($error): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="POST">
        <div class="form-row"><label>Current password</label><input type="password" name="current_password" required></div>
        <div class="form-row"><label>New password</label><input type="password" name="new_password" required minlength="6"></div>
        <div class="form-row"><label>Confirm new password</label><input type="password" name="confirm_password" required minlength="6"></div>
        <button type="submit" class="btn"><i class="fas fa-save"></i> Update password</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
