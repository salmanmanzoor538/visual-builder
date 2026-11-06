# Troubleshooting

- Missing classes: run `composer install` and `composer dump-autoload`.
- Missing assets: run `npm install && npm run build`.
- Database errors: verify `.env`, create the database, then run `php artisan migrate`.
- Stale output: run `php artisan optimize:clear` and restart queue workers.
- Published page missing: confirm the website has an active publish version and a visible page snapshot.
