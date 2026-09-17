# AGENTS.md

## Overview

This repository is **Bonfire** (v0.9.0-dev), a modular web application framework built on **CodeIgniter 3** (HMVC) using PHP.

- **Web root**: `public/index.php` — all web requests route through `public/`
- **CI3 system**: `bonfire/ci3/` (CodeIgniter 3 framework files)
- **Bonfire core**: `bonfire/` (core libs, helpers, controllers, HMVC modules)
- **Application code**: `application/` (controllers, views, models, configs, `application/modules/`)
- **HMVC layer**: `application/third_party/MX` — Wiredesignz Modular Extensions HMVC

## Environment & Prerequisites

- **PHP**: `>= 5.4.0`. PHP 8+ emits dynamic-property deprecation notices from legacy CI3 code (harmless, but noisy).
- **Environment** is set via the `CI_ENV` server variable (read in `public/index.php:56`), defaulting to `development`.
- **Database**: config in `application/config/database.php`. Default: MySQLi, `localhost`/`root`/empty password, `template` DB.
- **base_url** is hardcoded to `http://localhost/template/public/` in `application/config/config.php:26`.

## Commands & Testing

Run tests via SimpleTest (bundled, no Composer install needed):

```
php tests/run.php              # run all tests (CLI, TextReporter)
php tests/run.php -b           # Bonfire core tests only
php tests/run.php -a           # Application tests only
php tests/run.php -f file_test.php    # single test file
php tests/run.php -d path/to/dir       # tests in a directory
```

- Tests bootstrap CodeIgniter by including `public/index.php`, then run via `TextReporter` in CLI mode or `test_gui.php` in browser.
- Model tests use **mocked DB**: `tests/_support/database.php` provides a stub `MY_DB` class; tests call `Mock::generate('MY_DB')`. Real DB credentials are not required for most tests.
- **Sparks**: `php tools/spark <command>`

## Key Conventions & Architecture

- **Module location priority**: configured in `application/config/application.php:30-33`. App modules (`application/modules/`) take priority over Bonfire core modules (`bonfire/modules/`). Core modules include: users, roles, permissions, logs, emailer, migrations, builder, docs, settings, translate, ui, sysinfo, activities, database, developer.
- **Controller hierarchy** (all in `application/core/`):
  - `Base_Controller` (extends `MX_Controller`) → root, autoloads `settings_lib`, `events`, handles sessions/cache/migrations
  - `Front_Controller` → public-facing pages
  - `Authenticated_Controller` → requires login
  - `Admin_Controller` → admin context + permissions
  - `App_Controller` → app-specific overrides
- **Core overrides**: Bonfire replaces CI3 core classes with `BF_` prefixed variants in `bonfire/core/` (`BF_Model`, `BF_Router`, `BF_Security`, `BF_Loader`, `BF_Lang`).
- **Routes**: `application/config/routes.php` — default controller `home`, auth routes (login/register/logout), and `SITE_AREA` admin contexts (content, master, reports, developer, settings).
- **Hooks**: `application/config/hooks.php` — `App_hooks` class handles requested-URL tracking, redirects, and maintenance mode.

## Migrations

- Bonfire uses its **own migration library** (`bonfire/modules/migrations/libraries/Migrations.php`), not CI3's native `$this->migration`. The CI3 native config in `application/config/migration.php` does **not** control Bonfire migrations.
- **Core migrations**: sequential-numbered PHP files in `bonfire/migrations/` (currently 001–044).
- **App migrations**: go in `application/db/migrations/`. Naming: `NNN_description.php`, class `Migration_description` with `up()`/`down()` methods.
- **Auto-migrate** is disabled by default: `$config['migrate.auto_core'] = false` and `$config['migrate.auto_app'] = false` in `application/config/application.php:271-272`. Enable to auto-run on controller load.

## Tooling Notes

- No CI workflows, linters, or static analysis are configured. `composer.json` has no `require-dev`. PHP code style is not enforced.
- No `.env` file; environment config lives in `application/config/` (with `development/`, `testing/`, `production/` subdirs).
