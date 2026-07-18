# FindIt

Campus **Lost & Found** management system built with **Laravel**, **Oracle 11g**, and **PL/SQL**.

## Stack

- Laravel 12 (PHP 8.2+)
- Oracle Database 11g XE (`PDO_OCI`)
- Blade + Vite + Tailwind CSS 4
- PL/SQL package `findit_pkg` (procedures, functions, audit triggers)

## Setup

1. Install PHP dependencies: `composer install`
2. Copy `.env.example` to `.env` and set Oracle credentials
3. Ensure Oracle XE is running and the `findit` schema/scripts under `database/oracle/` are applied
4. `php artisan storage:link`
5. `npm install && npm run build`
6. `php artisan serve`

## Demo accounts

| Role | Email | Password |
|------|--------|----------|
| User | `john.smith@university.edu` | `pass_john123` |
| Admin | `admin.one@university.edu` | `admin_pass_secure_001` |

## Oracle scripts

Run in order as the `findit` (or `SYSTEM`) user:

1. `database/oracle/01_create_user_schema.sql`
2. `database/oracle/02_create_tables.sql`
3. `database/oracle/03_insert_sample_data.sql`
4. `database/oracle/05_plsql_triggers_package.sql`
