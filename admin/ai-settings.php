<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../admin_login_handler.php');
}

$settings = $conn->query("SELECT * FROM ai_settings WHERE id=1")->fetch_assoc();
if (!$settings) {
    $conn->query("INSERT INTO ai_settings (id) VALUES (1)");
    $settings = $conn->query("SELECT * FROM ai_settings WHERE id=1")->fetch_assoc();
}

$counts = [
    'active'    => $conn->query("SELECT COUNT(*) c FROM ai_conversations WHERE status='active'")->fetch_assoc()['c'],
    'escalated' => $conn->query("SELECT COUNT(*) c FROM ai_conversations WHERE status='escalated'")->fetch_assoc()['c'],
    'resolved'  => $conn->query("SELECT COUNT(*) c FROM ai_conversations WHERE status='resolved'")->fetch_assoc()['c'],
];

$statusLabels = [
    'active' => ['label' => 'Active & working', 'class' => 'status-active'],
    'not_configured' => ['label' => 'Not configured yet', 'class' => 'status-coming'],
    'limit_reached' => ['label' => 'Key limit reached — needs a new key', 'class' => 'status-inactive'],
    'invalid_key' => ['label' => 'Invalid API key', 'class' => 'status-inactive'],
    'error' => ['label' => 'Error — see details below', 'class' => 'status-inactive'],
];
$statusInfo = $statusLabels[$settings['status']] ?? $statusLabels['not_configured'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Assistant | Admin</title>
<link rel="icon" href="../images/logo.jpg">
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
.main-content { margin-left:280px; flex:1; padding:30px; }
.top-bar { background:white; padding:20px 30px; border-radius:12px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.card { background:white; border-radius:12px; padding:25px 30px; margin-bottom:25px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.card h3 { margin-bottom:6px; color:#1a2a3a; }
.card .sub { color:#718096; font-size:13px; margin-bottom:20px; }
.status-badge { padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
.status-active { background:#d4edda; color:#155724; }
.status-coming { background:#fff3cd; color:#856404; }
.status-inactive { background:#f8d7da; color:#721c24; }
.form-row { margin-bottom:18px; }
.form-row label { display:block; font-weight:600; margin-bottom:6px; color:#1a2a3a; font-size:14px; }
.form-row .hint { color:#a0aec0; font-size:12px; margin-top:5px; }
.form-row input[type=text], .form-row input[type=password], .form-row textarea, .form-row select {
  width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-family:inherit; font-size:14px;
}
.form-row textarea { min-height:90px; resize:vertical; }
.form-row input:focus, .form-row textarea:focus, .form-row select:focus { outline:none; border-color:#ff6b6b; }
.key-row { position:relative; }
.key-row .toggle-eye { position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#a0aec0; }
.switch-row { display:flex; align-items:center; gap:12px; }
.switch { position:relative; display:inline-block; width:46px; height:26px; }
.switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; inset:0; background:#ccc; border-radius:26px; transition:.3s; }
.slider::before { content:''; position:absolute; height:20px; width:20px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s; }
input:checked + .slider { background:#ff6b6b; }
input:checked + .slider::before { transform:translateX(20px); }
.btn { padding:11px 24px; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:8px; }
.btn-primary { background:#ff6b6b; color:white; } .btn-primary:hover { background:#ff5252; transform:translateY(-1px); }
.btn-outline { background:white; border:1.5px solid #ff6b6b; color:#ff6b6b; } .btn-outline:hover { background:#fff5f5; }
.actions-row { display:flex; gap:12px; margin-top:20px; flex-wrap:wrap; align-items:center; }
#testResult { font-size:13px; margin-top:10px; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; margin-bottom:25px; }
.stat-card { background:white; padding:18px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.stat-card .num { font-size:26px; font-weight:800; color:#1a2a3a; }
.stat-card .lbl { font-size:12px; color:#718096; margin-top:4px; }
.tabs { display:flex; gap:8px; margin-bottom:18px; }
.tab-btn { padding:8px 16px; border-radius:20px; border:1.5px solid #e2e8f0; background:white; cursor:pointer; font-size:13px; font-weight:600; color:#718096; }
.tab-btn.active { background:#1a2a3a; color:white; border-color:#1a2a3a; }
table { width:100%; border-collapse:collapse; }
th, td { padding:13px 15px; text-align:left; border-bottom:1px solid #eef2f6; font-size:13px; }
th { background:#fafbfe; font-weight:600; color:#1a2a3a; }
.conv-status { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; }
.cs-active { background:#cce5ff; color:#004085; }
.cs-escalated { background:#fff3cd; color:#856404; }
.cs-resolved { background:#d4edda; color:#155724; }
.view-btn { color:#17a2b8; cursor:pointer; font-weight:600; font-size:12px; margin-right:10px; }
.resolve-btn { color:#28a745; cursor:pointer; font-weight:600; font-size:12px; }
.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; }
.modal.show { display:flex; align-items:center; justify-content:center; }
.modal-box { background:white; width:92%; max-width:520px; max-height:80vh; border-radius:14px; display:flex; flex-direction:column; overflow:hidden; }
.modal-head { padding:18px 22px; border-bottom:1px solid #eef2f6; display:flex; justify-content:space-between; align-items:center; }
.modal-body { padding:18px 22px; overflow-y:auto; }
.msg-row { margin-bottom:12px; }
.msg-role { font-size:11px; font-weight:700; color:#a0aec0; margin-bottom:3px; text-transform:uppercase; }
.msg-text { background:#f5f6f8; padding:10px 14px; border-radius:10px; font-size:13.5px; white-space:pre-wrap; }
.msg-row.user .msg-text { background:#ffe9e9; }
.menu-toggle { display:none; position:fixed; top:20px; left:20px; z-index:101; background:#ff6b6b; color:white; padding:10px; border-radius:8px; cursor:pointer; }
@media (max-width:768px){ .sidebar{ transform:translateX(-100%);} .sidebar.active{ transform:translateX(0);} .main-content{ margin-left:0; padding:20px;} .menu-toggle{ display:block; } .top-bar{ padding-left:60px; } }
</style>
</head>
<body>
<div class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')"><i class="fas fa-bars"></i></div>
<div class="admin-container">
  <div class="sidebar">
    <div class="sidebar-header"><h3>Dan<span>Creatives</span></h3><p style="font-size:12px;margin-top:10px;">Admin Panel</p></div>
    <div class="sidebar-menu">
      <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
      <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
      <a href="manage-questions.php"><i class="fas fa-question-circle"></i> Questions</a>
      <a href="manage-about.php"><i class="fas fa-info-circle"></i> About Page</a>
      <a href="view-registrations.php"><i class="fas fa-users"></i> Registrations</a>
      <a href="ai-settings.php" class="active"><i class="fas fa-robot"></i> AI Assistant</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <div class="main-content">
    <div class="top-bar">
      <h2>AI Assistant</h2>
      <span class="status-badge <?php echo $statusInfo['class']; ?>"><?php echo $statusInfo['label']; ?></span>
    </div>

    <div class="stats-grid">
      <div class="stat-card"><div class="num"><?php echo $counts['active']; ?></div><div class="lbl">Active chats</div></div>
      <div class="stat-card"><div class="num"><?php echo $counts['escalated']; ?></div><div class="lbl">Waiting for you</div></div>
      <div class="stat-card"><div class="num"><?php echo $counts['resolved']; ?></div><div class="lbl">Resolved</div></div>
    </div>

    <div class="card">
      <h3><i class="fas fa-cog"></i> Setup</h3>
      <p class="sub">The assistant reads your services, products, courses and prices live from this site — no need to retype anything here. Get a free key from <a href="https://aistudio.google.com/apikey" target="_blank">Google AI Studio</a>, paste it below, and it goes live.</p>

      <form id="settingsForm">
        <div class="form-row">
          <label>Provider</label>
          <select name="provider" id="provider">
            <option value="gemini" <?php echo $settings['provider']==='gemini'?'selected':''; ?>>Google Gemini (free tier)</option>
          </select>
          <div class="hint">Gemini is used because it has a genuinely free tier with no card required.</div>
        </div>

        <div class="form-row">
          <label>API key</label>
          <div class="key-row">
            <input type="password" name="api_key" id="apiKey" placeholder="<?php echo !empty($settings['api_key']) ? 'Key saved — leave blank to keep it' : 'Paste your Gemini API key here'; ?>">
            <span class="toggle-eye" onclick="const i=document.getElementById('apiKey'); i.type = i.type==='password'?'text':'password';"><i class="fas fa-eye"></i></span>
          </div>
          <div class="hint">When your key hits its daily free limit, just paste a new one here — nothing else changes.</div>
        </div>

        <div class="form-row">
          <label>Model</label>
          <select name="model" id="model">
            <option value="gemini-2.5-flash-lite" <?php echo $settings['model']==='gemini-2.5-flash-lite'?'selected':''; ?>>gemini-2.5-flash-lite — recommended, highest free daily quota</option>
            <option value="gemini-2.5-flash" <?php echo $settings['model']==='gemini-2.5-flash'?'selected':''; ?>>gemini-2.5-flash — smarter, lower free quota</option>
            <option value="gemini-2.5-pro" <?php echo $settings['model']==='gemini-2.5-pro'?'selected':''; ?>>gemini-2.5-pro — most capable, lowest free quota</option>
          </select>
        </div>

        <div class="form-row">
          <label>Assistant personality (optional)</label>
          <textarea name="system_prompt" placeholder="e.g. You are the friendly virtual assistant for Dan Creatives, a graphics design studio and design academy in Ethiopia. Be warm, concise, and helpful."><?php echo htmlspecialchars($settings['system_prompt'] ?? ''); ?></textarea>
          <div class="hint">Leave blank to use the default. Business facts (prices, services, courses) are always pulled live — you don't need to list them here.</div>
        </div>

        <div class="form-row">
          <label>Welcome message override (optional)</label>
          <textarea name="welcome_message" placeholder="Hi! I'm the Dan Creatives assistant..."><?php echo htmlspecialchars($settings['welcome_message'] ?? ''); ?></textarea>
        </div>

        <div class="form-row switch-row">
          <label class="switch"><input type="checkbox" id="enabled" name="enabled" <?php echo $settings['enabled']?'checked':''; ?>><span class="slider"></span></label>
          <span>Assistant enabled on the site</span>
        </div>

        <div class="actions-row">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save settings</button>
          <button type="button" class="btn btn-outline" id="testBtn"><i class="fas fa-bolt"></i> Send test message</button>
        </div>
        <div id="testResult"></div>
        <?php if (!empty($settings['last_error'])): ?>
          <div id="lastErrorBox" style="margin-top:14px;padding:12px 16px;background:#fff5f5;border:1px solid #ffd3d3;border-radius:10px;font-size:12.5px;color:#c0392b;">
            Last error (<?php echo htmlspecialchars($settings['last_checked_at'] ?? ''); ?>): <?php echo htmlspecialchars($settings['last_error']); ?>
          </div>
        <?php endif; ?>
      </form>
    </div>

    <div class="card">
      <h3><i class="fas fa-comments"></i> Conversations</h3>
      <p class="sub">Every chat is logged here. Anything the AI can't confidently answer gets flagged to you on Telegram automatically.</p>
      <div class="tabs">
        <button class="tab-btn active" data-filter="all" onclick="filterConvs('all', this)">All</button>
        <button class="tab-btn" data-filter="escalated" onclick="filterConvs('escalated', this)">Needs you</button>
        <button class="tab-btn" data-filter="active" onclick="filterConvs('active', this)">Active</button>
        <button class="tab-btn" data-filter="resolved" onclick="filterConvs('resolved', this)">Resolved</button>
      </div>
      <div style="overflow-x:auto;">
        <table>
          <thead><tr><th>#</th><th>Visitor</th><th>Status</th><th>Last activity</th><th>Actions</th></tr></thead>
          <tbody id="convTableBody">
            <?php
            $convs = $conn->query("SELECT * FROM ai_conversations ORDER BY last_message_at DESC LIMIT 100");
            while ($c = $convs->fetch_assoc()):
                $name = $c['visitor_name'] ?: 'Anonymous visitor';
                $contact = trim(($c['visitor_phone'] ?: '') . ($c['visitor_telegram'] ? ' · @' . $c['visitor_telegram'] : ''));
            ?>
            <tr data-status="<?php echo $c['status']; ?>">
              <td>#<?php echo $c['id']; ?></td>
              <td><?php echo htmlspecialchars($name); ?><?php if($contact): ?><br><small style="color:#a0aec0;"><?php echo htmlspecialchars($contact); ?></small><?php endif; ?></td>
              <td><span class="conv-status cs-<?php echo $c['status']; ?>"><?php echo ucfirst($c['status']); ?></span></td>
              <td><?php echo date('M d, h:i A', strtotime($c['last_message_at'])); ?></td>
              <td>
                <span class="view-btn" onclick="viewConversation(<?php echo $c['id']; ?>)"><i class="fas fa-eye"></i> View</span>
                <?php if ($c['status'] !== 'resolved'): ?>
                <span class="resolve-btn" onclick="markResolved(<?php echo $c['id']; ?>, this)"><i class="fas fa-check"></i> Resolve</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="convModal">
  <div class="modal-box">
    <div class="modal-head"><h3 id="modalTitle">Conversation</h3><i class="fas fa-times" style="cursor:pointer;" onclick="document.getElementById('convModal').classList.remove('show')"></i></div>
    <div class="modal-body" id="modalBody">Loading...</div>
  </div>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fd.set('enabled', document.getElementById('enabled').checked ? '1' : '0');
  fd.set('action', 'save_settings');
  fetch('ai_actions.php', { method:'POST', body:fd })
    .then(r=>r.json()).then(d=>{
      const box = document.getElementById('testResult');
      box.style.color = '#28a745';
      box.textContent = 'Saved ✓';
      document.getElementById('apiKey').value = '';
      setTimeout(()=>{ box.textContent=''; }, 3000);
    });
});

document.getElementById('testBtn').addEventListener('click', function(){
  const box = document.getElementById('testResult');
  box.style.color = '#718096';
  box.textContent = 'Testing...';
  fetch('ai_actions.php?action=test_connection', { method:'POST' })
    .then(r=>r.json()).then(d=>{
      if (d.ok) { box.style.color = '#28a745'; box.textContent = '✓ Working! Model replied: "' + d.message + '"'; }
      else { box.style.color = '#c0392b'; box.textContent = '✗ ' + d.message; }
    });
});

function filterConvs(filter, btn){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#convTableBody tr').forEach(tr=>{
    tr.style.display = (filter==='all' || tr.dataset.status===filter) ? '' : 'none';
  });
}

function markResolved(id, el){
  const fd = new FormData(); fd.set('action','mark_resolved'); fd.set('id', id);
  fetch('ai_actions.php', { method:'POST', body:fd }).then(()=>location.reload());
}

function viewConversation(id){
  document.getElementById('convModal').classList.add('show');
  document.getElementById('modalTitle').textContent = 'Conversation #' + id;
  const bodyEl = document.getElementById('modalBody');
  bodyEl.innerHTML = 'Loading...';
  fetch('ai_actions.php?action=get_conversation&id=' + id)
    .then(r=>r.json()).then(d=>{
      if (!d.ok) { bodyEl.textContent = 'Could not load.'; return; }
      bodyEl.innerHTML = d.messages.map(m => `
        <div class="msg-row ${m.role}">
          <div class="msg-role">${m.role}</div>
          <div class="msg-text">${m.content.replace(/</g,'&lt;')}</div>
        </div>`).join('');
    });
}
</script>
</body>
</html>
