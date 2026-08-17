# Guide for AI Agents

## Project Purpose

AbeloHost is an MVP blog built with plain PHP 8.5, without a framework.
It uses MySQL, Smarty, SCSS, and Docker Compose.
The complete functional specification is in `Plan.md`; read it before starting a task.

## Project Map

```text
public/index.php                 # Front controller
kernel/                           # PHP backend, namespace Kernel\
database/
  migrations/*.sql                # SQL migrations
  migrations/migrate.php          # Runner for all SQL migrations
  seeders/seed.php                # Idempotent seeder
src/                              # Frontend source directory
```

## Backend Rules

## Frontend Rules

- Source files are located only in `src/scss/`.

## Docker and Environment

- Services: `php` (PHP-FPM), `nginx`, `db` (MySQL), one-off `init`, and profile-based `assets` (Node).
- On creation/re-creation, `init` runs `composer install`, migrations, and the seeder; it then exits. `php` starts after `init` completes successfully.
- `vendor` is in the `composer_vendor` named volume, not the local project directory. Do not rely on a local `vendor` directory inside the PHP container.
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