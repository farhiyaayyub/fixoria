<?php
require __DIR__ . '/auth/db.php';
session_start();
$first = $_SESSION['first_name'] ?? '';
$username = $_SESSION['username'] ?? '';
$initial = $first ? strtoupper(substr($first,0,1)) : ($username ? strtoupper(substr($username,0,1)) : 'U');
$authenticated = !empty($_SESSION['user_id']);

// fetch providers (users) from DB — if a search query is provided, filter by it
$providers = [];
try{
    $q = trim((string)($_GET['q'] ?? ''));
    if($q !== ''){
      // search by first/last/username/email
      $like = '%' . str_replace('%','\\%',$q) . '%';
      $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, created_at FROM users WHERE (first_name LIKE :q OR last_name LIKE :q OR username LIKE :q OR email LIKE :q) ORDER BY created_at DESC LIMIT 200");
      $stmt->execute(['q' => $like]);
      $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $stmt = $pdo->query("SELECT id, username, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 200");
      $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}catch(Exception $e){
    // leave providers empty on error
}
// If DB returned no providers but a user is signed in, expose that user as a provider card so UI isn't empty
if(empty($providers) && !empty($_SESSION['user_id'])){
  $providers[] = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'] ?? '',
    'first_name' => $_SESSION['first_name'] ?? '',
    'last_name' => $_SESSION['last_name'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'created_at' => date('Y-m-d')
  ];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Providers — Fixora</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" onload="this.rel='stylesheet'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
  <meta name="description" content="Find local providers and browse services — Fixora connects you with vetted professionals." />
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="antialiased bg-gradient-to-b from-slate-50 via-white to-slate-50 text-slate-800">
  <!-- server-rendered header -->
  <header class="site-header" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="header-inner flex items-center justify-between py-3">
        <div class="left flex items-center gap-3">
          <button id="mobileToggle" class="hamburger p-2 rounded-md md:hidden" aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <a id="siteLogo" href="index.php" class="logo no-underline flex items-center gap-3">
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
          <?php if ($authenticated): ?>
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
          <?php else: ?>
            <div id="authArea" class="flex items-center gap-2">
              <a id="linkSignIn" href="login.html" class="guest-link">Sign in</a>
              <a id="linkSignUp" href="signup.html" class="cta-signup" aria-label="Create an account">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Sign up</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <section class="hero relative isolate">
    <div class="absolute -top-24 left-1/2 -z-10 transform -translate-x-1/2 blur-3xl opacity-60 pointer-events-none" aria-hidden="true">
      <div class="w-[680px] h-[420px] rounded-[40%] bg-gradient-to-tr from-indigo-400 via-blue-400 to-emerald-300 animate-blob mix-blend-multiply" style="filter:blur(60px);"></div>
    </div>
    <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
      <h1 class="text-4xl font-extrabold">Browse Providers</h1>
      <p class="lead mt-3">Find local professionals for repairs, design, tutoring and more.</p>
      <div class="mt-6"><a href="post-request.php" class="btn primary">Post a Job</a></div>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold">All Providers</h1>
      <p class="text-slate-600 mt-1">Browse all providers. Use the search and filter to narrow results.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-8">
      <form id="searchForm" class="flex gap-2" action="providers.php" method="get" onsubmit="event.preventDefault(); if(window.fixora && typeof window.fixora.fetchAndRenderServer === 'function'){ window.fixora.fetchAndRenderServer(document.getElementById('searchInput')?.value || '') }">
        <input id="searchInput" name="q" class="flex-1 rounded border border-slate-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" type="search" placeholder="Search providers, e.g. plumber, logo design" aria-label="Search providers" />
        <select id="serviceFilter" class="rounded border border-slate-200 px-3 py-2" aria-label="Filter by service">
          <option value="all">All services</option>
          <option value="repair">Repair & Maintenance</option>
          <option value="design">Design</option>
          <option value="tutor">Tutoring</option>
          <option value="delivery">Local Services</option>
        </select>
        <button type="button" id="providersSearchBtn" class="px-4 py-2 bg-blue-600 text-white rounded" onclick="(function(){ if(window.fixora && typeof window.fixora.fetchAndRenderServer === 'function'){ window.fixora.fetchAndRenderServer(document.getElementById('searchInput')?.value || '') } })()">Search</button>
      </form>
      <?php if($authenticated):
        $displayName = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?: ($_SESSION['username'] ?? 'You');
        $email = $_SESSION['email'] ?? '';
        $uid = htmlspecialchars($_SESSION['user_id']);
        $initials = strtoupper(substr(trim($displayName),0,1));
      ?>
      <div id="profileCardWrap" class="mt-4" data-server-init="1">
        <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-lg font-bold text-slate-700"><?php echo htmlspecialchars($initials); ?></div>
          <div class="flex-1">
            <div class="text-sm text-slate-500">Signed in as</div>
            <div class="font-semibold"><?php echo htmlspecialchars($displayName); ?></div>
            <div class="text-xs text-slate-500"><?php echo htmlspecialchars($email); ?></div>
          </div>
          <div class="text-right">
            <a href="provider-owner.php?id=<?php echo urlencode($uid); ?>" class="inline-flex px-3 py-2 bg-blue-50 text-blue-700 rounded text-sm">Profile</a>
          </div>
        </div>
      </div>
      <?php else: ?>
      <div id="profileCardWrap" class="mt-4"></div>
      <?php endif; ?>
    </div>

    <section id="providersSection">
      <div id="providerList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php if(!empty($providers)): ?>
  <?php foreach($providers as $p):
    $display = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? ($p['name'] ?? 'Unnamed'));
    $memberSince = $p['created_at'] ? date('Y-m-d', strtotime($p['created_at'])) : '';
  ?>
          <?php
            $dataName = htmlspecialchars($display);
            $dataService = htmlspecialchars($p['primaryService'] ?? 'general');
          ?>
          <article class="card bg-white rounded-xl shadow-lg p-6 flex flex-col items-start gap-4" data-provider-id="<?php echo htmlspecialchars($p['id']); ?>" data-provider-email="<?php echo htmlspecialchars($p['email'] ?? ''); ?>" data-name="<?php echo $dataName; ?>" data-service="<?php echo $dataService; ?>">
            <div class="w-full flex items-center gap-4">
              <?php
                $email = trim(strtolower($p['email'] ?? ''));
                $gravatar = '';
                if($email){ $gravatar = 'https://www.gravatar.com/avatar/' . md5($email) . '?s=160&d=identicon'; }
                $initials = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? ($p['name'] ?? 'U'));
                $initialLetter = strtoupper(substr(trim($initials),0,1));
              ?>
              <div class="flex-none">
                <?php if($gravatar): ?>
                  <img src="<?php echo $gravatar; ?>" alt="<?php echo htmlspecialchars($display); ?>" class="w-20 h-20 rounded-full object-cover border" />
                <?php else: ?>
                  <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-xl font-bold text-slate-700"><?php echo htmlspecialchars($initialLetter); ?></div>
                <?php endif; ?>
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-semibold mb-1"><?php echo htmlspecialchars($display); ?></h3>
                <div class="text-sm text-slate-500">Member since <?php echo htmlspecialchars($memberSince); ?></div>
                <div class="mt-2 text-sm text-slate-700"><?php echo htmlspecialchars($p['email'] ?? ''); ?></div>
              </div>
            </div>
            <div class="w-full flex items-center justify-between pt-3">
              <div class="flex items-center gap-2">
                <a class="inline-flex px-4 py-2 bg-blue-50 text-blue-700 rounded-md shadow-sm hover:bg-blue-100" href="provider.html?id=<?php echo urlencode($p['id']); ?>">View profile</a>
                <button class="messageBtn inline-flex px-4 py-2 bg-emerald-50 text-emerald-700 rounded-md shadow-sm hover:bg-emerald-100" data-id="<?php echo htmlspecialchars($p['id']); ?>">Message</button>
              </div>
              <div class="text-sm text-slate-500">Service: <strong class="text-slate-700">General</strong></div>
            </div>
          </article>
  <?php endforeach; ?>
  <?php else: ?>
          <div class="text-slate-500">No providers found.</div>
  <?php endif; ?>
      </div>

      <p id="noResults" class="no-results mt-4 text-sm text-slate-500" hidden>No providers found — try a different search.</p>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <p>&copy; <span id="year"></span> Fixora — Connecting people with skilled local professionals.</p>
    </div>
  </footer>

  <script src="js/header.js" defer></script>
  <script>window._fixora_disableProviderClient = true;</script>
  <script src="js/app.js" defer></script>
  <script src="js/index-ui.js" defer></script>
  <script>
    // expose current session user to client-side
    window.currentUser = <?php echo json_encode([ 'id' => $_SESSION['user_id'] ?? null, 'username' => $_SESSION['username'] ?? null, 'first_name' => $_SESSION['first_name'] ?? null, 'last_name' => $_SESSION['last_name'] ?? null, 'email' => $_SESSION['email'] ?? null ], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

    try{ if(window.currentUser && window.currentUser.id){ localStorage.setItem('fixora_current_user', JSON.stringify(window.currentUser)) } }catch(e){}

    document.addEventListener('DOMContentLoaded', function(){
      // wire message buttons
      function openMessage(providerId, providerName, providerEmail){
        try{
          if(window.fixora && typeof window.fixora.openMessageModal === 'function'){
            window.fixora.openMessageModal(providerId, providerName);
            return;
          }
        }catch(e){ /* ignore */ }
        // If in-page modal not available, open the PHP messenger interface
        const url = 'messages.php?provider_id=' + encodeURIComponent(providerId);
        window.location.href = url;
      }

      document.querySelectorAll('.messageBtn').forEach(function(btn){
        btn.addEventListener('click', function(){
          const pid = this.getAttribute('data-id')
          const card = this.closest('.card')
          const name = card ? (card.querySelector('h3')?.textContent || '') : ''
          const email = card ? card.getAttribute('data-provider-email') : ''
          openMessage(pid, name, email)
        })
      })

      // Ensure provider cards remain visible — some client scripts may hide them.
      (function keepCardsVisible(){
        const list = document.getElementById('providerList')
        if(!list) return
        function restoreNode(n){
          try{
            if(n.nodeType !== 1) return
            if(n.classList && n.classList.contains('card')) n.style.display = ''
            // also clear inline display on any child cards
            n.querySelectorAll && n.querySelectorAll('.card').forEach(c=> c.style.display = '')
          }catch(e){}
        }
        // restore existing cards immediately
        list.querySelectorAll('.card').forEach(c=> c.style.display = '')
        // observe for any future changes and revert display changes
        const mo = new MutationObserver(muts => {
          muts.forEach(m => {
            if(m.type === 'attributes' && m.attributeName === 'style') restoreNode(m.target)
            if(m.addedNodes && m.addedNodes.length) m.addedNodes.forEach(restoreNode)
          })
        })
        mo.observe(list, { attributes: true, attributeFilter: ['style'], childList: true, subtree: true })
      })()
    })
  </script>
  <script>
    // expose the server-rendered provider data so client can rehydrate if DOM is cleared
    window.serverProviders = <?php echo json_encode(array_values($providers ?: []), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  </script>
</body>
</html>
