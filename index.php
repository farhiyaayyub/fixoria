<?php
session_start();
$first = $_SESSION['first_name'] ?? '';
$username = $_SESSION['username'] ?? '';
$initial = $first ? strtoupper(substr($first,0,1)) : ($username ? strtoupper(substr($username,0,1)) : 'U');
$authenticated = !empty($_SESSION['user_id']);
?>
<?php
// provide a small server-side providers payload so client can rehydrate and search names
try{
  require __DIR__ . '/auth/db.php';
  $providers = [];
  $stmt = $pdo->query("SELECT id, username, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 200");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($rows as $p){
    $display = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? 'Unnamed');
    $providers[] = [
      'id' => $p['id'],
      'name' => $display,
      'email' => $p['email'] ?? '',
      'created_at' => $p['created_at'] ?? null
    ];
  }
}catch(Exception $e){
  $providers = [];
}
// If a user is signed in but not present in the fetched list, ensure they appear so homepage search can find them
try{
  if(!empty($_SESSION['user_id'])){
    $found = false;
    foreach($providers as $pp){ if(isset($pp['id']) && strval($pp['id']) === strval($_SESSION['user_id'])){ $found = true; break; } }
    if(!$found){
      $uid = intval($_SESSION['user_id']);
      $stmt2 = $pdo->prepare("SELECT id, username, first_name, last_name, email, created_at FROM users WHERE id = :id LIMIT 1");
      $stmt2->execute(['id' => $uid]);
      $u = $stmt2->fetch(PDO::FETCH_ASSOC);
      if($u){
        $display = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: ($u['username'] ?? 'Unnamed');
        $providers[] = [ 'id' => $u['id'], 'name' => $display, 'email' => $u['email'] ?? '', 'created_at' => $u['created_at'] ?? null ];
      }
    }
  }
}catch(Exception $e){ /* ignore */ }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fixora — Find Local Experts & Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
    <meta name="description" content="Fixora connects skilled individuals (freelancers, repair specialists, designers, tutors, and local service providers) with people and small businesses who need help." />
    <link rel="stylesheet" href="css/styles.css">
    <style>
      /* Refined page-scoped header preview using the uploaded image */
      .site-header.hero{
        background-image: url('beautiful%20header.png');
        background-size: cover; background-position: center; position:relative; color: #fff;
        min-height: 220px; display:block; padding-top:0; padding-bottom:0; overflow:hidden;
      }
      .site-header.hero::before{ content:''; position:absolute; left:0;top:0;right:0;bottom:0;
        /* use CSS variable for header tint (defaults to a pleasant blue) */
        background: linear-gradient(180deg, rgba(var(--header-rgb,59,130,246),0.68) 0%, rgba(var(--header-rgb,59,130,246),0.36) 40%, rgba(var(--header-rgb,59,130,246),0.14) 100%); z-index:0 }
      .site-header.hero .header-inner{ position:relative; z-index:2; padding-top:28px; padding-bottom:28px }
      .site-header.hero .left{ display:flex; align-items:center }
      .site-header.hero .logo-mark{ filter: drop-shadow(0 6px 18px rgba(3,7,18,0.45)); }
      .site-header.hero .logo-word{ color:#fff; font-weight:800; font-size:1.05rem }
      .site-header.hero .nav-center{ gap:18px }
      .site-header.hero .nav-center a{ color: rgba(255,255,255,0.93); padding:8px 10px; border-radius:8px; transition:background .18s, transform .12s }
      .site-header.hero .nav-center a:hover{ background: rgba(255,255,255,0.06); transform:translateY(-1px) }
      .site-header.hero .guest-link{ color: rgba(255,255,255,0.92); margin-right:8px }
      .site-header.hero .cta-signup{ background: #fff; color: #0f172a; padding:8px 12px; border-radius:999px; font-weight:600 }
      /* make header feel taller on large screens */
      @media(min-width:1024px){ .site-header.hero{ min-height:260px } .site-header.hero .header-inner{ padding-top:36px; padding-bottom:36px } }
    </style>
  </head>
  <body class="antialiased bg-gradient-to-b from-slate-50 via-white to-slate-50 text-slate-800">
    <!-- header copied from partials/header.html -->
    <header class="site-header" role="banner">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="header-inner flex items-center justify-between py-3">
          <!-- Left: mobile toggle + logo -->
          <div class="left flex items-center gap-3">
            <button id="mobileToggle" class="hamburger p-2 rounded-md md:hidden" aria-label="Toggle menu">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <a id="siteLogo" href="index.html" class="logo no-underline flex items-center gap-3">
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

          <!-- Center: primary nav (hidden on small screens) -->
          <nav class="nav-center hidden md:flex items-center gap-6 text-sm">
            <a href="post-request.php" class="nav-link">Post a Job</a>
            <a href="providers.php" class="nav-link">Providers</a>
            <a href="posted-jobs.php" class="nav-link">Posted Jobs</a>
          </nav>

          <!-- Right: auth (signin, signup, profile) -->
          <div class="right flex items-center gap-3">
            <div id="authArea" class="flex items-center gap-3">
              <a id="linkSignIn" href="login.html" class="guest-link">Sign in</a>
              <a id="linkSignUp" href="signup.html" class="cta-signup" aria-label="Create an account">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Sign up</span>
              </a>

              <div id="profileMenu" class="hidden relative">
                <button id="avatarBtn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 px-2 py-1 rounded focus:outline-none avatar-btn">
                  <span id="avatarCircle" class="w-9 h-9 rounded-full avatar-ring bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-700">U</span>
                </button>
                <div id="avatarDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded shadow-lg ring-1 ring-black ring-opacity-5">
                  <div class="py-1">
                    <a id="menuView" href="provider-owner.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View profile</a>
                    <a id="menuEdit" href="provider-edit.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit profile</a>
                    <a id="menuSettings" href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
                    <div class="border-t border-slate-100"></div>
                    <a id="menuSignOut" href="auth/logout.php" class="w-full text-left block px-4 py-2 text-sm text-rose-600 hover:bg-slate-50">Sign out</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>


    <!-- Hero -->
    <main class="relative overflow-hidden">
      <section class="relative isolate bg-white">
        <div class="absolute -top-24 left-1/2 -z-10 transform -translate-x-1/2 blur-3xl opacity-60">
          <div class="w-[680px] h-[420px] bg-gradient-to-tr from-indigo-400 via-blue-400 to-emerald-300 rounded-[40%] animate-blob mix-blend-multiply" style="filter:blur(60px);"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
              <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight gradient-heading">Find trusted local experts — fast, fair, and verified</h1>
              <p class="text-lg text-slate-600 max-w-xl">Fixora connects you with local professionals for repairs, design, tutoring, and everyday services. Read reviews, compare prices, and hire confidently.</p>
              <div class="flex flex-wrap gap-3 items-center">
                <form id="searchForm" class="flex w-full sm:w-auto gap-2" role="search" aria-label="Search providers">
                  <input id="searchInput" name="q" class="w-full sm:w-[420px] rounded-full border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm" type="search" placeholder="Search services, e.g. plumber, logo design" />
                  <button type="button" id="searchBtn" class="rounded-full px-5 py-3 bg-indigo-600 text-white shadow hover:scale-[1.02] transition-transform">Search</button>
                </form>
                  <div id="profileCardWrap" class="mt-4"></div>
                <a href="auth/signup.php" class="hidden sm:inline-flex items-center px-4 py-3 border rounded-full text-sm font-medium hover:bg-slate-50">Create account</a>
              </div>

              <div class="mt-6 flex gap-4 text-sm text-slate-500">
                <div class="inline-flex items-center gap-2"><span class="text-amber-500 font-bold">•</span> Verified pros</div>
                <div class="inline-flex items-center gap-2"><span class="text-rose-500 font-bold">•</span> Secure messaging</div>
                <div class="inline-flex items-center gap-2"><span class="text-indigo-500 font-bold">•</span> Local support</div>
              </div>
            </div>
            <div class="relative">
              <div id="highlightCard" class="rounded-2xl bg-gradient-to-tr from-white to-indigo-50 shadow-xl p-6 reveal" style="transform-origin:center; display: none;">
                <div id="highlightContainer"></div>
              </div>
              <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-gradient-to-br from-pink-200 to-amber-200 rounded-full opacity-60 blur-xl"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features -->
      <section id="features" class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="feature-card reveal bg-white rounded-xl p-6 shadow hover:shadow-lg transition-shadow">
            <div class="text-indigo-600 mb-3">Reliable Pros</div>
            <h3 class="text-lg font-semibold">Verified provider profiles</h3>
            <p class="mt-2 text-sm text-slate-600">Background checks, ratings and real reviews from customers.</p>
          </div>
          <div class="feature-card reveal bg-white rounded-xl p-6 shadow hover:shadow-lg transition-shadow">
            <div class="text-indigo-600 mb-3">Transparent Pricing</div>
            <h3 class="text-lg font-semibold">Clear rates & quotes</h3>
            <p class="mt-2 text-sm text-slate-600">Compare offers and choose the best fit for your budget and time.</p>
          </div>
          <div class="feature-card reveal bg-white rounded-xl p-6 shadow hover:shadow-lg transition-shadow">
            <div class="text-indigo-600 mb-3">Secure</div>
            <h3 class="text-lg font-semibold">Safe messaging & payments</h3>
            <p class="mt-2 text-sm text-slate-600">Chat with providers securely and pay only after work is complete.</p>
          </div>
        </div>
      </section>

      <!-- Providers grid (keeps providerList id for app.js) -->
      <section id="providers" class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold">Available Providers</h2>
          <a href="providers.html" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div id="providerList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Provider cards injected by js/app.js -->
        </div>
        <p id="noResults" class="no-results mt-4 text-sm text-slate-500" hidden>No providers found — try a different search.</p>
      </section>

      <!-- How it works -->
      <section id="how-it-works" class="max-w-5xl mx-auto px-6 lg:px-8 py-12">
        <div class="bg-gradient-to-b from-white to-slate-50 rounded-2xl p-8 shadow-lg reveal">
          <h2 class="text-2xl font-semibold">How Fixora works</h2>
          <ol class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-slate-700">
            <li class="p-4 bg-white rounded-lg shadow">1. Search & Filter — find the right specialist.</li>
            <li class="p-4 bg-white rounded-lg shadow">2. Message & Compare — get quotes and ask questions.</li>
            <li class="p-4 bg-white rounded-lg shadow">3. Book & Pay — secure payments and reviews.</li>
          </ol>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container footer-inner">
        <p>&copy; <span id="year"></span> Fixora — Connecting people with skilled local professionals.</p>
      </div>
    </footer>

    <script src="js/include.js" defer></script>
    <script src="js/header.js" defer></script>
    <script>
      // This page is the public guest homepage — force guest header state.
      // Do not expose `currentUser` here so the header remains in guest mode.
      window.currentUser = null;
      window.serverProviders = <?php echo json_encode(array_values($providers), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    </script>
    <script src="js/app.js" defer></script>
    <script src="js/index-ui.js" defer></script>
    <script>
      // Sample the uploaded header image and apply its dominant color to header CSS variables.
      (function(){
        const imgSrc = encodeURI('beautiful header.png')
        function applyVars(r,g,b){
          const root = document.documentElement
          root.style.setProperty('--header-rgb', `${r},${g},${b}`)
          const lum = 0.2126*r + 0.7152*g + 0.0722*b
          const text = lum < 140 ? '#ffffff' : '#0f172a'
          const textRgb = text === '#ffffff' ? '255,255,255' : '15,23,42'
          root.style.setProperty('--header-text', text)
          root.style.setProperty('--header-text-rgba', textRgb)
          root.style.setProperty('--header-cta-text', lum < 140 ? '#0f172a' : '#ffffff')
        }

        // Ensure override CSS that uses the variables is present
        const css = `:root{ --header-rgb:59,130,246; --header-text:#fff; --header-text-rgba:255,255,255; --header-cta-text:#0f172a }
        .site-header.hero::before{ background: linear-gradient(180deg, rgba(var(--header-rgb),0.72) 0%, rgba(var(--header-rgb),0.36) 40%, rgba(var(--header-rgb),0.14) 100%) !important }
        .site-header.hero .logo-word{ color: var(--header-text) !important }
        .site-header.hero .nav-center a{ color: rgba(var(--header-text-rgba),0.93) !important }
        .site-header.hero .guest-link{ color: rgba(var(--header-text-rgba),0.92) !important }
        .site-header.hero .cta-signup{ background: rgba(var(--header-text-rgba),0.98) !important; color: var(--header-cta-text) !important }
        `
        const s = document.createElement('style'); s.appendChild(document.createTextNode(css)); document.head.appendChild(s)

        const img = new Image(); img.crossOrigin = 'anonymous'; img.src = imgSrc
        img.onload = function(){
          try{
            const w = 40
            const h = Math.max(1, Math.round(img.height * (w/img.width)))
            const c = document.createElement('canvas'); c.width = w; c.height = h
            const ctx = c.getContext('2d'); ctx.drawImage(img,0,0,w,h)
            const data = ctx.getImageData(0,0,w,h).data
            let r=0,g=0,b=0,count=0
            for(let i=0;i<data.length;i+=4){ const a = data[i+3]; if(a < 100) continue; r += data[i]; g += data[i+1]; b += data[i+2]; count++ }
            if(count === 0) count = 1
            r = Math.round(r/count); g = Math.round(g/count); b = Math.round(b/count)
            applyVars(r,g,b)
          }catch(e){ /* ignore */ }
        }
        img.onerror = function(){ /* ignore */ }
      })()
    </script>
  </body>
</html>
