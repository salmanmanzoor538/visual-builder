# StudioForge Agent Guide

## Purpose

Build a secure multi-tenant visual website builder. Structured builder data is authoritative; generated HTML is a derived publish artifact.

## Non-negotiable rules

- Every workspace-owned query and mutation must be authorized server-side.
- Never trust a workspace, website, page, or node ID from the browser.
- Draft and published output must use `BlockRegistry` and `BuilderRenderer`.
- Do not add block-type switch statements outside the registry.
- Escape normal content. Custom HTML/code requires explicit permission and sanitization.
- Regenerate ULIDs for copied or duplicated node trees.
- Do not change published snapshots while editing drafts.
- Add or update tests with every domain change.

## Domain layout

- `app/Builder`: blocks, registry, rendering, validation, and publishing
- `app/Models`: persistence and relations
- `app/Policies`: tenant authorization
- `database/migrations`: canonical schema
- `docs`: decisions and operating guidance

## Adding a block

Implement `BlockDefinition`, add its renderer view and tests, and register it in `AppServiceProvider`. See `docs/ADDING-A-BLOCK.md`.

## Definition of done

Run Composer validation, Pint, PHPStan, tests, and the Vite production build. Document any command that cannot run and never report an unverified feature as complete.
