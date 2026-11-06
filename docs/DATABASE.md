# Database design

ULIDs are used for domain keys. `workspace_user` has a composite unique membership key. Slugs are unique within their parent. Builder sibling ordering is indexed by page, parent, and position. JSON columns hold extensible component data while ownership and lifecycle fields stay normalized.

Foreign keys cascade where children have no independent audit value. Soft deletes protect websites and pages. Immutable versions are retained according to workspace policy.
