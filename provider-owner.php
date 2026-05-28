<?php
require __DIR__ . '/auth/db.php';
session_start();
// require login
if (empty($_SESSION['user_id'])) {
  header('Location: auth/login.php');
  exit;
}

// Load user record from DB
$user = null;
try {
    $stmt = $pdo->prepare('SELECT id, username, first_name, last_name, email, phone, status, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    $user = null;
}

if (!$user) {
  // fallback: simple session-derived profile
  $user = [
    'id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? '',
    'first_name' => $_SESSION['first_name'] ?? '',
    'last_name' => $_SESSION['last_name'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'phone' => $_SESSION['phone'] ?? '',
    'status' => $_SESSION['status'] ?? '',
    'created_at' => null,
  ];
}

$displayName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['username'] ?? 'Unnamed');
$initials = '';
if (!empty($user['first_name'])) $initials = strtoupper(substr($user['first_name'],0,1));
else if (!empty($user['username'])) $initials = strtoupper(substr($user['username'],0,1));
else $initials = 'U';

// Expose a small JSON blob to client-side
$currentUser = [
  'id' => $user['id'],
  'username' => $user['username'] ?? null,
  'first_name' => $user['first_name'] ?? null,
  'last_name' => $user['last_name'] ?? null,
  'email' => $user['email'] ?? null,
  'phone' => $user['phone'] ?? null,
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Provider Profile — Fixora (Owner View)</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-slate-50">
  <header class="site-header" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="header-inner flex items-center justify-between py-3">
        <div class="left flex items-center gap-3">
          <button id="mobileToggle" class="hamburger p-2 rounded-md md:hidden" aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <a id="siteLogo" href="index-signed.php" class="logo no-underline flex items-center gap-3">
            <svg class="logo-mark" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false">
              <defs>
                <linearGradient id="lg1" x1="0" x2="1">
                  <stop offset="0" stop-color="#6b21a8" />
                  <stop offset="0.5" stop-color="#4f46e5" />
                  <stop offset="1" stop-color="#06b6d4" />
                </linearGradient>
              </defs>
              <rect width="48" height="48" rx="10" fill="url(#lg1)" />
              <path d="M14 30c2-6 8-10 16-10" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
              <path d="M22 18v6h8" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="logo-word">Fixora</span>
          </a>
        </div>

        <nav class="nav-center hidden md:flex items-center gap-6 text-sm">
          <a href="post-request.php" class="nav-link">Post a Job</a>
          <a href="providers.php" class="nav-link">Providers</a>
          <a href="posted-jobs.php" class="nav-link">Posted Jobs</a>
        </nav>

        <div class="right flex items-center gap-3">
          <div id="authArea" class="flex items-center">
            <div id="profileMenu" class="relative">
              <button id="avatarBtn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 px-2 py-1 rounded focus:outline-none avatar-btn">
                <span id="avatarCircle" class="w-9 h-9 rounded-full avatar-ring bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-700"><?php echo htmlspecialchars($initials ?: 'U'); ?></span>
              </button>
              <div id="avatarDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                  <a href="provider-owner.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View profile</a>
                  <a href="provider-edit.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit profile</a>
                  <div class="border-t border-slate-100"></div>
                  <a href="auth/logout.php" class="w-full text-left block px-4 py-2 text-sm text-rose-600 hover:bg-slate-50">Sign out</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-6 pt-24">
    <section id="profileHeader" class="bg-white rounded-lg shadow mb-6 overflow-hidden">
      <div class="relative">
        <div id="cover" class="h-40 bg-gradient-to-r from-slate-200 to-slate-100"></div>
        <div class="p-6 pt-10 pl-44">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="-mt-12 flex-none">
                <div id="avatarWrap" class="w-28 h-28 rounded-full overflow-hidden bg-slate-100 drop-shadow-lg border-4 border-white flex items-center justify-center text-2xl text-slate-700 font-semibold">
                  <?php echo htmlspecialchars($initials ?: 'U'); ?>
                </div>
              </div>
              <div>
                <h1 id="p-name" class="text-2xl font-bold text-slate-900 leading-tight"><?php echo htmlspecialchars($displayName); ?></h1>
                <div id="p-tagline" class="text-sm text-slate-600 mt-1">Owner profile</div>
                <div id="p-meta" class="text-sm text-slate-500 mt-2"><?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <a id="contactBtn" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow-sm" href="mailto:<?php echo htmlspecialchars($user['email'] ?? ''); ?>">Contact</a>
              <button id="msgBtnInline" data-provider-id="<?php echo htmlspecialchars($user['id']); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-sm">Message</button>
              <button id="rateBtn" onclick="(function(){const p=new URLSearchParams(location.search).get('id')||''; const n=document.getElementById('p-name')?.textContent||''; if(window.fixora && window.fixora.openRatingModal) window.fixora.openRatingModal(p,n)})()" class="inline-flex items-center px-3 py-2 text-sm bg-yellow-100 text-amber-800 rounded hover:bg-yellow-200">Rate</button>
              <a id="editProfileBtn" class="inline-flex items-center px-3 py-2 text-sm text-slate-700 hover:underline" href="provider-edit.php">Edit profile</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="profileMain" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <aside class="lg:col-span-1 bg-white rounded-lg shadow p-4">
        <div class="text-center">
          <div id="p-badges" class="mt-2 space-x-2"></div>
          <div id="rating" class="mt-4 flex items-center justify-center gap-2"></div>
          <div id="p-rate" class="mt-3 font-medium text-slate-700"></div>
          <div id="p-location" class="mt-2 text-sm text-slate-600"></div>
          <div class="mt-4 text-sm text-slate-700">
            <div><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? '—'); ?></div>
            <div class="mt-1"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? '—'); ?></div>
            <div class="mt-1"><strong>Member since:</strong> <?php echo $user['created_at'] ? htmlspecialchars($user['created_at']) : '—'; ?></div>
            <div class="mt-1"><strong>Status:</strong> <?php echo htmlspecialchars($user['status'] ?? '—'); ?></div>
          </div>
        </div>
        <div id="certsSection" class="mt-6 text-sm text-slate-600"></div>
      </aside>

      <div class="lg:col-span-2">
        <div id="p-desc" class="bg-white rounded-lg shadow p-4 text-slate-700 leading-relaxed mb-6">
          <p class="text-sm text-slate-600">This is your account profile page. Use <a href="provider-edit.php" class="text-indigo-600 underline">Edit profile</a> to update your public information, services, and portfolio.</p>
        </div>

        <div id="timeline" class="space-y-4">
          <!-- Timeline items will be injected here -->
        </div>
      </div>
    </section>
  </main>

  <!-- Messages panel (two-column Messenger-style) -->
  <div id="messagesPanel" class="fixed inset-0 z-40 hidden items-stretch bg-black/40">
    <div class="mx-auto my-8 w-full max-w-6xl bg-white rounded-lg shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-4" style="height:80vh;">
      <!-- Left: conversations list -->
      <div class="col-span-1 md:col-span-1 border-r bg-slate-50 overflow-auto" id="messagesHeads" style="min-width:260px">
        <div class="p-4 border-b flex items-center justify-between">
          <strong>Messages</strong>
          <button id="closeMessagesModal" class="text-sm text-slate-600">Close</button>
        </div>
        <div id="headsList" class="p-2"></div>
      </div>
      <!-- Right: conversation area occupies remaining columns -->
      <div class="col-span-1 md:col-span-3 flex flex-col relative bg-white">
        <div class="p-4 border-b flex items-center justify-between">
          <div id="convHeader" class="text-sm font-medium">Select a conversation</div>
        </div>
        <div id="convMessages" class="flex-1 overflow-auto p-6 bg-gray-50"></div>
        <div class="p-4 border-t bg-white">
          <form id="convForm" class="flex gap-3" onsubmit="return false;">
            <input id="convInput" class="flex-1 rounded-full border px-4 py-3" placeholder="Write a message..." autocomplete="off">
            <button id="convSend" class="px-4 py-2 bg-indigo-600 text-white rounded-full">Send</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-white mt-8 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm text-slate-600 text-center">
      &copy; <span id="year"></span> Fixora — Connecting people with skilled local professionals.
    </div>
  </footer>

  <script>window.currentUser = <?php echo json_encode($currentUser, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;</script>
  <script src="js/include.js" defer></script>
  <script src="js/app.js" defer></script>
  <script>
  // small wiring for avatar dropdown on server-rendered header
  (function(){
    function wire(){
      const avatarBtn = document.getElementById('avatarBtn')
      const avatarDropdown = document.getElementById('avatarDropdown')
      if(!avatarBtn || !avatarDropdown) return
      avatarBtn.addEventListener('click', function(e){
        e.stopPropagation()
        const open = !avatarDropdown.classList.contains('open')
        avatarDropdown.classList.toggle('open', open)
        avatarDropdown.classList.toggle('hidden', !open)
        avatarBtn.setAttribute('aria-expanded', open)
      })
      document.addEventListener('click', function(e){
        if(e.target.closest && e.target.closest('#avatarDropdown')) return
        if(avatarDropdown.classList.contains('open')){
          avatarDropdown.classList.remove('open')
          avatarDropdown.classList.add('hidden')
          avatarBtn.setAttribute('aria-expanded','false')
        }
      })
    }
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', wire)
    else wire()
  })()
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Basic owner logic: prefer current user's profile if no id provided
      function qs(name){ const params = new URLSearchParams(window.location.search); return params.get(name) }
      const rawId = qs('id') || '';
      if(!rawId && window.currentUser && window.currentUser.id){
        // default to current user id so owner sees their own profile
        const sep = location.search ? '&' : '?'
        location.replace(location.pathname + location.search + sep + 'id=' + encodeURIComponent(window.currentUser.id))
        return
      }

      // Rest of client-side rendering (kept from original HTML)
      function normalizeName(n){ return String(n||'').toLowerCase().replace(/[^a-z0-9]+/g,' ').trim() }

      // Locate profile: try provider DB, then local providers, then local users, then current user
      let provider = null;
      const providers = (window.fixora && window.fixora.loadLocalProviders) ? window.fixora.loadLocalProviders() : (localStorage.getItem('fixora_providers') ? JSON.parse(localStorage.getItem('fixora_providers')) : []);
      if(rawId){ provider = providers.find(p => String(p.id) === String(rawId)) }

      if(!provider){
        const users = JSON.parse(localStorage.getItem('fixoria_users')||'[]')
        if(rawId){ provider = users.find(u => String(u.id) === String(rawId)) }
      }

      if(!provider){
        try{ const cur = window.currentUser || JSON.parse(localStorage.getItem('fixora_current_user')||'null'); if(cur && ( !rawId || String(cur.id) === String(rawId) )) provider = cur }catch(e){}
      }

      if(!provider){
        const headerArea = document.getElementById('profileHeader') || document.querySelector('main')
        if(headerArea){
          headerArea.innerHTML = `<div class="p-8 text-center"><p class="text-red-600">Profile not found.</p><p class="mt-3 text-sm text-slate-600">If you are the owner, <a class="text-blue-600 underline" href="provider-edit.php">create or edit your profile</a>.</p></div>`
        }
        const y = document.getElementById('year'); if(y) y.textContent = new Date().getFullYear();
        return;
      }

      const avatarWrap = document.getElementById('avatarWrap');
      avatarWrap.innerHTML = '';
      const firstName = provider.firstName || (provider.name || '').split(' ')[0] || ''
      const lastName = provider.lastName || (provider.name || '').split(' ').slice(1).join('') || ''
      const displayName = provider.name || ((firstName + ' ' + lastName).trim()) || 'Unnamed'
      if(provider.avatarDataUrl){ const img = document.createElement('img'); img.src = provider.avatarDataUrl; img.alt = displayName; img.className = 'w-full h-full object-contain p-1'; avatarWrap.appendChild(img); }
      else { const initials = ((firstName||'').charAt(0) + (lastName||'').charAt(0) || (displayName||'').charAt(0)).toUpperCase(); avatarWrap.innerHTML = `<div class="w-full h-full flex items-center justify-center text-2xl text-slate-300">${initials}</div>` }

      document.getElementById('p-name').textContent = displayName;
      document.getElementById('p-tagline').textContent = provider.tagline || '';
      document.getElementById('p-meta').textContent = [provider.primaryService || ''].filter(Boolean).join(' • ');
      const pLoc = document.getElementById('p-location'); if(pLoc) pLoc.textContent = '';
      document.getElementById('p-rate').textContent = provider.rate ? '£' + provider.rate + ' / hr' : '';
      document.getElementById('p-desc').textContent = provider.bio || 'No bio provided.';

      try{ if(window.fixora && window.fixora.renderRatingSummary) window.fixora.renderRatingSummary(provider.id) }catch(e){}

      const badges = document.getElementById('p-badges'); badges.innerHTML = '';
      if(provider.verified || provider.certs) {
        const b = document.createElement('span'); b.className = 'inline-flex items-center px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded-full'; b.textContent = 'Verified'; badges.appendChild(b);
      }

      const tags = document.getElementById('serviceTags'); if(tags) tags.innerHTML = '';
      if(provider.primaryService && tags) { const t = document.createElement('span'); t.className='px-3 py-1 bg-slate-100 text-slate-800 rounded-full text-sm'; t.textContent = provider.primaryService; tags.appendChild(t) }

      const timeline = document.getElementById('timeline'); timeline.innerHTML = '';

      const y = document.getElementById('year'); if(y) y.textContent = new Date().getFullYear();

      // Messages slide-over wiring
      const msgBtn = document.getElementById('msgBtnInline')
      const panel = document.getElementById('messagesPanel')
      const closeBtn = document.getElementById('closeMessagesModal')
      const headsList = document.getElementById('headsList')
      const convMessages = document.getElementById('convMessages')
      const convHeader = document.getElementById('convHeader')
      const convInput = document.getElementById('convInput')
      const convSend = document.getElementById('convSend')

      function openModal(){ panel.classList.remove('hidden'); panel.classList.add('flex') }
      function closeModal(){ panel.classList.add('hidden'); panel.classList.remove('flex'); headsList.innerHTML = ''; convMessages.innerHTML = ''; currentConversationKey = null }
      if(closeBtn) closeBtn.addEventListener('click', closeModal)

      let providerId = msgBtn ? msgBtn.getAttribute('data-provider-id') : (window.currentUser && window.currentUser.id)
      let allMessages = []
      let grouped = {}
      let currentConversationKey = null

      function threadKey(m){
        if(m.conversation_key) return 'conv:'+m.conversation_key
        if(m.sender_id && String(m.sender_id) !== String(providerId)) return 'sid:'+m.sender_id
        if(m.sender_email) return 'email:'+m.sender_email
        return 'name:'+ (m.sender_name||'guest')
      }

      function formatDate(ts){ try{ return new Date(ts).toLocaleString() }catch(e){ return ts }
      }

      function groupMessages(msgs){
        const g = {}
        msgs.forEach(m => {
          const k = threadKey(m)
          if(!g[k]) g[k] = { key:k, sender_name: null, sender_email: null, sender_id: null, messages: [] }
          g[k].messages.push(m)
        })
        // sort each group's messages by created_at and pick representative sender info
        Object.values(g).forEach(gr => {
          gr.messages.sort((a,b)=> new Date(a.created_at) - new Date(b.created_at))
          // prefer a message from the non-provider participant to determine display name/email
          let rep = gr.messages.find(m => !(m.sender_id && String(m.sender_id) === String(providerId))) || gr.messages[0]
          gr.sender_name = rep.sender_name || 'Guest'
          gr.sender_email = rep.sender_email || null
          gr.sender_id = (rep && rep.sender_id) ? rep.sender_id : null
        })
        return g
      }

      function renderHeads(){
        headsList.innerHTML = ''
        const groups = Object.values(grouped).sort((a,b)=> new Date(b.messages[b.messages.length-1].created_at) - new Date(a.messages[a.messages.length-1].created_at))
        if(!groups.length){ headsList.innerHTML = '<div class="p-4 text-slate-600">No messages yet.</div>'; return }
        groups.forEach(g => {
          const last = g.messages[g.messages.length-1]
          const unread = g.messages.filter(m => !m.read_at && String(m.sender_id||'') !== String(providerId)).length
          const btn = document.createElement('button')
          btn.type = 'button'
          btn.className = 'w-full text-left p-3 hover:bg-slate-50 border-b flex items-center'

          const left = document.createElement('div')
          left.className = 'flex-1'
          const nameEl = document.createElement('div')
          nameEl.className = 'font-medium'
          nameEl.textContent = g.sender_name || 'Guest'
          const previewEl = document.createElement('div')
          previewEl.className = 'text-xs text-slate-500 mt-1'
          previewEl.textContent = (last && last.body) ? (last.body.length>80 ? last.body.slice(0,80)+'...' : last.body) : ''
          left.appendChild(nameEl)
          left.appendChild(previewEl)

          const right = document.createElement('div')
          right.className = 'flex flex-col items-end ml-3'
          const timeEl = document.createElement('div')
          timeEl.className = 'text-xs text-slate-400'
          timeEl.textContent = formatDate(last && last.created_at ? last.created_at : '')
          right.appendChild(timeEl)
          if(unread){ const badge = document.createElement('div'); badge.className='mt-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-rose-100 text-rose-700'; badge.textContent = unread > 99 ? '99+' : String(unread); right.appendChild(badge) }

          btn.appendChild(left)
          btn.appendChild(right)
          btn.addEventListener('click', function(){ openConversation(g.key) })
          headsList.appendChild(btn)
        })
      }

      function escapeHtml(s){ return String(s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;") }

      function renderConversation(key){
        convMessages.innerHTML = ''
        const g = grouped[key]
        if(!g) return
        convHeader.textContent = g.sender_name + (g.sender_email?(' • '+g.sender_email):'')
        // render as continuous message list
        g.messages.forEach(m => {
          const isMe = String(m.sender_id) === String(providerId)
          const msgWrap = document.createElement('div')
          msgWrap.className = 'my-3 flex ' + (isMe ? 'justify-end' : 'justify-start')
          const bubble = document.createElement('div')
          bubble.className = 'px-4 py-2 rounded-xl max-w-[75%] whitespace-pre-wrap'
          bubble.style.background = isMe ? '#4f46e5' : '#f1f5f9'
          bubble.style.color = isMe ? '#fff' : '#0f172a'
          bubble.innerHTML = `<div class="text-sm">${escapeHtml(m.body)}</div><div class="text-xs opacity-60 mt-1 text-right">${formatDate(m.created_at)}</div>`
          msgWrap.appendChild(bubble)
          convMessages.appendChild(msgWrap)
        })
        // auto-scroll to bottom
        convMessages.scrollTop = convMessages.scrollHeight
      }

      async function fetchMessages(){
        try{
          const res = await fetch('./auth/messages_api.php?action=fetch&provider_id='+encodeURIComponent(providerId), { credentials: 'same-origin' })
          if(!res.ok) return []
          const j = await res.json(); return j.messages || []
        }catch(e){ console.error(e); return [] }
      }

      async function openConversation(key){
        currentConversationKey = key
        // render from grouped messages
        renderConversation(key)
        // mark messages in this conversation as read
        const g = grouped[key]
        if(!g) return
        for(const m of g.messages){ if(!m.read_at){ try{ await fetch('./auth/messages_api.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'mark_read', provider_id: providerId, message_id: m.id }) }) }catch(e){ } } }
        // refresh messages to update unread badge
        allMessages = await fetchMessages(); grouped = groupMessages(allMessages); renderHeads(); renderConversation(key)
      }

      convSend.addEventListener('click', async function(){
        const text = (convInput.value||'').trim(); if(!text || !currentConversationKey) return; convInput.value = ''
        // optimistic append
        const temp = { id: 'temp-'+Date.now(), provider_id: providerId, sender_id: providerId, sender_name: window.currentUser && (window.currentUser.first_name||window.currentUser.username) || 'You', body: text, attachment: null, created_at: (new Date()).toISOString() }
        grouped[currentConversationKey].messages.push(temp); renderConversation(currentConversationKey)
        try{
          // compute a clean conversation_key value to send to server
          let convPayload = null
          if(currentConversationKey){
            if(currentConversationKey.startsWith('conv:')) convPayload = currentConversationKey.slice(5)
            else if(currentConversationKey.startsWith('email:') || currentConversationKey.startsWith('name:')) convPayload = currentConversationKey
            else convPayload = null
          }
          const payload = { action:'send', provider_id: providerId, body: text, sender_id: providerId, sender_name: (window.currentUser && (window.currentUser.first_name||window.currentUser.username)), conversation_key: convPayload }
          const res = await fetch('./auth/messages_api.php', { method:'POST', credentials: 'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
          const j = await res.json()
          if(res.ok && j && j.message){
            // replace temp with real message
            const msgs = grouped[currentConversationKey].messages
            for(let i=0;i<msgs.length;i++){ if(msgs[i].id === temp.id){ msgs[i] = j.message; break } }
            // refresh all messages
            allMessages = await fetchMessages(); grouped = groupMessages(allMessages); renderHeads(); renderConversation(currentConversationKey)
            try{ if(bc) bc.postMessage(j.message) }catch(e){}
          } else if(res.ok){
            // no message returned; refresh to pick up server record
            allMessages = await fetchMessages(); grouped = groupMessages(allMessages); renderHeads(); renderConversation(currentConversationKey)
          }
        }catch(e){ console.error(e); try{ allMessages = await fetchMessages(); grouped = groupMessages(allMessages); renderHeads(); renderConversation(currentConversationKey) }catch(_){} }
      })

        // Open messages panel and load conversations
        if(msgBtn){ msgBtn.addEventListener('click', async function(){ providerId = msgBtn.getAttribute('data-provider-id') || providerId; openModal(); allMessages = await fetchMessages(); grouped = groupMessages(allMessages); renderHeads(); // auto-select first conversation
          const first = Object.keys(grouped)[0]; if(first) { currentConversationKey = first; renderConversation(first) }
        }) }

        // Real-time: BroadcastChannel and polling
        let bc = null
        try{ if('BroadcastChannel' in window) bc = new BroadcastChannel('fixora_messages') }catch(e){ bc = null }
        if(bc){ bc.addEventListener('message', async function(ev){ try{ const m = ev.data; if(!m || String(m.provider_id) !== String(providerId)) return; allMessages.push(m); grouped = groupMessages(allMessages); renderHeads(); if(currentConversationKey && (m.conversation_key && ('conv:'+m.conversation_key) === currentConversationKey || (m.sender_id && ('sid:'+m.sender_id) === currentConversationKey) || (m.sender_email && ('email:'+m.sender_email) === currentConversationKey))){ renderConversation(currentConversationKey) } }catch(e){}}) }
        // periodic poll to catch missed messages
        setInterval(async ()=>{ try{ const msgs = await fetchMessages(); if(msgs && msgs.length){ allMessages = msgs; grouped = groupMessages(allMessages); renderHeads(); if(currentConversationKey) renderConversation(currentConversationKey) } }catch(e){} }, 2500)


      // If owner, show Edit link to edit their profile
      const editBtn = document.getElementById('editProfileBtn');
      if(window.currentUser && String(window.currentUser.id) === String(provider.id)){
        if(editBtn) editBtn.href = 'provider-edit.php';
      } else {
        if(editBtn) editBtn.style.display = 'none';
      }

    })
  </script>
</body>
</html>
