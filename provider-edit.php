<?php
require __DIR__ . '/auth/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: auth/login.php');
  exit;
}
// Load minimal user info
$user = null;
try {
  $stmt = $pdo->prepare('SELECT id, username, first_name, last_name, email, phone FROM users WHERE id = :id LIMIT 1');
  $stmt->execute(['id' => $_SESSION['user_id']]);
  $user = $stmt->fetch();
} catch (Exception $e) {
  $user = null;
}
if (!$user) {
  $user = [
    'id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? '',
    'first_name' => $_SESSION['first_name'] ?? '',
    'last_name' => $_SESSION['last_name'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'phone' => $_SESSION['phone'] ?? '',
  ];
}
$displayName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['username'] ?? '');
$initial = !empty($user['first_name']) ? strtoupper(substr($user['first_name'],0,1)) : (!empty($user['username']) ? strtoupper(substr($user['username'],0,1)) : 'U');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Edit Provider Profile — Fixora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body class="bg-slate-50 text-slate-800">
    <!-- server-rendered signed-in header -->
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
                  <span id="avatarCircle" class="w-9 h-9 rounded-full avatar-ring bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-700"><?php echo htmlspecialchars($initial); ?></span>
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

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="text-2xl font-semibold mb-4">Edit Provider Profile</h1>
      <p class="text-slate-600 mb-6">Add your professional details, upload a profile picture, and specify services you offer. This demo saves data in your browser unless your deployment implements server endpoints.</p>

      <form id="providerForm" class="space-y-6 bg-white p-6 rounded-lg shadow">
        <div class="flex items-center gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700">Profile photo</label>
            <div class="mt-2 flex items-center">
              <span id="avatarPreview" class="inline-block h-20 w-20 rounded-full overflow-hidden bg-slate-100 flex items-center justify-center text-xl text-slate-700">
                <!-- server-side initial -->
                <?php echo htmlspecialchars($initial); ?>
              </span>
              <div class="ml-4">
                <input id="avatar" type="file" accept="image/*" />
                <p class="text-sm text-slate-500 mt-2">Max 2MB. PNG or JPG.</p>
              </div>
            </div>
          </div>

          <div class="flex-1">
            <label class="block text-sm font-medium text-slate-700">Display name</label>
            <input id="name" required class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="Your business or full name" value="<?php echo htmlspecialchars($displayName); ?>" />

            <label class="block text-sm font-medium text-slate-700 mt-4">Tagline</label>
            <input id="tagline" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="e.g. Trusted plumber — 10+ years experience" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Biography / About</label>
          <textarea id="bio" rows="4" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="Describe your services, experience, and areas served."></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Primary service</label>
            <select id="primaryService" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2">
              <option>Plumbing</option>
              <option>Electrical</option>
              <option>Carpentry</option>
              <option>Painting</option>
              <option>Handyman</option>
              <option>Design</option>
              <option>Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Rate (optional)</label>
            <input id="rate" type="number" min="0" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="e.g. 40" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Location (city / postcode)</label>
          <input id="location" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="e.g. London, SE1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Contact email</label>
            <input id="contactEmail" type="email" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="you@business.com" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Phone (optional)</label>
            <input id="contactPhone" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="+44 7123 456789" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Portfolio links (comma separated)</label>
          <input id="portfolio" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="https://example.com, https://instagram.com/you" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Certifications / Verification notes</label>
          <input id="certs" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="Gas Safe #12345, DBS checked" />
        </div>

        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-500">Your profile will be saved to your browser for this demo. For production, server persistence is required.</div>
          <div class="space-x-2">
            <button type="button" id="deleteProfile" class="px-3 py-2 bg-red-50 text-red-600 rounded">Delete</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save profile</button>
          </div>
        </div>
      </form>
    </main>

    <script>window.currentUser = <?php echo json_encode($user, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;</script>
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
      // Provider edit script adapted to use server-provided currentUser as fallback
      document.addEventListener('DOMContentLoaded', function(){
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');
        const form = document.getElementById('providerForm');

        function getCurrentUserLocal(){ try{ const raw = localStorage.getItem('fixora_current_user'); return raw ? JSON.parse(raw) : null }catch(e){ return null } }
        const params = new URLSearchParams(window.location.search);
        const idParam = params.get('id') || null;
        const currentUser = window.currentUser || getCurrentUserLocal();
        let provider = null;
        if(idParam && window.fixora && window.fixora.getProviderById){ provider = window.fixora.getProviderById(idParam) }
        if(!provider){
          if(currentUser && window.fixora && window.fixora.getProviderById) provider = window.fixora.getProviderById(currentUser.id)
          if(!provider && window.fixora && window.fixora.loadLocalProviders){
            const list = window.fixora.loadLocalProviders();
            provider = list.find(p=> String(p.contactEmail || '') === String(currentUser.email || currentUser.contactEmail || '') || String(p.contactPhone || '') === String(currentUser.phone || currentUser.contactPhone || '')) || null
          }
        }
        if(!provider){
          const baseId = (currentUser && currentUser.id) ? currentUser.id : (idParam || (Date.now().toString(36)+Math.random().toString(36).slice(2,8)));
          provider = { id: baseId, name: currentUser.name || ((currentUser.first_name || '') + ' ' + (currentUser.last_name || '')).trim() || '', tagline:'', bio:'', primaryService:'Plumbing', rate:null, location:'', contactEmail:currentUser.email || '', contactPhone:currentUser.phone || '', portfolio:[], certs:'', avatarDataUrl:currentUser.avatarDataUrl || null }
        }

        function renderAvatar(){
          avatarPreview.innerHTML = '';
          if(provider.avatarDataUrl){
            const img = document.createElement('img'); img.src = provider.avatarDataUrl; img.alt = 'Profile photo'; img.className = 'w-20 h-20 object-contain p-1';
            avatarPreview.appendChild(img);
          } else {
            avatarPreview.textContent = (provider.name||'').split(' ').map(s=>s[0]).slice(0,1).join('') || '<?php echo htmlspecialchars($initial); ?>';
          }
        }

        // populate fields
        document.getElementById('name').value = provider.name || '';
        document.getElementById('tagline').value = provider.tagline || '';
        document.getElementById('bio').value = provider.bio || '';
        document.getElementById('primaryService').value = provider.primaryService || 'Plumbing';
        document.getElementById('rate').value = provider.rate || '';
        document.getElementById('location').value = provider.location || '';
        document.getElementById('contactEmail').value = provider.contactEmail || '';
        document.getElementById('contactPhone').value = provider.contactPhone || '';
        document.getElementById('portfolio').value = (provider.portfolio || []).join(', ');
        document.getElementById('certs').value = provider.certs || '';
        renderAvatar();

        avatarInput.addEventListener('change', function(){
          const f = this.files && this.files[0];
          if(!f) return;
          if(f.size > 2 * 1024 * 1024){ alert('File too large (max 2MB)'); return }
          const reader = new FileReader();
          reader.onload = function(evt){ provider.avatarDataUrl = evt.target.result; renderAvatar(); }
          reader.readAsDataURL(f);
        });

        form.addEventListener('submit', function(e){
          e.preventDefault();
          provider.name = document.getElementById('name').value.trim();
          provider.tagline = document.getElementById('tagline').value.trim();
          provider.bio = document.getElementById('bio').value.trim();
          provider.primaryService = document.getElementById('primaryService').value;
          provider.rate = document.getElementById('rate').value ? Number(document.getElementById('rate').value) : null;
          provider.location = document.getElementById('location').value.trim();
          provider.contactEmail = document.getElementById('contactEmail').value.trim();
          provider.contactPhone = document.getElementById('contactPhone').value.trim();
          provider.portfolio = document.getElementById('portfolio').value.split(',').map(s=>s.trim()).filter(Boolean);
          provider.certs = document.getElementById('certs').value.trim();

          if(!provider.name){ alert('Please enter a display name.'); return }
          if(!provider.contactEmail){ if(!confirm('No contact email provided — continue?')) return }

          const cur = currentUser;
          if(cur && cur.id){ provider.id = cur.id }

          try{
            if(window.fixora && window.fixora.saveProvider){ window.fixora.saveProvider(provider); }
            else {
              const key = 'fixora_providers';
              const raw = localStorage.getItem(key); const list = raw ? JSON.parse(raw) : [];
              let idx = list.findIndex(p=> String(p.id) === String(provider.id));
              if(idx === -1){ idx = list.findIndex(p=> String(p.contactEmail || '') === String(provider.contactEmail || '') || String(p.contactPhone || '') === String(provider.contactPhone || '')) }
              if(idx === -1) list.push(provider); else list[idx] = provider;
              localStorage.setItem(key, JSON.stringify(list));
            }

            if(cur){
              const updated = Object.assign({}, cur);
              updated.name = provider.name || updated.name;
              if(provider.avatarDataUrl) updated.avatarDataUrl = provider.avatarDataUrl;
              if(provider.contactPhone) updated.phone = provider.contactPhone;
              try{ if(window.fixora && window.fixora.setCurrentUser) window.fixora.setCurrentUser(updated); else { localStorage.setItem('fixora_current_user', JSON.stringify(updated)); window.fixora.currentUser = updated } }catch(e){}
              const avatarCircle = document.getElementById('avatarCircle') || document.querySelector('.header-avatar')
              if(avatarCircle){ if(updated.avatarDataUrl){ avatarCircle.innerHTML = '<img src="'+updated.avatarDataUrl+'" alt="avatar" class="w-8 h-8 object-contain rounded-full">' } else { avatarCircle.textContent = (updated.name||'').split(' ').map(s=>s[0]).slice(0,1).join('') || 'U' } }
              try{
                if(cur.id && typeof fetch === 'function'){
                  fetch('/api/users/' + encodeURIComponent(cur.id), { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ name: provider.name, avatarDataUrl: provider.avatarDataUrl, phone: provider.contactPhone, bio: provider.bio }) }).catch(()=>{})
                }
              }catch(e){}
            }

            alert('Profile saved locally. You can view it now.');
            window.location.href = `provider.html?id=${encodeURIComponent(provider.id)}`;
          }catch(e){ console.error('save provider', e); alert('Failed to save'); return }
        });

        document.getElementById('deleteProfile').addEventListener('click', function(){
          if(!confirm('Delete this profile from this browser?')) return;
          const key = 'fixora_providers';
          try{
            const raw = localStorage.getItem(key); const list = raw ? JSON.parse(raw) : [];
            const idx = list.findIndex(p=> String(p.id) === String(provider.id));
            if(idx !== -1) { list.splice(idx,1); localStorage.setItem(key, JSON.stringify(list)); }
            alert('Profile deleted');
            window.location.href = 'index.html';
          }catch(e){ console.error(e); alert('Failed to delete'); }
        });

      });
    </script>
  </body>
</html>
