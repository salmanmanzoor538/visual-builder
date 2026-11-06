# Builder architecture

## Decision: normalized drafts plus immutable snapshots

Draft pages use normalized `builder_nodes`. Each node has a stable ULID, parent, order, component type, content, settings, base styles, and responsive overrides. This supports targeted edits, nesting validation, and conflict-safe updates.

Page versions and publish versions store immutable structured snapshots. Snapshots make history, restore, audit, and anonymous rendering independent from future draft edits.

## Invariants

- A node belongs to exactly one page.
- Parent and child belong to the same page.
- The component registry validates parent-child relationships.
- Sibling order is indexed within its parent scope.
- Normal content is escaped by block views.
- Published output is produced only from a validated snapshot.

Edits authorize the page, check its optimistic revision, validate schemas and nesting, apply one transaction, then increment the revision. Autosave is debounced and never writes during pointer movement. Undo/redo uses a bounded client command history plus durable version checkpoints.
