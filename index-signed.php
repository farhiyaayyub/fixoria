<?php
session_start();
$first = $_SESSION['first_name'] ?? '';
$last = $_SESSION['last_name'] ?? '';
$username = $_SESSION['username'] ?? '';
$initial = $first ? strtoupper(substr($first,0,1)) : ($username ? strtoupper(substr($username,0,1)) : 'U');

// load DB so we can render provider cards on the signed-in index
$providers = [];
try{
	require __DIR__ . '/auth/db.php';
	$stmt = $pdo->query("SELECT id, username, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 12");
	$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){
	$providers = [];
}
// if no providers from DB, expose the signed-in user so UI isn't empty
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
// compute unread messages for this signed-in provider
$unread_count = 0;
try{
	if(!empty($_SESSION['user_id'])){
		$stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM messages WHERE provider_id = :pid AND read_at IS NULL');
		$stmt->execute(['pid' => (int)$_SESSION['user_id']]);
		$r = $stmt->fetch(PDO::FETCH_ASSOC);
		$unread_count = isset($r['cnt']) ? (int)$r['cnt'] : 0;
	}
}catch(Exception $e){ $unread_count = 0; }
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Fixora — Your dashboard</title>
		<script src="https://cdn.tailwindcss.com"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" onload="this.rel='stylesheet'">
		<noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>
		<meta name="description" content="Fixora signed in view" />
		<link rel="stylesheet" href="css/styles.css">
	</head>
	<body class="antialiased bg-gradient-to-b from-slate-50 via-white to-slate-50 text-slate-800">
		<header id="siteHeader" class="site-header" role="banner">
		  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="header-inner flex items-center justify-between py-3">
			  <div class="left flex items-center gap-3">
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
				<div id="authArea" class="flex items-center gap-3">
					<!-- avatar/profile -->
					<div id="profileMenu" class="relative">
						<button id="avatarBtn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 px-2 py-1 rounded focus:outline-none avatar-btn">
							<span id="avatarCircle" class="w-9 h-9 rounded-full avatar-ring bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-700"><?php echo htmlspecialchars($initial); ?></span>
						</button>
						<div id="avatarDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded shadow-lg ring-1 ring-black ring-opacity-5">
							<div class="py-1">
								<a href="provider.html?id=<?php echo urlencode($_SESSION['user_id'] ?? ''); ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View profile</a>
								<a href="provider-edit.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit profile</a>
								<a href="messages.php?provider_id=<?php echo urlencode($_SESSION['user_id'] ?? ''); ?>" class="flex items-center justify-between block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
									<span>Messages</span>
									<?php if(!empty($unread_count)): ?>
									  <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-rose-100 text-rose-700"><?php echo $unread_count > 99 ? '99+' : htmlspecialchars($unread_count); ?></span>
									<?php endif; ?>
								</a>
								<a href="auth/logout.php" class="w-full text-left block px-4 py-2 text-sm text-rose-600 hover:bg-slate-50">Sign out</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		  </div>
		</header>

		<main class="relative overflow-hidden">
		  <section class="relative isolate bg-white">
		    <div class="absolute -top-24 left-1/2 -z-10 transform -translate-x-1/2 blur-3xl opacity-60">
		      <div class="w-[680px] h-[420px] bg-gradient-to-tr from-indigo-400 via-blue-400 to-emerald-300 rounded-[40%] animate-blob mix-blend-multiply" style="filter:blur(60px);"></div>
		    </div>
		    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
		      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
		        <div class="space-y-6">
				  <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight gradient-heading">Welcome back, <?php echo htmlspecialchars($first ?: $username); ?></h1>
		          <p class="text-lg text-slate-600 max-w-xl">Manage your profile, view quotes, and respond to job requests.</p>
		          <div class="flex flex-wrap gap-3 items-center">
		            <a href="provider.html" class="rounded-full px-5 py-3 bg-indigo-600 text-white shadow hover:scale-[1.02] transition-transform">My profile</a>
					<a href="post-request.php" class="rounded-full px-5 py-3 border">Post a job</a>
					<a href="messages.php?provider_id=<?php echo urlencode($_SESSION['user_id'] ?? ''); ?>" class="rounded-full px-5 py-3 border flex items-center gap-2 hover:bg-slate-50">
					  <span>Messages</span>
					  <?php if(!empty($unread_count)): ?>
					    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-rose-100 text-rose-700"><?php echo $unread_count > 99 ? '99+' : htmlspecialchars($unread_count); ?></span>
					  <?php endif; ?>
					</a>
		          </div>
		        </div>
		        <div class="relative">
		          <div class="rounded-2xl bg-gradient-to-tr from-white to-indigo-50 shadow-xl p-6 reveal" style="transform-origin:center;">
		            <div class="grid grid-cols-1 gap-4">
		              <div class="flex gap-4 items-center">
		                <div class="w-16 h-16 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
		                  <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64'></svg>" alt="" class="w-full h-full object-contain p-1">
		                </div>
		                <div>
		                  <div class="font-semibold">Recent activity</div>
		                  <div class="text-sm text-slate-500">No recent activity</div>
		                </div>
		              </div>
		              <div class="grid grid-cols-2 gap-3 mt-3">
		                <div class="p-3 border rounded-lg bg-white shadow-sm">Local pros vetted by our team.</div>
		                <div class="p-3 border rounded-lg bg-white shadow-sm">Fast quotes & secure payments.</div>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		  </section>

		  <!-- Small providers preview for signed-in users -->
		  <section id="providersSection" class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
		    <div class="flex items-center justify-between mb-6">
		      <h2 class="text-2xl font-bold">Your Nearby Providers</h2>
		      <a href="providers.php" class="text-sm text-indigo-600 hover:underline">View all</a>
		    </div>
		    <div id="providerList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
		<?php if(!empty($providers)): ?>
		<?php foreach($providers as $p):
		  $display = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? 'Unnamed');
		  $memberSince = $p['created_at'] ? date('Y-m-d', strtotime($p['created_at'])) : '';
		?>
		      <article class="card bg-white rounded-xl shadow-lg p-6 flex flex-col items-start gap-4" data-provider-id="<?php echo htmlspecialchars($p['id']); ?>" data-provider-email="<?php echo htmlspecialchars($p['email'] ?? ''); ?>">
		        <div class="w-full flex items-center gap-4">
		          <?php
		            $email = trim(strtolower($p['email'] ?? ''));
		            $gravatar = '';
		            if($email){ $gravatar = 'https://www.gravatar.com/avatar/' . md5($email) . '?s=160&d=identicon'; }
		            $initials = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? 'U');
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
		  </section>
		</main>

		<footer class="site-footer">
			<div class="container footer-inner">
				<p>&copy; <span id="year"></span> Fixora — Connecting people with skilled local professionals.</p>
			</div>
		</footer>

			<script>document.getElementById('year')?.textContent = new Date().getFullYear();</script>
			<script src="js/header.js" defer></script>
			<script src="js/app.js" defer></script>
			<script src="js/index-ui.js" defer></script>
			<script>
		// Avatar dropdown wiring for signed-in header
		(function(){
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
		})()
		</script>
			<script>
  // expose current session user to client-side for UI scripts
  window.currentUser = <?php echo json_encode([ 'id' => $_SESSION['user_id'] ?? null, 'username' => $_SESSION['username'] ?? null, 'first_name' => $_SESSION['first_name'] ?? null, 'last_name' => $_SESSION['last_name'] ?? null, 'email' => $_SESSION['email'] ?? null ], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
</script>
			<script>
				window.serverProviders = <?php echo json_encode(array_values($providers ?: []), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
			</script>
	</body>
</html>
