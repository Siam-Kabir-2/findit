# FindIt

**FindIt** is a campus **Lost & Found** management system. Students report lost or found items, submit ownership claims, and admins review claims, manage the catalog, and audit activity.

The application is built with **Laravel 12**, connects to **Oracle Database 11g XE** through a custom **PDO_OCI** driver, and routes core business logic through a **PL/SQL** package (`findit_pkg`).

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
- Dashboard with live stats from PL/SQL functions
- Review claims — approve or reject via PL/SQL
- Manage items, users, categories, and locations
- Audit log viewer (status/action history from DB triggers)

---

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | Oracle Database 11g XE |
| DB access | Custom PDO_OCI connector (`app/Database/*`) |
| Business logic | PL/SQL package `findit_pkg` + triggers |
| Frontend | Blade, Vite, Tailwind CSS 4 |
| Auth | Dual guards: `web` (users) and `admin` (admins) |

> **Why not `yajra/laravel-oci8`?**  
> The stock OCI8 extension builds often target newer Oracle clients and do not play well with **Oracle 11g XE**. FindIt uses PHP’s **PDO_OCI** with a small Laravel connector that emits **Oracle 11g-safe SQL** (including `ROWNUM` pagination).

---

## Project structure (high level)

```
app/
  Database/          # Oracle connector, connection, grammars, processor
  Http/Controllers/  # User + admin controllers
  Models/            # Eloquent models mapped to Oracle tables
  Services/
    FinditPlsqlService.php   # Calls findit_pkg procedures/functions
database/
  oracle/
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

Sequences + `BEFORE INSERT` triggers assign primary keys. Status/audit triggers write to `audit_logs`.

---

## PL/SQL package (`findit_pkg`)

Defined in `database/oracle/05_plsql_triggers_package.sql`. Laravel calls it through `App\Services\FinditPlsqlService`.

### Procedures
| Procedure | Role |
|-----------|------|
| `register_user` | Create a user |
| `add_item` | Report a lost/found item |
| `update_item_status` | Change item status |
| `submit_claim` | Create a claim |
| `approve_claim` / `reject_claim` | Admin claim decisions |
| `add_category` / `add_location` | Catalog maintenance |
| `delete_*` | Safe deletes for users, items, categories, locations |

### Functions (dashboard)
`get_total_users`, `get_total_items`, `get_pending_claims`, `get_approved_claims`, `get_lost_items`, `get_found_items`

---

## Requirements

- **PHP 8.2+** with extensions: `pdo_oci`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- **Composer**
- **Node.js 18+** and npm (Vite build)
- **Oracle Database 11g XE** listening on `1521` (service/SID `XE`)
- Oracle Instant Client compatible with PDO_OCI on your OS
- Recommended local stack on Windows: **XAMPP** (PHP) + Oracle XE

---

## Oracle setup

Run the scripts **in order** (SQL\*Plus, SQL Developer, or similar).

### 1. Create schema user

`database/oracle/01_create_user_schema.sql`

Creates user `findit` / password `106` (adjust if needed) and grants:

- `CONNECT`, `RESOURCE`
- `CREATE SESSION`, `CREATE TABLE`, `CREATE SEQUENCE`, `CREATE TRIGGER`, `CREATE PROCEDURE`
- unlimited quota on `USERS`

> The script connects as `SYSTEM`. Change the SYSTEM password in the script to match your install.

### 2. Tables & sequences

`database/oracle/02_create_tables.sql`

### 3. Sample data

`database/oracle/03_insert_sample_data.sql`

### 4. Optional reference queries

`database/oracle/04_basic_queries.sql`

### 5. Triggers + PL/SQL package

`database/oracle/05_plsql_triggers_package.sql`

Confirm the package compiled:

```sql
SELECT object_name, object_type, status
FROM   user_objects
WHERE  object_name = 'FINDIT_PKG';
```

Status should be `VALID`.

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

Set Oracle connection values in `.env`:

```env
APP_NAME=FindIt
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=XE
DB_USERNAME=findit
DB_PASSWORD=106

# Prefer file/sync drivers (Oracle session/cache tables are not used)
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

Seeded by `03_insert_sample_data.sql`. Passwords are rehashed to bcrypt on first successful login if needed.

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

## How Laravel talks to Oracle

1. `config/database.php` registers connection `oracle`.
2. `AppServiceProvider` binds the custom connector in `app/Database/`.
3. Eloquent models use the `oracle` connection for reads/listing.
4. Writes that must stay consistent with business rules go through `FinditPlsqlService` → `findit_pkg`.

Example flow — **approve claim**:

1. Admin posts to `/admin/claims/{id}/approve`
2. Controller calls `FinditPlsqlService::approveClaim()`
3. Package updates claim + related item status
4. Triggers append rows to `audit_logs`

---

## Item images

- Uploads and sample images live under `storage/app/public/items/`
- Public URL path uses the storage symlink: `/storage/items/...`
- DB column `items.item_image` stores a relative path such as `items/dell_laptop.png`

---

## Troubleshooting

| Problem | What to check |
|---------|----------------|
| `could not find driver` / PDO OCI errors | Enable `pdo_oci` in `php.ini`; Instant Client on `PATH`; restart Apache/CLI |
| ORA-12154 / connection refused | Oracle listener up; `DB_HOST`, `DB_PORT`, `DB_DATABASE=XE` |
| ORA-01017 invalid username/password | Schema user from script 01; password matches `.env` |
| Package INVALID | Re-run `05_plsql_triggers_package.sql`; check `USER_ERRORS` |
| Blank styles | Run `npm install && npm run build` |
| Images 404 | Run `php artisan storage:link` |
| Session / cache DB errors | Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` |

Disable conflicting OCI8 DLLs meant for newer Oracle if they crash PHP 8.2 against 11g (common on XAMPP). Prefer **PDO_OCI** only for this project.

---

## Security notes

- `.env` is gitignored — never commit real credentials
- Dual auth prevents user sessions from accessing `/admin/*`
- Claim approve/reject is admin-only and executed in PL/SQL
- Demo passwords are for local/dev only; change them before any shared deployment

---

## License

This project is provided for academic / coursework use unless otherwise noted.
