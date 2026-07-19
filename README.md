# FindIt

**FindIt** is a campus **Lost & Found** management system. Students report lost or found items, submit ownership claims, and admins review claims, manage the catalog, and audit activity.

The application is built with **Laravel 12** and runs on **MySQL / MariaDB** (XAMPP). Core business rules live in `App\Services\FinditPlsqlService` (PHP transactions). Original **Oracle** SQL and PL/SQL scripts remain under `database/oracle/` for coursework reference.

---

## Features

### Public / visitors
- Landing page with recent activity highlights
- Browse lost & found listings with filters (type, category, location, status, search)
- Item detail pages with images and claim context

### Authenticated users
- Register / login (separate from admin auth)
- Personal dashboard (items & claims overview)
- Report a lost or found item (with optional image upload)
- Manage own listings
- Submit claims with a message and proof description
- Track claim status (`PENDING`, `APPROVED`, `REJECTED`)

### Admin console (`/admin`)
- Separate admin login and session guard
- Dashboard with live stats
- Review claims — approve or reject
- Manage items, users, categories, and locations
- Audit log viewer (status/action history from DB triggers)

---

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Database (runtime) | MySQL / MariaDB (XAMPP) |
| DB access | Laravel PDO MySQL |
| Business logic | `FinditPlsqlService` + MySQL audit triggers |
| Frontend | Blade, Vite, Tailwind CSS 4 |
| Auth | Dual guards: `web` (users) and `admin` (admins) |

Oracle scripts (`database/oracle/`) and the optional custom PDO_OCI driver (`app/Database/*`) are kept if you need to demonstrate Oracle / PL/SQL separately.

---

## Project structure (high level)

```
app/
  Database/          # Optional Oracle connector (unused when DB_CONNECTION=mysql)
  Http/Controllers/  # User + admin controllers
  Models/            # Eloquent models
  Services/
    FinditPlsqlService.php   # Business rules (was findit_pkg)
database/
  mysql/             # Active schema for XAMPP MySQL
    01_create_database.sql
    02_create_tables.sql
    03_insert_sample_data.sql
    04_basic_queries.sql
    05_triggers.sql
  oracle/            # Kept for Oracle/PL/SQL coursework
    01_create_user_schema.sql
    02_create_tables.sql
    03_insert_sample_data.sql
    04_basic_queries.sql
    05_plsql_triggers_package.sql
resources/views/     # Blade UI (user + admin)
routes/web.php
storage/app/public/items/   # Sample / uploaded item images
```

---

## Database schema

Core tables:

| Table | Purpose |
|-------|---------|
| `users` | Campus users |
| `admins` | Admin accounts |
| `categories` | Item categories |
| `locations` | Campus locations |
| `items` | Lost/found listings (`LOST` / `FOUND`) |
| `claims` | Ownership claims |
| `audit_logs` | Trigger-written audit trail |

Item statuses: `PENDING`, `FOUND`, `CLAIMED`, `RETURNED`, `REJECTED`  
Claim statuses: `PENDING`, `APPROVED`, `REJECTED`

MySQL uses `AUTO_INCREMENT` primary keys. Audit triggers on `items` and `claims` write to `audit_logs`.

---

## Requirements

- **PHP 8.2+** with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- **Composer**
- **Node.js 18+** and npm (Vite build)
- **XAMPP MySQL/MariaDB** listening on `3306`

---

## MySQL setup (XAMPP)

Start **MySQL** in the XAMPP Control Panel, then run the scripts in order from the project root.

**Command Prompt (cmd):**

```bat
C:\xampp\mysql\bin\mysql.exe -u root < database\mysql\01_create_database.sql
C:\xampp\mysql\bin\mysql.exe -u root findit < database\mysql\02_create_tables.sql
C:\xampp\mysql\bin\mysql.exe -u root findit < database\mysql\05_triggers.sql
C:\xampp\mysql\bin\mysql.exe -u root findit < database\mysql\03_insert_sample_data.sql
```

**PowerShell:**

```powershell
Get-Content database\mysql\01_create_database.sql -Raw | C:\xampp\mysql\bin\mysql.exe -u root
Get-Content database\mysql\02_create_tables.sql -Raw | C:\xampp\mysql\bin\mysql.exe -u root
Get-Content database\mysql\05_triggers.sql -Raw | C:\xampp\mysql\bin\mysql.exe -u root
Get-Content database\mysql\03_insert_sample_data.sql -Raw | C:\xampp\mysql\bin\mysql.exe -u root
```

Optional reference queries: `database/mysql/04_basic_queries.sql`.

If your root user has a password, add `-p` to each mysql command.

---

## Application setup

### 1. Clone and install PHP deps

```bash
git clone https://github.com/Siam-Kabir-2/findit.git
cd findit
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Set MySQL connection values in `.env` (XAMPP defaults):

```env
APP_NAME=FindIt
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=findit
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

### 3. Storage link & assets

```bash
php artisan storage:link
npm install
npm run build
```

For local UI work with hot reload:

```bash
npm run dev
```

### 4. Run the app

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

Admin login: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login).

---

## Demo accounts

Seeded by `database/mysql/03_insert_sample_data.sql`. Passwords are rehashed to bcrypt on first successful login if needed.

| Role  | Email                         | Password                 |
|-------|-------------------------------|--------------------------|
| User  | `john.smith@university.edu`   | `pass_john123`           |
| Admin | `admin.one@university.edu`    | `admin_pass_secure_001`  |

---

## Key routes

| Area | Path | Notes |
|------|------|-------|
| Home | `/` | Public landing |
| Browse | `/items` | Filters + search |
| Item detail | `/items/{id}` | Public |
| User auth | `/login`, `/register` | User guard |
| User dashboard | `/dashboard` | Auth required |
| Report item | `/items/create` | Auth required |
| My claims | `/my-claims` | Auth required |
| Admin login | `/admin/login` | Admin guard |
| Admin dashboard | `/admin/dashboard` | Auth required |
| Admin claims | `/admin/claims?status=PENDING` | Filterable |
| Audit | `/admin/audit` | Auth required |

---

## How Laravel talks to MySQL

1. `.env` sets `DB_CONNECTION=mysql`.
2. Controllers use `DB::select` / Eloquent for reads.
3. Writes go through `FinditPlsqlService` (transactions + validation).
4. MySQL triggers append rows to `audit_logs` on item/claim changes.

Example flow — **approve claim**:

1. Admin posts to `/admin/claims/{id}/approve`
2. Controller calls `FinditPlsqlService::approveClaim()`
3. Service updates claim + related item status
4. Triggers append rows to `audit_logs`

---

## Oracle scripts (kept)

`database/oracle/` still contains the original schema, sample data, and `findit_pkg` PL/SQL package. Use those if your course requires Oracle demos. The optional Oracle driver under `app/Database/` remains registered when `DB_CONNECTION=oracle`.

---

## Item images

- Uploads and sample images live under `storage/app/public/items/`
- Public URL path uses the storage symlink: `/storage/items/...`
- DB column `items.item_image` stores a relative path such as `items/dell_laptop.png`

---

## Troubleshooting

| Problem | What to check |
|---------|----------------|
| `could not find driver` (pdo_mysql) | Enable `extension=pdo_mysql` in XAMPP `php.ini`; restart PHP |
| Connection refused | Start MySQL in XAMPP; `DB_HOST=127.0.0.1`, `DB_PORT=3306` |
| Access denied for root | Match `DB_PASSWORD` to your XAMPP MySQL root password |
| Unknown database `findit` | Run `01_create_database.sql` then tables/triggers/seed |
| Blank styles | Run `npm install && npm run build` |
| Images 404 | Run `php artisan storage:link` |
| Session / cache DB errors | Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` |

---

## Security notes

- `.env` is gitignored — never commit real credentials
- Dual auth prevents user sessions from accessing `/admin/*`
- Claim approve/reject is admin-only
- Demo passwords are for local/dev only; change them before any shared deployment

---

## License

This project is provided for academic / coursework use unless otherwise noted.
