# Stack versions

Validated against official release and installation documentation on 2026-09-04.

| Technology | Selected version | Latest stable checked | Reason | Compatibility notes |
|---|---:|---:|---|---|
| PHP | 8.5 | 8.5 | Current actively supported line | Satisfies Laravel and Filament |
| Laravel | 13.x | 13.x | Current stable framework | Skeleton requires PHP 8.3+ |
| Livewire | 4.x | 4.x | Current stable Livewire line | Supports Laravel 10+ and PHP 8.1+ |
| Filament | 5.x | 5.x | Current stable admin line | Requires PHP 8.2+, Laravel 11.28+, Tailwind 4.1+ |
| Alpine.js | Livewire bundled | Livewire bundled | Avoid duplicate runtimes | Loaded by Livewire |
| Tailwind CSS | 4.3.x | 4.3 | Current stable CSS line | Exceeds Filament 5 minimum |
| Vite | 8.x | 8.x | Current stable build tool | Laravel Vite plugin 3 |
| Pest | 4.x | 4.x | Current stable runner | PHPUnit 12 compatible |
| PHPUnit | 12.5.x | 12.5.x | Laravel 13 skeleton default | PHP 8.3+ |

Lock files become the authoritative exact patch-version record after installation.
