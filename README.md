# StudioForge Visual Builder

StudioForge is a multi-tenant Laravel SaaS foundation for creating, editing, previewing, and publishing websites from structured builder data. The editable tree is the source of truth; draft preview and published rendering use the same block registry and renderer.

## Stack

- PHP 8.5
- Laravel 13
- Livewire 4
- Filament 5
- Tailwind CSS 4.3
- Vite 8
- MySQL 8.0+ or PostgreSQL 16+
- Redis-compatible cache and queues in production

## Install

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

Run workers separately:

```bash
php artisan queue:work
php artisan schedule:work
```

## Demo account

After seeding: `owner@example.test` / `password`. Never use these credentials in production.

## Verification

```bash
composer validate
vendor/bin/pint --test
vendor/bin/phpstan analyse
php artisan test
npm run build
```

## Architecture

The application uses workspace-scoped authorization, ULID primary keys, normalized builder nodes for editing, immutable page versions for history, and publish snapshots for fast anonymous rendering. Start with `docs/ARCHITECTURE.md`, `docs/BUILDER-ARCHITECTURE.md`, and `docs/ERD.md`.

## Current milestone

This repository contains the installable Laravel foundation, core schema, tenant-aware models and policies, component registry, shared renderer, initial blocks, publishing action, seed data, and foundational tests. Runtime verification must be completed after dependencies are installed.
