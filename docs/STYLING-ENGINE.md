# Styling engine

Styles are stored as validated semantic properties, not arbitrary Tailwind class strings. The renderer maps properties to an allowlist and emits scoped CSS variables or sanitized declarations. Responsive values inherit `base -> tablet -> mobile`; only overrides are stored.

Arbitrary CSS and JavaScript are disabled by default and require a dedicated permission. Published custom code must be isolated, audited, and protected by a suitable Content Security Policy.
