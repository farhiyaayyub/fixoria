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
    <title>Post a Job — Fixora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" onload="this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
    <meta name="description" content="Create and manage local job requests — post your job, receive quotes, and hire local professionals." />
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body class="antialiased bg-gradient-to-b from-slate-50 via-white to-slate-50 text-slate-800">
    <!-- server-rendered header: show avatar if signed-in, else guest links -->
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

    <section class="hero relative isolate">
      <div class="absolute -top-24 left-1/2 -z-10 transform -translate-x-1/2 blur-3xl opacity-60 pointer-events-none" aria-hidden="true">
        <div class="w-[680px] h-[420px] rounded-[40%] bg-gradient-to-tr from-indigo-400 via-blue-400 to-emerald-300 animate-blob mix-blend-multiply" style="filter:blur(60px);"></div>
      </div>
      <div class="hero-inner max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <h1 class="text-4xl font-extrabold">Post a Job — Get responses from local pros</h1>
            <p class="lead mt-4">Describe your job and providers will send quotes. This demo stores posts in your browser — perfect for quick testing and prototyping.</p>
            <div class="mt-6">
              <a href="#postForm" class="btn primary">Create a post</a>
            </div>
          </div>
          <div class="hidden lg:block">
            <div class="rounded-2xl bg-gradient-to-tr from-white to-indigo-50 shadow-xl p-6 reveal">
              <div class="font-semibold">Why post here?</div>
              <p class="text-sm text-slate-600 mt-2">Quick local outreach, private replies, and an easy demo experience — no server required.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        <aside class="hidden lg:block">
          <div class="rounded-2xl bg-gradient-to-tr from-white to-indigo-50 shadow-xl p-8 reveal">
            <div class="mb-4">
              <svg class="w-20 h-20 text-blue-600" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" rx="12" fill="currentColor" opacity="0.08"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Post a Job — Get quick responses</h2>
            <p class="mt-3 text-slate-600">Create a clear job request so local providers can send quotes. This demo saves posts locally in your browser (no server required).</p>
            <ul class="mt-6 space-y-2 text-sm text-slate-700">
              <li>• Describe the work and any constraints</li>
              <li>• Add an optional budget to get better matches</li>
              <li>• Providers will contact you via the contact number you supply</li>
            </ul>
          </div>
        </aside>

        <div>
          <div class="bg-white rounded-lg shadow p-6 mb-6 reveal">
            <h1 class="text-2xl font-semibold mb-2">Post a Job</h1>
            <p class="text-sm text-slate-600 mb-4">Fill in the details below to create a job request.</p>

            <form id="postForm" class="space-y-4" role="form">
              <div>
                <label class="block text-sm font-medium text-slate-700">Job Title</label>
                <input id="title" required class="mt-1 block w-full rounded border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="e.g. Fix my leaking sink" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Category</label>
                <select id="category" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                  <option>Plumbing</option>
                  <option>Electrical</option>
                  <option>Carpentry</option>
                  <option>Painting</option>
                  <option>General Handyman</option>
                  <option>Other</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Budget (optional)</label>
                <input id="budget" type="number" min="0" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="e.g. 100" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" rows="4" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="Describe the job, location, and any details providers need to know."></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700">Contact number (optional)</label>
                  <input id="phone" type="tel" inputmode="tel" class="mt-1 block w-full rounded border border-slate-200 px-3 py-2" placeholder="e.g. +63 917 123 4567" />
              </div>

              <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Posts are saved locally in your browser.</div>
                <div>
                  <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded shadow hover:scale-[1.02] transition-transform">Save Post</button>
                </div>
              </div>
            </form>
          </div>

          <div class="mt-6 text-sm text-slate-600">
            Your saved posts are not shown on this page. View all saved posts in the
            <a href="posted-jobs.php" class="text-blue-600 hover:underline">Posted Jobs</a> list.
            <div id="savedNote" class="mt-3 text-sm text-emerald-600" style="display:none"></div>
          </div>
        </div>
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

      /* Posts are shown on the dedicated Posted Jobs page. This page only saves posts to localStorage.
        The rendering/listing logic lives in `posted-jobs.php`. */

      function escapeHtml(text){
        if(!text && text !== 0) return '';
        return String(text)
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#39;');
      }

      document.addEventListener('DOMContentLoaded', ()=>{
        const form = document.getElementById('postForm');

        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const title = document.getElementById('title').value.trim();
          const category = document.getElementById('category').value;
          const budget = document.getElementById('budget').value.trim();
          const description = document.getElementById('description').value.trim();
          const phone = document.getElementById('phone').value.trim();

          if(!title){
            alert('Please enter a job title.');
            return;
          }

          const payload = {
            id: cryptoRandomId(),
            title, category, budget: budget || null, description, phone: phone || null,
            createdAt: new Date().toISOString()
          };

          // Try server first, fall back to localStorage
          let saved = false;
          try{
            const res = await fetch('/api/posts', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            });
            if(res.ok){
              // use server's returned object when available
              const created = await res.json().catch(()=> payload);
              const posts = loadPosts();
              posts.push(created);
              savePosts(posts);
              saved = true;
            }
          }catch(err){
            // network error or no server — fall back below
          }

          if(!saved){
            const posts = loadPosts();
            posts.push(payload);
            savePosts(posts);
          }

          form.reset();
          const savedNote = document.getElementById('savedNote');
          if(savedNote){
            savedNote.style.display = 'block';
            savedNote.innerHTML = 'Post saved. View it in <a href="posted-jobs.php" class="text-blue-600 hover:underline">Posted Jobs</a>.';
          }
        });
      });

      function cryptoRandomId(){
        // simple unique id using timestamp + random
        return Date.now().toString(36) + Math.random().toString(36).slice(2,9);
      }
    </script>
  </body>
</html>