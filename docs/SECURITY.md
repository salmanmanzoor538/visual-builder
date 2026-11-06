# Security baseline

- Policies and workspace membership protect every tenant-owned action.
- CSRF middleware protects state-changing browser requests.
- Blade escapes ordinary content; generic blocks never render raw HTML.
- Custom HTML/code requires explicit permission and sanitization.
- Uploads require size, MIME, extension, and decoded-file validation.
- Models use explicit fillable attributes; queries use parameter binding.
- Published cache keys include workspace, website, page, and version.
- Domain verification uses random challenges and blocks private-network requests.
- Forms use rate limits, honeypots, and server-side validation.
- Production disables debug output and requires HTTPS and secure cookies.

Tenant-isolation and IDOR tests are release blockers.
