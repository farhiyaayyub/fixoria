# Fixora — Demo Landing Page

This is a small, static demo of Fixora — a web-based platform that connects skilled individuals (freelancers, repair specialists, designers, tutors, local providers) with people and small businesses who need services.

Files added:
- `index.html` — Main landing page and provider cards
- `css/styles.css` — Basic responsive styles
- `js/app.js` — Client-side search and filter logic

How to run locally (with XAMPP):

1. Place the `Fixoria` folder inside your XAMPP `htdocs` (already located at `c:\xampp\htdocs\Fixoria`).
2. Start Apache using the XAMPP Control Panel.
3. Open a browser and visit:

   http://localhost/Fixoria/

Quick PowerShell command to open the site (Windows PowerShell v5.1):

```powershell
Start-Process 'http://localhost/Fixoria/'
```

Notes & next steps:
- This is a static prototype. To make Fixora production-ready you would add a backend (API, DB), authentication, messaging, payments, profile management, and proper validation.
- I can next scaffold a simple backend (Node/Express) or wire up a mock JSON API for provider listings if you want.
