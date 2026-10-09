# AGENTS.md

## Overview

**Klinika Apotek** — clinic & pharmacy management app (Indonesian UI) built on **Bonfire** (v0.9.0-dev) / **CodeIgniter 3** HMVC, PHP >= 5.4.

- Web root: `public/index.php` (app also reachable at `http://localhost/klinikapotek/public/`; root `index.php` exists too)
- App code: `application/` (controllers, models, views, `modules/`)
- Framework: `bonfire/` (core) + `bonfire/ci3/`; HMVC layer `application/third_party/MX`
- Feature modules in `application/modules/`: antrian, apotekonline, audit, backup, kunjungan, laporan, master, pasien, pemeriksaan, pengadaan, pengguna, penjualan, resep, stok, tagihan, transaksi, users

## Environment & Setup

- **Database is PostgreSQL**, not MySQL: driver `postgre`, db `apotek`, user `postgres`, port 5432 (`application/config/database.php`).
- Password comes from env **`DB_PASSWORD`** (default fallback `'postgres'`). `public/index.php` manually parses root `.env` into env vars via `putenv` — copy `.env.example` to `.env`; do not commit `.env`.
- Fresh install: `CREATE DATABASE apotek;` then `psql -U postgres -d apotek -f database/apotek_latest.sql` (structure + seed, cf. `database/README_DATABASE.md`).
- Existing old DB: run `psql -U postgres -d apotek -v ON_ERROR_STOP=1 -f database/update_dari_versi_lama.sql` (idempotent incremental update; do NOT re-import `apotek_latest.sql`).
- `base_url` hardcoded to `http://localhost/klinikapotek/public/` in `application/config/config.php`.
- `composer install` needed for `phpoffice/phpspreadsheet` (exports); `vendor/` is committed-present but not in git per .gitignore (verify).
- Test logins (password `admin123`, phpass-hashed): `admin` (ADMIN_PELAYANAN), plus dokter/apoteker/pasien users — see `database/README_DATABASE.md`.

## Testing

```
php tests/run.php              # all tests (SimpleTest, CLI)
php tests/run.php -b           # Bonfire core only
php tests/run.php -a           # application tests only
php tests/run.php -f file_test.php
php tests/run.php -d path/to/dir
```

- Bootstrap includes `public/index.php`; model tests mock DB via `tests/_support/database.php` (`Mock::generate('MY_DB')`).
- Sparks CLI: `php tools/spark <command>`.

## Architecture & Conventions

- Controller hierarchy in `application/core/`: `Base_Controller` (MX_Controller; autoloads settings_lib/events, sessions/cache/migrations) → `Front_Controller` / `Authenticated_Controller` / `Admin_Controller` → `App_Controller`.
- Core CI classes overridden with `BF_` prefixes in `bonfire/core/` (BF_Model, BF_Router, BF_Security, BF_Loader, BF_Lang).
- App modules in `application/modules/` take priority over `bonfire/modules/` (`application/config/application.php`).
- Routes in `application/config/routes.php`: default controller `home`; `SITE_AREA` admin contexts (content/master/reports); custom `admin/master/<modul>/...` aliases.
- Hooks in `application/config/hooks.php` (`App_hooks`): requested-URL tracking, redirects, maintenance mode.

## Migrations & DB Gotchas

- Bonfire uses its own migration lib (`bonfire/modules/migrations/libraries/Migrations.php`); CI3 native `application/config/migration.php` does **not** govern it. Auto-migrate is OFF (`application/config/application.php`).
- Core migrations numbered in `bonfire/migrations/`; app migrations in `application/db/migrations/` (`NNN_description.php`, class `Migration_description` with `up()`/`down()`). Schema changes also ship as SQL in `database/update_dari_versi_lama.sql` — keep migrations and that script in sync.
- Status columns use UPPERCASE enums enforced by CHECK constraints (`AKTIF`, `TERDAFTAR`, `MENUNGGU`, `DIPROSES`, `SELESAI`, `DIBUAT`, ...). Inserting lowercase status fails.
- Sidebar visibility is data-driven: needs `{Module}.{Context}.View` (and `Site.{Context}.View`) rows in permissions per role — missing rows = empty sidebar.
- `dokter.id_user` / `pasien.id_user` map login users to their doctor/patient records; per-user data filters silently show nothing if these mappings are not set.
- Passwords stored as phpass hashes — never replace with plaintext.

## Tooling Notes

- No CI workflows, linters, or static analysis; no enforced PHP style; `composer.json` has no require-dev.
- PHP 8+ dynamic-property deprecation notices from legacy CI3 are expected/harmless.
- Env-specific config overrides live in `application/config/{development,testing,production}/`.
