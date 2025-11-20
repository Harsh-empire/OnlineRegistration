# Backend (PHP + MySQL) — Setup & Quick Guide

These files add a minimal PHP JSON API and a MySQL schema to this project.

Files added:

- `backend/db.php` — PDO helper and JSON utilities. Edit DB constants at the top.
- `backend/register.php` — POST JSON register endpoint.
- `backend/login.php` — POST JSON login endpoint.
- `sql/schema.sql` — MySQL schema to create database and `users` table.

Quick local steps
1. Install MySQL and create/import schema:

   - From MySQL CLI or a GUI import tool run `sql/schema.sql`.

   Example using `mysql` command-line:

   ```powershell
   mysql -u root -p < "sql\schema.sql"
   ```

2. Configure DB connection

   - Open `backend/db.php` and update `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS`.

3. Start PHP built-in server for development

   From the project root run:

   ```powershell
   php -S localhost:8000 -t backend
   ```

   This serves `backend/*.php` under `http://localhost:8000/` (e.g. `http://localhost:8000/register.php`).

4. Calling the endpoints

   - Register: POST JSON to `/register.php` with body:

   ```json
   {"email":"alice@example.com","password":"secret","firstName":"Alice","lastName":"Smith","country":"us"}
   ```

   - Login: POST JSON to `/login.php` with body:

   ```json
   {"email":"alice@example.com","password":"secret"}
   ```

Notes and next steps
- CORS is permissive (`*`) in the example for convenience — tighten for production.
- Passwords use PHP `password_hash`/`password_verify`.
- Consider adding HTTPS, CSRF protections, rate limiting, and input validation for production.
- If you want, I can integrate the frontend (`index.html`) to call these endpoints directly (fetch) and persist responses into `localStorage`.
