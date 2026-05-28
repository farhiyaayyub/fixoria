<?php
session_start();
$first = $_SESSION['first_name'] ?? '';
$last = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? '';
$initial = $first ? strtoupper(substr($first,0,1)) : ($username ? strtoupper(substr($username,0,1)) : 'U');
$authenticated = !empty($_SESSION['user_id']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Posted Jobs — Fixora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
    <meta name="description" content="View your posted jobs saved in the browser." />
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

    <section class="hero relative isolate bg-white">
      <div class="absolute -top-24 left-1/2 -z-10 transform -translate-x-1/2 blur-3xl opacity-60">
        <div class="w-[680px] h-[420px] bg-gradient-to-tr from-indigo-400 via-blue-400 to-emerald-300 rounded-[40%] animate-blob mix-blend-multiply" style="filter:blur(60px);"></div>
      </div>
      <div class="hero-inner max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <h1 class="text-4xl font-extrabold">Posted Jobs</h1>
            <p class="lead mt-4">All job requests you've posted in this browser are listed here.</p>
            <div class="mt-6">
              <a href="post-request.php" class="btn primary">Create a new post</a>
            </div>
          </div>
          <div class="hidden lg:block">
            <div class="rounded-2xl bg-gradient-to-tr from-white to-indigo-50 shadow-xl p-6 reveal">
              <div class="font-semibold">Manage your posts</div>
              <p class="text-sm text-slate-600 mt-2">Delete individual posts or clear all posts stored locally in your browser.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
      <div class="bg-white rounded-lg shadow p-6 mb-6 reveal">
        <div class="flex items-center justify-between mb-4 gap-4">
          <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold">Your Saved Posts</h2>
            <div id="postsCount" class="text-sm text-slate-500">(0)</div>
          </div>
          <div class="flex items-center gap-3">
            <input id="searchInput" placeholder="Search title, category or description" class="px-3 py-2 border rounded w-64 text-sm" />
            <select id="categoryFilter" class="px-3 py-2 border rounded text-sm">
              <option value="all">All categories</option>
            </select>
            <select id="sortBy" class="px-3 py-2 border rounded text-sm">
              <option value="newest">Newest</option>
              <option value="oldest">Oldest</option>
              <option value="budget-high">Budget: High → Low</option>
              <option value="budget-low">Budget: Low → High</option>
            </select>
            <select id="pageSize" class="px-3 py-2 border rounded text-sm">
              <option value="5">5 / page</option>
              <option value="10" selected>10 / page</option>
              <option value="25">25 / page</option>
            </select>
            <button id="clearAll" class="text-sm text-red-600 hover:underline">Clear All</button>
          </div>
        </div>

        <div id="postsList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- posts will render here as cards -->
        </div>

        <div id="noPosts" class="text-slate-500 mt-4">No posts yet — create one on the Post a Job page.</div>

        <div id="pagination" class="mt-6 flex items-center justify-center space-x-2"></div>
      </div>
    </main>

    <footer class="site-footer">
      <div class="container footer-inner px-4 sm:px-6 lg:px-8 py-6">
        <p>&copy; <span id="year"></span> Fixora — Connecting people with skilled local professionals.</p>
      </div>
    </footer>

    <script src="js/header.js" defer></script>
    <script src="js/app.js" defer></script>
    <script src="js/index-ui.js" defer></script>
    <script>
      const STORAGE_KEY = 'fixora_posts';

      function loadPosts(){
        try{
          const raw = localStorage.getItem(STORAGE_KEY);
          return raw ? JSON.parse(raw) : [];
        }catch(e){
          console.error('Failed to parse posts', e);
          return [];
        }
      }

      function savePosts(posts){
        localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
      }

      function escapeHtml(text){
        if(!text && text !== 0) return '';
        return String(text)
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#39;');
      }
      // Enhanced rendering: search/filter/sort/pagination
      (function(){
        const listEl = document.getElementById('postsList');
        const noPosts = document.getElementById('noPosts');
        const postsCount = document.getElementById('postsCount');
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortBy = document.getElementById('sortBy');
        const pageSizeEl = document.getElementById('pageSize');
        const paginationEl = document.getElementById('pagination');

        let posts = [];
        let filtered = [];
        let currentPage = 1;

        function loadAndIndex(){
          posts = loadPosts() || [];
          // populate category filter options
          const cats = Array.from(new Set(posts.map(p => (p.category||'').trim()).filter(Boolean))).sort();
          // clear existing (keep first All option)
          while(categoryFilter.options.length > 1) categoryFilter.remove(1);
          cats.forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; categoryFilter.appendChild(o) });
          updateCount();
        }

        function updateCount(){ if(postsCount) postsCount.textContent = `(${posts.length})`; }

        function applyFilterSort(){
          const q = (searchInput?.value || '').toString().toLowerCase().trim();
          const cat = categoryFilter?.value || 'all';
          const s = sortBy?.value || 'newest';

          filtered = posts.slice().filter(p => {
            if(cat !== 'all' && (p.category||'') !== cat) return false;
            if(!q) return true;
            const hay = `${p.title || ''} ${p.category || ''} ${p.description || ''} ${p.phone || ''}`.toLowerCase();
            return hay.indexOf(q) !== -1;
          });

          if(s === 'newest') filtered.sort((a,b) => new Date(b.createdAt) - new Date(a.createdAt));
          else if(s === 'oldest') filtered.sort((a,b) => new Date(a.createdAt) - new Date(b.createdAt));
          else if(s === 'budget-high') filtered.sort((a,b) => (parseFloat(b.budget)||0) - (parseFloat(a.budget)||0));
          else if(s === 'budget-low') filtered.sort((a,b) => (parseFloat(a.budget)||0) - (parseFloat(b.budget)||0));
        }

        function renderPage(){
          const size = Number(pageSizeEl.value || 10);
          const total = filtered.length;
          const totalPages = Math.max(1, Math.ceil(total / size));
          if(currentPage > totalPages) currentPage = totalPages;
          const start = (currentPage - 1) * size;
          const pageItems = filtered.slice(start, start + size);

          listEl.innerHTML = '';
          if(!posts.length){ noPosts.style.display = 'block'; paginationEl.innerHTML = ''; return; }
          noPosts.style.display = 'none';

          if(!pageItems.length){ listEl.innerHTML = `<div class="col-span-1 text-slate-500">No matching posts.</div>`; paginationEl.innerHTML = ''; return; }

          pageItems.forEach((p, idx) => {
            const created = new Date(p.createdAt || Date.now());
            const card = document.createElement('article');
            card.className = 'card reveal bg-white rounded-lg p-4 shadow flex flex-col justify-between';
            card.innerHTML = `
              <div>
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h3 class="text-lg font-semibold">${escapeHtml(p.title || '')}</h3>
                    <div class="text-sm text-slate-500 mt-1">${escapeHtml(p.category || 'Uncategorized')} • ${p.budget ? '₱' + escapeHtml(p.budget) : 'Budget not set'}</div>
                  </div>
                  <div class="text-sm text-slate-400">${created.toLocaleString()}</div>
                </div>
                <p class="mt-3 text-slate-700">${escapeHtml((p.description||'').substring(0,300))}${(p.description||'').length > 300 ? '…' : ''}</p>
              </div>
              <div class="mt-4 flex items-center justify-between text-sm text-slate-600">
                <div>${p.phone ? `<a href="tel:${escapeHtml(p.phone)}" class="text-blue-600">${escapeHtml(p.phone)}</a>` : '<span class="text-slate-400">No contact number</span>'}</div>
                <div class="space-x-2">
                  <a href="job.html?id=${encodeURIComponent(p.id)}" class="text-sm text-blue-600 hover:underline">View</a>
                  <button data-id="${escapeHtml(p.id)}" class="deleteBtn inline-flex items-center px-2 py-1 bg-red-50 text-red-600 rounded text-sm">Delete</button>
                </div>
              </div>
            `;
            listEl.appendChild(card);
          });

          renderPagination(totalPages);
          attachDeleteHandlers();
        }

        function renderPagination(totalPages){
          paginationEl.innerHTML = '';
          if(totalPages <= 1) return;
          const createBtn = (txt, cls, handler) => { const b = document.createElement('button'); b.className = 'px-3 py-1 border rounded text-sm '+(cls||''); b.innerText = txt; b.addEventListener('click', handler); return b };

          // prev
          paginationEl.appendChild(createBtn('Prev','', ()=>{ if(currentPage>1){ currentPage--; renderPage(); } }));

          // page numbers (show window around current)
          const windowSize = 5;
          let start = Math.max(1, currentPage - Math.floor(windowSize/2));
          let end = Math.min(totalPages, start + windowSize -1);
          if(end - start < windowSize -1) start = Math.max(1, end - windowSize +1);
          for(let p=start;p<=end;p++){
            const btn = createBtn(p, p===currentPage ? 'bg-slate-900 text-white' : '', ()=>{ currentPage = p; renderPage(); });
            paginationEl.appendChild(btn);
          }

          // next
          paginationEl.appendChild(createBtn('Next','', ()=>{ if(currentPage < totalPages){ currentPage++; renderPage(); } }));
        }

        function attachDeleteHandlers(){
          const dels = Array.from(document.querySelectorAll('.deleteBtn'));
          dels.forEach(btn => {
            btn.addEventListener('click', function(){
              const id = this.getAttribute('data-id');
              if(!id) return;
              if(!confirm('Delete this post?')) return;
              const all = loadPosts();
              const idx = all.findIndex(x => String(x.id) === String(id));
              if(idx === -1) return;
              all.splice(idx,1);
              savePosts(all);
              loadAndIndex(); applyFilterSort(); renderPage();
            })
          })
        }

        // wire up controls
        [searchInput, categoryFilter, sortBy, pageSizeEl].forEach(el=>{ if(!el) return; el.addEventListener('input', ()=>{ currentPage = 1; applyFilterSort(); renderPage(); }) });

        document.getElementById('clearAll').addEventListener('click', ()=>{
          if(confirm('Clear all posts from this browser?')){
            localStorage.removeItem(STORAGE_KEY);
            loadAndIndex(); applyFilterSort(); renderPage();
          }
        });

        // initial load
        loadAndIndex(); applyFilterSort(); renderPage();
      })();

      // set year in footer
      try{ document.getElementById('year').textContent = new Date().getFullYear(); }catch(e){}
    </script>
  </body>
</html>