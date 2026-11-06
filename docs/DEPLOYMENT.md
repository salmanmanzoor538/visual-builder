# Deployment

Provision PHP 8.5 with required Laravel extensions, Nginx, MySQL/PostgreSQL, Redis, a queue supervisor, HTTPS, and object storage. Store secrets outside source control.

```bash
composer install --no-dev --classmap-authoritative
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan queue:restart
```

Run `php artisan schedule:run` every minute. Deploy releases into versioned directories, switch the current symlink only after migrations and health checks, and retain the prior release for rollback. Back up the database and media independently and test restoration quarterly.
