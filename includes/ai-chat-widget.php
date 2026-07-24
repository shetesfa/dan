<?php
// Include this file right before </body> on any public-facing page:
// <?php include 'includes/ai-chat-widget.php'; ?>
$dai_page = basename($_SERVER['PHP_SELF']);
?>
<style>
:root{
  --dai-coral:#ff6b6b; --dai-coral-dark:#ff5252; --dai-navy:#1a2a3a;
  --dai-bg:#ffffff; --dai-bubble-bot:#f1f3f7; --dai-shadow:0 10px 40px rgba(0,0,0,.18);
}
.dai-fab{
  position:fixed; bottom:22px; right:22px; z-index:9998;
  width:62px; height:62px; border-radius:50%;
  background:linear-gradient(135deg,var(--dai-coral),var(--dai-coral-dark));
  display:flex; align-items:center; justify-content:center; cursor:pointer;
  box-shadow:0 6px 24px rgba(255,107,107,.5); border:none; color:#fff; font-size:26px;
  transition:transform .25s ease, box-shadow .25s ease;
}
.dai-fab:hover{ transform:translateY(-3px) scale(1.04); box-shadow:0 10px 30px rgba(255,107,107,.6); }
.dai-fab::after{
  content:''; position:absolute; inset:0; border-radius:50%;
  box-shadow:0 0 0 0 rgba(255,107,107,.6); animation:dai-pulse 2.4s infinite;
}
@keyframes dai-pulse{ 0%{box-shadow:0 0 0 0 rgba(255,107,107,.45);} 70%{box-shadow:0 0 0 16px rgba(255,107,107,0);} 100%{box-shadow:0 0 0 0 rgba(255,107,107,0);} }
.dai-fab .dai-dot{ position:absolute; top:2px; right:2px; width:14px; height:14px; background:#2ecc71; border:2px solid #fff; border-radius:50%; }
.dai-fab svg{ width:28px; height:28px; }

.dai-panel{
  position:fixed; bottom:96px; right:22px; z-index:9999;
  width:392px; max-width:calc(100vw - 32px); height:min(640px,80vh);
  background:var(--dai-bg); border-radius:20px; box-shadow:var(--dai-shadow);
  display:flex; flex-direction:column; overflow:hidden;
  opacity:0; transform:translateY(24px) scale(.97); pointer-events:none;
  transition:opacity .25s ease, transform .25s ease;
  font-family:'Inter',sans-serif;
}
.dai-panel.dai-open{ opacity:1; transform:translateY(0) scale(1); pointer-events:auto; }

.dai-header{
  background:linear-gradient(135deg,var(--dai-navy),#26374a); color:#fff;
  padding:16px 18px; display:flex; align-items:center; gap:12px; flex-shrink:0;
}
.dai-avatar{ width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,var(--dai-coral),var(--dai-coral-dark)); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.dai-header-info{ flex:1; min-width:0; }
.dai-header-info h4{ font-size:15px; font-weight:700; margin:0; }
.dai-header-status{ font-size:11.5px; opacity:.85; display:flex; align-items:center; gap:5px; margin-top:2px; }
.dai-header-status .dai-live-dot{ width:7px; height:7px; border-radius:50%; background:#2ecc71; display:inline-block; }
.dai-header-actions{ display:flex; align-items:center; gap:6px; }
.dai-lang-btn{ background:rgba(255,255,255,.12); border:none; color:#fff; font-size:11px; font-weight:700; padding:6px 9px; border-radius:8px; cursor:pointer; }
.dai-lang-btn:hover{ background:rgba(255,255,255,.22); }
.dai-close-btn{ background:transparent; border:none; color:#fff; font-size:20px; cursor:pointer; opacity:.85; padding:4px; line-height:0; }
.dai-close-btn:hover{ opacity:1; }

.dai-body{ flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; background:#fafbfd; }
.dai-body::-webkit-scrollbar{ width:5px; }
.dai-body::-webkit-scrollbar-thumb{ background:#ddd; border-radius:10px; }

.dai-row{ display:flex; gap:8px; align-items:flex-end; max-width:88%; }
.dai-row.dai-user{ align-self:flex-end; flex-direction:row-reverse; max-width:85%; }
.dai-mini-avatar{ width:24px; height:24px; border-radius:50%; background:linear-gradient(135deg,var(--dai-coral),var(--dai-coral-dark)); flex-shrink:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; }
.dai-bubble{ padding:10px 14px; border-radius:16px; font-size:13.5px; line-height:1.5; word-wrap:break-word; white-space:pre-wrap; }
.dai-row.dai-bot .dai-bubble{ background:var(--dai-bubble-bot); color:#1a2a3a; border-bottom-left-radius:5px; }
.dai-row.dai-user .dai-bubble{ background:linear-gradient(135deg,var(--dai-coral),var(--dai-coral-dark)); color:#fff; border-bottom-right-radius:5px; }
.dai-row.dai-system{ align-self:center; max-width:100%; }
.dai-row.dai-system .dai-bubble{ background:#e8f8ef; color:#1e7a46; border:1px solid #c9ecd9; border-radius:12px; font-size:12.5px; text-align:center; }
.dai-img-thumb{ max-width:180px; border-radius:12px; margin-top:6px; display:block; }

.dai-typing{ display:flex; gap:4px; padding:12px 14px; background:var(--dai-bubble-bot); border-radius:16px; border-bottom-left-radius:5px; width:fit-content; }
.dai-typing span{ width:6px; height:6px; border-radius:50%; background:#9aa4b2; animation:dai-bounce 1.2s infinite; }
.dai-typing span:nth-child(2){ animation-delay:.15s; } .dai-typing span:nth-child(3){ animation-delay:.3s; }
@keyframes dai-bounce{ 0%,60%,100%{ transform:translateY(0); opacity:.5; } 30%{ transform:translateY(-5px); opacity:1; } }

.dai-chips{ display:flex; flex-wrap:wrap; gap:7px; padding:2px 0 4px; }
.dai-chip{ background:#fff; border:1.5px solid #ffd3d3; color:var(--dai-coral-dark); font-size:12px; font-weight:600; padding:7px 12px; border-radius:20px; cursor:pointer; transition:.2s; white-space:nowrap; }
.dai-chip:hover{ background:var(--dai-coral); color:#fff; border-color:var(--dai-coral); }

.dai-footer{ border-top:1px solid #eef1f5; padding:10px 12px; flex-shrink:0; background:#fff; }
.dai-preview{ display:none; align-items:center; gap:8px; padding:6px 8px; background:#f5f6f8; border-radius:10px; margin-bottom:8px; font-size:12px; color:#555; }
.dai-preview img{ width:34px; height:34px; object-fit:cover; border-radius:6px; }
.dai-preview .dai-remove{ margin-left:auto; cursor:pointer; color:#e74c3c; font-weight:700; }
.dai-input-row{ display:flex; align-items:flex-end; gap:8px; }
.dai-attach-btn{ background:#f1f3f7; border:none; width:38px; height:38px; border-radius:12px; cursor:pointer; color:#5a6472; font-size:15px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.dai-attach-btn:hover{ background:#e6e9ef; }
.dai-textarea{ flex:1; resize:none; border:1.5px solid #e6e9ef; border-radius:14px; padding:9px 12px; font-family:inherit; font-size:13.5px; max-height:90px; outline:none; }
.dai-textarea:focus{ border-color:var(--dai-coral); }
.dai-send-btn{ background:linear-gradient(135deg,var(--dai-coral),var(--dai-coral-dark)); border:none; width:38px; height:38px; border-radius:12px; cursor:pointer; color:#fff; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:14px; transition:.2s; }
.dai-send-btn:disabled{ opacity:.5; cursor:default; }
.dai-send-btn:not(:disabled):hover{ transform:scale(1.06); }
.dai-disclaimer{ text-align:center; font-size:10px; color:#a7adb5; margin-top:7px; }

@media (max-width:640px){
  .dai-panel{ right:0; bottom:0; left:0; width:100%; max-width:100%; height:88vh; border-radius:18px 18px 0 0; }
  .dai-fab{ bottom:16px; right:16px; }
}
</style>

<button class="dai-fab" id="daiFab" aria-label="Chat with us">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
  <span class="dai-dot"></span>
</button>

<div class="dai-panel" id="daiPanel">
  <div class="dai-header">
    <div class="dai-avatar"><i class="fas fa-robot"></i></div>
    <div class="dai-header-info">
      <h4>Dan AI Assistant</h4>
      <div class="dai-header-status"><span class="dai-live-dot"></span><span id="daiStatusText">Online · replies instantly</span></div>
    </div>
    <div class="dai-header-actions">
      <button class="dai-lang-btn" id="daiLangBtn" title="Switch language">EN</button>
      <button class="dai-close-btn" id="daiCloseBtn" aria-label="Close">&times;</button>
    </div>
  </div>

  <div class="dai-body" id="daiBody"></div>

  <div class="dai-footer">
    <div class="dai-preview" id="daiPreview">
      <img id="daiPreviewImg" src="" alt="">
      <span>Image attached</span>
      <span class="dai-remove" id="daiRemoveImg">&times;</span>
    </div>
    <div class="dai-input-row">
      <button class="dai-attach-btn" id="daiAttachBtn" title="Attach an image"><i class="fas fa-paperclip"></i></button>
      <input type="file" id="daiFileInput" accept="image/*" style="display:none">
      <textarea class="dai-textarea" id="daiInput" rows="1" placeholder="Type your message..."></textarea>
      <button class="dai-send-btn" id="daiSendBtn"><i class="fas fa-paper-plane"></i></button>
    </div>
    <div class="dai-disclaimer">AI assistant · can make mistakes · type "human" anytime to reach our team</div>
  </div>
</div>

<script>
(function(){
  const STR = {
    en: {
      welcome: "Hi! 👋 I'm the Dan Creatives assistant. Ask me about our design services, products, prices, or courses — or tap a quick option below.",
      chips: [["💼 Services","Tell me about your services and prices"],["🛍️ Products","What products do you sell?"],["🎓 Courses","Tell me about your courses"],["🧑‍💼 Talk to a human","__ESCALATE__"]],
      placeholder: "Type your message...",
      lang_label: "EN",
      status: "Online · replies instantly",
      escalated_banner: "✅ Our team has been notified and will reach out to you on Telegram or phone shortly.",
      img_too_big: "That image is a bit large — please attach one under 4MB.",
      human_prompt: "Sure — what's the best phone or Telegram username for our team to reach you?"
    },
    am: {
      welcome: "ሰላም! 👋 እኔ የዳን ክሪኤቲቭስ ረዳት ነኝ። ስለ አገልግሎቶቻችን፣ ምርቶቻችን፣ ዋጋ ወይም ኮርሶች ጠይቁኝ።",
      chips: [["💼 አገልግሎቶች","ስለ አገልግሎቶቻችሁ እና ዋጋ ንገሩኝ"],["🛍️ ምርቶች","ምን ምርቶች ትሸጣላችሁ?"],["🎓 ኮርሶች","ስለ ኮርሶቻችሁ ንገሩኝ"],["🧑‍💼 ከሰው ጋር ማውራት","__ESCALATE__"]],
      placeholder: "መልእክትዎን ይጻፉ...",
      lang_label: "አማ",
      status: "ተገናኝቷል · ወዲያውኑ ይመልሳል",
      escalated_banner: "✅ ቡድናችን ተነግሮታል፤ በቴሌግራም ወይም በስልክ በቅርቡ ያገኙዎታል።",
      img_too_big: "ምስሉ በጣም ትልቅ ነው — እባክዎ ከ4MB በታች ያያይዙ።",
      human_prompt: "እሺ — ቡድናችን እርስዎን ለማግኘት የትኛው ስልክ ወይም ቴሌግራም መጠቀም ይሻላል?"
    }
  };
  let lang = localStorage.getItem('dai_lang') || 'en';

  const fab = document.getElementById('daiFab');
  const panel = document.getElementById('daiPanel');
  const closeBtn = document.getElementById('daiCloseBtn');
  const langBtn = document.getElementById('daiLangBtn');
  const body = document.getElementById('daiBody');
  const input = document.getElementById('daiInput');
  const sendBtn = document.getElementById('daiSendBtn');
  const attachBtn = document.getElementById('daiAttachBtn');
  const fileInput = document.getElementById('daiFileInput');
  const preview = document.getElementById('daiPreview');
  const previewImg = document.getElementById('daiPreviewImg');
  const removeImg = document.getElementById('daiRemoveImg');
  const statusText = document.getElementById('daiStatusText');

  let pendingImage = null; // {base64, mime}
  let sessionId = localStorage.getItem('dai_session_id');
  if (!sessionId) {
    sessionId = 'dai-' + Date.now() + '-' + Math.random().toString(36).slice(2, 10);
    localStorage.setItem('dai_session_id', sessionId);
  }
  let history = JSON.parse(localStorage.getItem('dai_history_' + sessionId) || 'null');
  let opened = false;
  let awaitingHumanContact = false;

  function saveHistory(){ localStorage.setItem('dai_history_' + sessionId, JSON.stringify(history)); }

  function scrollDown(){ body.scrollTop = body.scrollHeight; }

  function addRow(kind, html){
    const row = document.createElement('div');
    row.className = 'dai-row dai-' + kind;
    if (kind === 'bot') {
      row.innerHTML = '<div class="dai-mini-avatar"><i class="fas fa-robot"></i></div><div class="dai-bubble">' + html + '</div>';
    } else if (kind === 'user') {
      row.innerHTML = '<div class="dai-bubble">' + html + '</div>';
    } else {
      row.innerHTML = '<div class="dai-bubble">' + html + '</div>';
    }
    body.appendChild(row);
    scrollDown();
    return row;
  }

  function addChips(){
    const wrap = document.createElement('div');
    wrap.className = 'dai-chips';
    STR[lang].chips.forEach(([label, msg]) => {
      const chip = document.createElement('div');
      chip.className = 'dai-chip';
      chip.textContent = label;
      chip.onclick = () => { if (msg === '__ESCALATE__') { startHumanHandoff(); } else { sendMessage(msg); } };
      wrap.appendChild(chip);
    });
    body.appendChild(wrap);
    scrollDown();
  }

  function escapeHtml(s){ const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

  function renderFromHistory(){
    body.innerHTML = '';
    if (!history || !history.length) {
      addRow('bot', escapeHtml(STR[lang].welcome));
      addChips();
      history = [{kind:'bot', text: STR[lang].welcome}];
      saveHistory();
      return;
    }
    history.forEach(m => {
      if (m.kind === 'chips') { addChips(); }
      else addRow(m.kind, escapeHtml(m.text).replace(/\n/g,'<br>'));
    });
  }

  function showTyping(){
    const row = document.createElement('div');
    row.className = 'dai-row dai-bot';
    row.id = 'daiTypingRow';
    row.innerHTML = '<div class="dai-mini-avatar"><i class="fas fa-robot"></i></div><div class="dai-typing"><span></span><span></span><span></span></div>';
    body.appendChild(row);
    scrollDown();
  }
  function hideTyping(){ const r = document.getElementById('daiTypingRow'); if (r) r.remove(); }

  function togglePanel(force){
    opened = force !== undefined ? force : !opened;
    panel.classList.toggle('dai-open', opened);
    if (opened) { renderFromHistory(); setTimeout(() => input.focus(), 200); }
  }
  fab.addEventListener('click', () => togglePanel());
  closeBtn.addEventListener('click', () => togglePanel(false));

  langBtn.textContent = STR[lang].lang_label;
  langBtn.addEventListener('click', () => {
    lang = lang === 'en' ? 'am' : 'en';
    localStorage.setItem('dai_lang', lang);
    langBtn.textContent = STR[lang].lang_label;
    input.placeholder = STR[lang].placeholder;
    statusText.textContent = STR[lang].status;
  });
  input.placeholder = STR[lang].placeholder;
  statusText.textContent = STR[lang].status;

  attachBtn.addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;
    if (file.size > 4 * 1024 * 1024) { alert(STR[lang].img_too_big); fileInput.value = ''; return; }
    const reader = new FileReader();
    reader.onload = () => {
      const result = reader.result; // data:image/xxx;base64,....
      const [meta, b64] = result.split(',');
      const mime = meta.match(/data:(.*);base64/)[1];
      pendingImage = { base64: b64, mime: mime };
      previewImg.src = result;
      preview.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  });
  removeImg.addEventListener('click', () => { pendingImage = null; fileInput.value = ''; preview.style.display = 'none'; });

  input.addEventListener('input', () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 90) + 'px'; });
  input.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSendClick(); } });
  sendBtn.addEventListener('click', handleSendClick);

  function handleSendClick(){
    const text = input.value.trim();
    if (!text && !pendingImage) return;
    if (awaitingHumanContact) {
      submitHumanContact(text);
      return;
    }
    sendMessage(text || '(sent an image)');
  }

  function startHumanHandoff(){
    addRow('bot', escapeHtml(STR[lang].human_prompt));
    history.push({kind:'bot', text: STR[lang].human_prompt});
    saveHistory();
    awaitingHumanContact = true;
    input.placeholder = lang === 'en' ? 'Phone or Telegram username...' : 'ስልክ ወይም ቴሌግራም...';
    input.focus();
  }

  function submitHumanContact(contact){
    awaitingHumanContact = false;
    input.value = ''; input.style.height = 'auto';
    input.placeholder = STR[lang].placeholder;
    if (contact) { addRow('user', escapeHtml(contact)); history.push({kind:'user', text: contact}); }
    showTyping();
    fetch('ai_chat.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ session_id: sessionId, message: contact, page: location.pathname.split('/').pop(), force_escalate: true, visitor_phone: contact, visitor_telegram: contact })
    }).then(r => r.json()).then(data => {
      hideTyping();
      addRow('system', escapeHtml(STR[lang].escalated_banner));
      history.push({kind:'system', text: STR[lang].escalated_banner});
      saveHistory();
    }).catch(() => { hideTyping(); });
  }

  function sendMessage(text){
    addRow('user', escapeHtml(text) + (pendingImage ? '<br><img class="dai-img-thumb" src="data:' + pendingImage.mime + ';base64,' + pendingImage.base64 + '">' : ''));
    history.push({kind:'user', text: text});
    saveHistory();
    input.value = ''; input.style.height = 'auto';
    const imgToSend = pendingImage;
    pendingImage = null; fileInput.value = ''; preview.style.display = 'none';
    sendBtn.disabled = true;
    showTyping();

    fetch('ai_chat.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        session_id: sessionId,
        message: text,
        page: location.pathname.split('/').pop(),
        image_base64: imgToSend ? imgToSend.base64 : null,
        image_mime: imgToSend ? imgToSend.mime : null
      })
    }).then(r => r.json()).then(data => {
      hideTyping();
      sendBtn.disabled = false;
      if (data.error) { return; }
      addRow('bot', escapeHtml(data.reply).replace(/\n/g,'<br>'));
      history.push({kind:'bot', text: data.reply});
      if (data.escalated) {
        addRow('system', escapeHtml(STR[lang].escalated_banner));
        history.push({kind:'system', text: STR[lang].escalated_banner});
      }
      saveHistory();
    }).catch(() => {
      hideTyping();
      sendBtn.disabled = false;
      const errText = lang === 'en' ? "Connection hiccup — please try again in a moment." : "የግንኙነት ችግር — እባክዎ ትንሽ ቆይተው ይሞክሩ።";
      addRow('bot', escapeHtml(errText));
      history.push({kind:'bot', text: errText});
      saveHistory();
    });
  }

  // Auto-open once per visit after a short delay to feel proactive but not pushy
  if (!sessionStorage.getItem('dai_auto_opened')) {
    sessionStorage.setItem('dai_auto_opened', '1');
    setTimeout(() => { if (!opened) { fab.style.transform = 'scale(1.08)'; setTimeout(()=>fab.style.transform='',300); } }, 3000);
  }
})();
</script>
