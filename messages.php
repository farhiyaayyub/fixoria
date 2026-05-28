<?php
require __DIR__ . '/auth/db.php';
session_start();
// accept provider_id from GET or POST (defensive: in-case the form posts without JS)
$provider_id = isset($_REQUEST['provider_id']) ? (int)$_REQUEST['provider_id'] : 0;
$provider = null;
if($provider_id){
  $stmt = $pdo->prepare('SELECT id, username, first_name, last_name, email FROM users WHERE id = :id LIMIT 1');
  $stmt->execute(['id'=>$provider_id]);
  $provider = $stmt->fetch(PDO::FETCH_ASSOC);
}
$me = isset($_SESSION['user_id']) ? ['id'=>$_SESSION['user_id'],'name'=>($_SESSION['first_name'] ?? $_SESSION['username'] ?? '')] : null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Messages — Fixora</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="antialiased bg-slate-50 text-slate-800">
  <main class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-white rounded-lg shadow p-4">
      <div class="flex items-center gap-4 mb-4">
        <a href="providers.php" class="text-sm text-slate-500">← Back to providers</a>
        <h1 class="text-xl font-semibold">Messages</h1>
      </div>
      <?php if(!$provider): ?>
        <div class="text-slate-600">No provider selected. Go back and pick a provider to message.</div>
      <?php else: ?>
        <div id="chatRoot">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-lg font-bold text-slate-700"><?php echo htmlspecialchars(strtoupper(substr(($provider['first_name'] ?? $provider['username'] ?? 'U'),0,1))); ?></div>
            <div>
              <div class="font-semibold"><?php echo htmlspecialchars(trim(($provider['first_name'] ?? '') . ' ' . ($provider['last_name'] ?? '') ?: ($provider['username'] ?? ''))); ?></div>
              <div class="text-sm text-slate-500"><?php echo htmlspecialchars($provider['email'] ?? ''); ?></div>
            </div>
          </div>

          <div id="messages" style="height:60vh;overflow:auto;padding:12px;border:1px solid #eee;border-radius:8px;background:#fff"></div>

          <pre id="sendDebug" class="mt-2 p-2 bg-slate-50 text-xs text-slate-600 rounded" style="max-height:140px;overflow:auto;display:none"></pre>
          <div id="sendStatus" class="mt-2 text-sm text-slate-600">JS status: initializing…</div>

          <form id="sendForm" class="mt-3 space-y-2" action="./auth/messages_api.php" method="post">
            <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($provider_id); ?>">
            <input type="hidden" name="action" value="send">
            <?php if(!$me): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <input id="guestName" name="sender_name" type="text" placeholder="Your name" class="rounded border px-3 py-2" autocomplete="name">
              <input id="guestEmail" name="sender_email" type="email" placeholder="Your email (optional)" class="rounded border px-3 py-2" autocomplete="email">
            </div>
            <?php endif; ?>
            <div class="flex gap-2">
              <input id="msgInput" name="body" type="text" placeholder="Message..." class="flex-1 rounded border px-3 py-2" autocomplete="off">
              <input id="fileInput" type="file" accept="image/*" style="display:none">
              <button type="button" id="attachBtn" class="px-3 py-2 bg-slate-100 rounded">📎</button>
              <button type="button" id="sendBtn" onclick="(window.__handleSend||(()=>{}))()" class="px-4 py-2 bg-indigo-600 text-white rounded">Send</button>
            </div>
          </form>
        </div>
        <script>
          (function(){
            const providerId = <?php echo json_encode($provider_id); ?>
            const me = <?php echo json_encode($me); ?>
            const guestNameEl = document.getElementById('guestName')
            const guestEmailEl = document.getElementById('guestEmail')
            const messagesEl = document.getElementById('messages')
            const form = document.getElementById('sendForm')
            const input = document.getElementById('msgInput')
            const fileInput = document.getElementById('fileInput')
            const attachBtn = document.getElementById('attachBtn')
            let polling = null
            let messagesCache = []

            function escapeHtml(s){ return String(s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;") }

            async function fetchAll(since){
              try{
                // compute conversation key (guest-only) so we only load this guest's thread
                let url = './auth/messages_api.php?action=fetch&provider_id='+encodeURIComponent(providerId)
                const conv = (guestEmailEl?.value?.trim()) ? ('email:'+guestEmailEl.value.trim().toLowerCase()) : ('name:' + (guestNameEl?.value?.trim() || 'guest'))
                if(conv) url += '&conversation_key=' + encodeURIComponent(conv)
                if(since) url += '&since='+encodeURIComponent(since)
                const res = await fetch(url, { credentials: 'same-origin' })
                if(!res.ok) return []
                const j = await res.json(); return j.messages || []
              }catch(e){ console.error('fetchAll error', e); return [] }
            }

            function render(messages){
              messagesCache = messages || []
              messagesEl.innerHTML = ''
              messages.forEach(m => {
                const el = document.createElement('div')
                el.style.marginBottom = '12px'
                const isMe = me && String(m.sender_id) === String(me.id)
                const senderLabel = m.sender_name ? '<div style="font-weight:600;margin-bottom:6px">'+escapeHtml(m.sender_name)+'</div>' : ''
                el.innerHTML = `<div style="display:flex;justify-content:${isMe?'flex-end':'flex-start'}"><div style="max-width:70%;padding:10px;border-radius:10px;background:${isMe?'#4f46e5':'#f1f5f9'};color:${isMe?'#fff':'#0f172a'}">${senderLabel}${m.attachment?('<div style="margin-bottom:8px"><img src="'+escapeHtml(m.attachment)+'" style="max-width:220px;border-radius:8px"></div>') : '' }<div>${escapeHtml(m.body)}</div><div style="font-size:11px;opacity:0.8;margin-top:6px">${new Date(m.created_at).toLocaleString()}</div></div></div>`
                messagesEl.appendChild(el)
              })
              messagesEl.scrollTop = messagesEl.scrollHeight
            }

            // Maintain an efficient poll loop using the latest message timestamp
            function getLatestTimestamp(){
              if(!messagesCache.length) return null
              const last = messagesCache[messagesCache.length-1]
              return last && last.created_at ? last.created_at : null
            }

            async function poll(){
              const since = getLatestTimestamp()
              const msgs = await fetchAll(since)
              if(msgs && msgs.length){
                // merge server messages into cache, replace temporary messages when possible
                msgs.forEach(serverMsg => {
                  if(messagesCache.find(m=>String(m.id) === String(serverMsg.id))) return
                  // try to match and replace a temp message by body+sender_name
                  let replaced = false
                  for(let i=0;i<messagesCache.length;i++){
                    const m = messagesCache[i]
                    if(String(m.id).startsWith('temp-') && m.body === serverMsg.body && ((m.sender_name||'') === (serverMsg.sender_name||''))){
                      messagesCache[i] = serverMsg; replaced = true; break
                    }
                  }
                  if(!replaced) messagesCache.push(serverMsg)
                })
                render(messagesCache)
              }
            }

            // Start polling; interval adapts to visibility to reduce load
            let pollInterval = 1500
            function startPolling(){ if(polling) clearInterval(polling); poll(); polling = setInterval(poll, pollInterval) }
            function stopPolling(){ if(polling) clearInterval(polling); polling = null }
            startPolling()

            // Speed up/slow down when page visibility changes
            document.addEventListener('visibilitychange', function(){
              if(document.hidden){ pollInterval = 5000 } else { pollInterval = 1200 }
              startPolling()
            })

            // BroadcastChannel for real-time updates across tabs (same-origin)
            let bc = null
            try{ if('BroadcastChannel' in window) bc = new BroadcastChannel('fixora_messages') }catch(e){ bc = null }
            if(bc){
              bc.addEventListener('message', function(ev){
                try{
                  const m = ev.data
                  if(!m || String(m.provider_id) !== String(providerId)) return
                  // avoid duplicates
                  if(!messagesCache.find(x=>String(x.id) === String(m.id))){ messagesCache.push(m); render(messagesCache) }
                }catch(e){ console.error('bc message', e) }
              })
            }

            attachBtn.addEventListener('click', ()=> fileInput.click())
            fileInput.addEventListener('change', function(){})

            const sendBtn = document.getElementById('sendBtn')
            const statusEl = document.getElementById('sendStatus')

            async function handleSend(e){
              if(e && e.preventDefault) e.preventDefault()
              try{ if(statusEl) statusEl.textContent = 'JS status: sending...' }catch(e){}
              const body = input.value.trim(); if(!body && !fileInput.files.length) return;
              if(!me){
                // Allow guests to send without providing a name; server will default to 'Guest'.
              }
              let attachment = null
              if(fileInput.files && fileInput.files[0]){
                const f = fileInput.files[0]
                const rdr = new FileReader()
                rdr.onload = async function(ev){ attachment = ev.target.result;
                  // optimistic append with attachment
                  try{
                    const tempId = 'temp-'+Date.now()+'-'+Math.round(Math.random()*1000)
                    const tempMsg = { id: tempId, provider_id: providerId, sender_id: (me?me.id:null), sender_name: (me?me.name:(guestNameEl?.value?.trim() || 'Guest')), sender_email: (me?null:(guestEmailEl?.value||null)), body: body, attachment: attachment, created_at: (new Date()).toISOString() }
                    messagesCache.push(tempMsg); render(messagesCache)
                  }catch(e){ console.error('optimistic append failed', e) }
                  await send(body, attachment); input.value = ''; fileInput.value = ''; }
                rdr.readAsDataURL(f)
                return
              }
              // optimistic append without attachment
              try{
                const tempId = 'temp-'+Date.now()+'-'+Math.round(Math.random()*1000)
                const tempMsg = { id: tempId, provider_id: providerId, sender_id: (me?me.id:null), sender_name: (me?me.name:(guestNameEl?.value?.trim() || 'Guest')), sender_email: (me?null:(guestEmailEl?.value||null)), body: body, attachment: null, created_at: (new Date()).toISOString() }
                messagesCache.push(tempMsg); render(messagesCache)
              }catch(e){ console.error('optimistic append failed', e) }
              await send(body, null); input.value = ''
            }

            // expose for inline onclick fallback
            window.__handleSend = handleSend
            try{ if(statusEl) statusEl.textContent = 'JS status: ready' }catch(e){}
            form.addEventListener('submit', handleSend)
            sendBtn?.addEventListener('click', handleSend)

            async function send(body, attachment){
              const sendBtnEl = document.getElementById('sendBtn')
              try{
                // compute a conversation key so both guest and provider messages appear in one thread
                let convKey = null
                if(!me){
                  // guest -> prefer email when provided, otherwise name
                  const em = guestEmailEl?.value?.trim() || null
                  const nm = guestNameEl?.value?.trim() || ''
                  if(em) convKey = 'email:' + em.toLowerCase()
                  else convKey = 'name:' + (nm || 'guest')
                }
                const payload = { action: 'send', provider_id: providerId, body: body, sender_id: (me?me.id:null), sender_name: (me?me.name:null), sender_email: null, attachment: attachment, conversation_key: convKey }
                if(!me){
                  payload.sender_name = guestNameEl?.value?.trim() || 'Guest'
                  payload.sender_email = guestEmailEl?.value?.trim() || null
                }
                // disable UI while sending
                if(sendBtnEl){ sendBtnEl.disabled = true; sendBtnEl.classList.add('opacity-60'); }
                const res = await fetch('./auth/messages_api.php', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) })
                let j = null
                try{ j = await res.json() }catch(e){ j = null }
                // show server response in debug panel
                const debugEl = document.getElementById('sendDebug')
                try{ if(debugEl) { debugEl.textContent = 'Request: '+JSON.stringify(payload)+'\nResponse: '+JSON.stringify(j||{httpStatus:res.status}); debugEl.style.display = 'block' } }catch(e){}
                if(!res.ok){
                  console.error('send failed', res.status, j)
                  const msg = (j && j.error) ? (j.error + (j.msg?(': '+j.msg):'')) : ('HTTP '+res.status)
                  alert('Message send failed: ' + msg)
                  return
                }
                if(j && j.message){
                  // immediately append the returned message to the UI for instant feedback
                  try{
                    // append only if not present
                    if(!messagesCache.find(x=>String(x.id) === String(j.message.id))){
                      messagesCache.push(j.message)
                    }
                    render(messagesCache)
                    // broadcast to other tabs
                    try{ if(bc) bc.postMessage(j.message) }catch(e){}
                  }catch(e){ console.error(e) }
                  // ensure we also refresh from server to get canonical state
                  poll()
                } else {
                  // server succeeded but didn't return the message payload; refresh from server to pick up canonical record
                  try{ poll() }catch(e){ console.warn('send succeeded but no message returned', j) }
                }
              }catch(e){ console.error('send exception', e); alert('Message send error') }
              finally{
                if(sendBtnEl){ sendBtnEl.disabled = false; sendBtnEl.classList.remove('opacity-60'); }
                try{ input.value = ''; if(fileInput) fileInput.value = '' }catch(e){}
              }
            }
          })()
        </script>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
