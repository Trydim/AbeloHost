# Guide for AI Agents

## Project Purpose

AbeloHost is an MVP blog built with plain PHP 8.5, without a framework.
It uses MySQL, Smarty, SCSS, and Docker Compose.
The complete functional specification is in `Plan.md`; read it before starting a task.

## Project Map

```text
public/index.php                  # Front controller
database/
  migrations/[0-9]*.php           # Versioned PDO migration callbacks
  migrations/migrate.php          # Migration runner; tracks schema_migrations
  seeders/seed.php                # Idempotent PDO seeder
kernel/                           # PHP backend, namespace Kernel\
  Kernel.php                      # Application singleton, configure() and request handling
  Container/Container.php         # DI container: get(), singleton(), autowiring
  Route/Route.php                 # Route registration/dispatching
  Http/Request.php                # Request built from PHP superglobals
  Controller/                     # Controllers
  Actions/                        # Actions and SQL queries
  DB/Database.php                 # PDO creation from env
  View/View.php                   # Smarty configuration
  Helper/func.php                 # Global app() helper
src/                              # Frontend source directory
templates/                        # Smarty templates
  pages/                          # Page templates
  layouts/                        # Base layouts
  partials/                       # Reusable template fragments
```

## Backend Rules

- Any change to `composer.json` requires updating `composer.lock` (`composer update --lock`) and checking `composer validate --strict`.
- Use the `Kernel\` namespace; preserve the case of names and directories.
- Keep SQL and PDO access in `kernel/Actions/`; controllers orchestrate requests and prepare view data.
- Never execute database queries inside loops. Fetch data in bulk and group flat result rows in the controller when needed.
- Validate and normalize request parameters before passing them to actions. Pagination is always one-based.
- New migrations must be named with a unique numeric prefix, for example `002_add_index.php`, and return `callable(PDO $pdo): void`.
- Applied migrations are recorded in `schema_migrations`. Never edit an applied migration; add a new version instead.
- MySQL DDL implicitly commits, so do not assume that wrapping schema changes in a PDO transaction makes them atomic.

## Frontend Rules

- Source files are located only in `src/scss/`.
- Do not use the SCSS `&` selector. Write complete BEM, pseudo-class, and state selectors explicitly.

## Docker and Environment

- Services: `php` (PHP-FPM), `nginx`, `db` (MySQL), one-off `init`, one-off `assets-init`, and profile-based `assets` (Node).
- On creation/re-creation, `init` runs Composer, migrations, and the seeder; `assets-init` runs `npm ci` and builds CSS. Both then exit successfully before their dependent long-running services start.
- `vendor` and `node_modules` use the `composer_vendor` and `node_modules` named volumes. Do not rely on local dependency directories inside containers.
- PHP does not read `.env` and does not require dotenv. Docker Compose can read `.env` for variable substitution and passes values to containers; PHP receives them via `getenv()`.
- `.env.example` is only a template and is not loaded automatically. Do not commit `.env` or real secrets.
- Xdebug is installed in the image. If `.env` is absent, `XDEBUG_MODE` defaults to `off`; Xdebug is always disabled for `init`. Keep `XDEBUG_MODE=off` in production.
- Do not run `docker compose down -v` without an explicit request: it removes MySQL data and the Composer dependency volume.

## Useful Commands

```bash
# Full local startup
docker compose up --build -d

# Status and logs
docker compose ps
docker compose logs -f php

# PHP dependencies and checks
docker compose exec php composer validate --strict
docker compose exec php composer dump-autoload --optimize
docker compose exec php sh -c "find kernel database public routes -name '*.php' -print0 | xargs -0 -n1 php -l"

# Database
docker compose exec php php database/migrations/migrate.php
docker compose exec php php database/seeders/seed.php
```
