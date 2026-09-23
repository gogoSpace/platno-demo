# Single-host deployment

These are example Apache 2.4 / PHP 8.4-FPM files for `platno.gogospace.cz`. Adapt paths, domain and process identity before use. They assume a dedicated `platno-demo` system user and a single server with SQLite and local files. Do not use this workspace-switching implementation with Octane or a shared multi-host filesystem.

## Release layout

```text
/var/www/platno-demo/
  current -> releases/<release>/app
  releases/<release>/
    app/                 # platno-demo checkout and Composer vendor
    platno/              # matching laravel-platno checkout
  shared/
    .env                 # private, persistent application key
    storage/             # private registry, workspaces, sessions and logs
    tmp/                 # PHP upload temporary directory
    acme/                # certificate challenge webroot
```

The sibling package is required by Composer's `../platno` path repository. Record the package and application Git revisions together. Install each release with `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`; do not run `composer update` during deployment. Link the release's `.env` and `storage` to the persistent paths. The process user needs write access to storage and the release's `bootstrap/cache`, but not to application code.

Use `.env.example` as the starting point. Set `APP_ENV=production`, `APP_DEBUG=false`, the canonical HTTPS `APP_URL`, and `SESSION_SECURE_COOKIE=true`. Generate the application key once and preserve it across releases. Keep the environment file out of the public directory and source control.

Run the following as the PHP-FPM process user from the new application directory:

```sh
php artisan demo:prepare --no-interaction
php artisan optimize --no-interaction
php artisan schedule:list
```

`demo:prepare` creates or migrates the registry; it does not reset visitor data. Switch `current` atomically only after the commands pass. Reload PHP-FPM to avoid old opcode paths after a release switch. Retain the previous release for rollback; do not replace or restore private storage as part of a code rollback.

## HTTPS and scheduling

Install the HTTP virtual host first. Point the domain's A record to this server, and publish an AAAA record only if it reaches the same virtual host. Obtain a certificate using the `shared/acme` webroot, then install the HTTPS virtual host. Validate Apache and FPM configuration before reloading either service. Enable certificate renewal through the server's certificate manager.

For Certbot with the webroot authenticator, install `platno-demo-renewal.sh` as `/etc/letsencrypt/renewal-hooks/deploy/platno-demo` with root ownership and mode `0755`. Adapt its certificate lineage if you change the domain. The hook validates and reloads Apache after a successful renewal of this certificate, so Apache starts serving the renewed certificate. An active renewal timer alone does not reload a manually configured virtual host. Verify renewal with `certbot renew --cert-name platno.gogospace.cz --dry-run`.

Run Laravel's scheduler every minute under the same process user:

```cron
* * * * * platno-demo cd /var/www/platno-demo/current && /usr/bin/php artisan schedule:run >> /var/www/platno-demo/shared/storage/logs/scheduler.log 2>&1
```

The application schedules expired workspace cleanup every five minutes. Expired workspaces remain inaccessible even if cleanup is delayed. Ensure application, scheduler and Apache logs are rotated. The virtual host logs omit query strings and referrers; paths can still contain temporary public share tokens, so restrict log access.

## Acceptance after a release

- Verify HTTPS, `/up`, the home page and `/developers`; verify `.env` and private files cannot be served.
- Start a new disposable demo, edit, save, publish and open its share link. Verify the image loads.
- Open the editor address in another browser session and confirm it cannot access the first visitor's editor.
- Verify Native, Vue, React and Livewire mount the same owned page. Vue/React load pinned external framework modules; the native editor and public pages need no CDN.
- Confirm scheduler execution, dedicated FPM pool health and writable private storage. Never run the test suite against production workspaces.

The supplied limits are an experimental-preview starting point, not a load-tested capacity promise. Monitor disk use, PHP-FPM saturation, failed workspace creation and cleanup failures before increasing them.
