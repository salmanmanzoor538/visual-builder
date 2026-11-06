# Architecture

StudioForge is a modular Laravel monolith. This keeps transactions, authorization, and deployment straightforward while preserving domain boundaries.

## Boundaries

- Identity and workspaces: users, memberships, roles, invitations.
- Websites: websites, domains, settings, global styles, and pages.
- Builder: structured nodes, registry, validation, rendering, versions, and publishing.
- Forms and media: definitions, submissions, and tenant-owned assets.
- Administration: Filament is reserved for platform operations, not the customer builder.

Controllers and Livewire components authorize resources, validate input, and delegate non-trivial work to actions. Policies enforce membership and capabilities. Database constraints provide the final integrity boundary.

Public domain records use ULIDs. Workspace ownership is explicit on top-level resources. Descendants derive ownership through mandatory parent relations. Route model binding never replaces policy checks.

One web application serves the SaaS interface and published sites. Queue workers compile large snapshots and process media. Redis is recommended for cache, queues, locks, and rate limiting; object storage is recommended for media.
