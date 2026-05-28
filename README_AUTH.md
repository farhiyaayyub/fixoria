# Fixora — Local XAMPP Authentication Setup

This folder contains a minimal MySQL schema and simple PHP authentication example you can run locally with XAMPP.

What I added
- `sql/fixora_schema.sql` — creates the `fixora` database and a `users` table.
- `auth/db.php` — PDO database connection (edit credentials if needed).
- `auth/signup.php` — sign-up form and handler (uses `password_hash`).
- `auth/login.php` — sign-in form and handler (uses `password_verify`).
- `auth/logout.php` — logs out the user.
- `auth/protected.php` — simple protected page that requires sign-in.

Steps to run locally (Windows + XAMPP)

1. Start XAMPP and run Apache + MySQL (use the XAMPP Control Panel).

2. Import the database schema
   - Option A (phpMyAdmin):
     - Open `http://localhost/phpmyadmin/` in your browser.
     - Click "Import", choose `sql/fixora_schema.sql` from this project, and run.
   - Option B (MySQL CLI from PowerShell):
     - Open PowerShell.
     - Run: `mysql -u root < "C:\xampp\htdocs\Fixoria\sql\fixora_schema.sql"`
     - If your MySQL root has a password: `mysql -u root -p < "C:\xampp\htdocs\Fixoria\sql\fixora_schema.sql"` and enter the password when prompted.

3. Open the auth pages
   - Visit `http://localhost/Fixoria/auth/signup.php` to create an account. After signing up the page will show a verification link (local testing) — open it to verify your email and activate the account.
   - Then go to `http://localhost/Fixoria/auth/login.php` to sign in. After sign-in you'll be redirected to `auth/protected.php`.
   - For local testing the "forgot password" form (`auth/forgot.php`) will display a reset link you can open to set a new password.

Notes about root pages
   - I added short redirects from `signup.html` -> `/Fixoria/auth/signup.php` and `login.html` -> `/Fixoria/auth/login.php` so any existing links to the root pages will forward to the PHP auth pages.

4. Credentials and security notes
   - The example uses `root` / empty password by default (XAMPP typical default). If your MySQL uses a password, open `auth/db.php` and update `$DB_PASS`.
   - Passwords are hashed with PHP's `password_hash()` (bcrypt). Never store plain text passwords.
   - This example is intentionally minimal for learning. Do not use it as-is in production.

5. Next steps (optional help I can do for you)
   - Integrate the auth UI into your existing `index.html` header and show a signed-in header.
   - Add email verification (send confirmation email).
   - Add a password-reset flow.

If you want, I can update `auth/db.php` to your MySQL credentials now, or wire the header links on `index.html` to the auth pages.
