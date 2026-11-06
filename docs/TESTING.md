# Testing

Use SQLite for fast feature tests and the production database engine in integration CI before release. Required suites cover authentication, policies, tenant isolation, builder rules, rendering, publishing, forms, media, and domains.

```bash
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
npm run build
```
