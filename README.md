# Research Ethics Review Workflow — public source snapshot

A sanitized Laravel 13 / Livewire application demonstrating research submissions, required document checklists, reviewer assignment, revision cycles, committee evaluations, role permissions, audit history, private document delivery and generated clearance layouts.

This independent portfolio snapshot uses fictional institution labels and synthetic fixtures. Institutional documents, uploads, signatures, account records, configuration, original image bytes and original Git history are excluded. Replacement imagery is illustrative. There are no existing or pre-provisioned accounts. This is not an institutional service or a legally valid clearance system.

## Local setup

Requires PHP 8.3+, Composer, Node.js and SQLite support. Work only with synthetic records.

```sh
mkdir -p storage/app/private storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
npm ci --ignore-scripts
npm run build
php artisan serve --host=127.0.0.1
```

Normal seeding creates roles, departments and document types only. Create your own local user through the Laravel console and assign the required role, or configure your own Google OAuth application and allowed domain. The reserved `example.test` domain cannot provide real institutional OAuth accounts. Keep demo persona routes disabled. `UserSeeder` is restricted to the automated testing environment; its accounts and passwords are synthetic test fixtures, never deployment credentials. Mail defaults to a local log. No production endpoints or credentials are included.

## Optional assessment templates

The two original institutional Word templates are not distributed. Assessment downloads return a clear HTTP 404 until an operator supplies reviewed templates outside version control at:

- `storage/app/templates/DEMO_ERB_Form_Informed_Consent_Assessment.docx`
- `storage/app/templates/DEMO_ERB_Form_Protocol_Assessment.docx`

The same filenames may instead be placed under `storage/app/private/templates/`. Missing files are never replaced with invalid placeholder documents. Document-type template links start unconfigured. Administrators may supply their own reviewed material privately. Reviewer assessment uploads retain the existing PDF/DOC/DOCX, 10 MB limit; public distribution excludes those artifacts.

## Verification and limits

The export passed 160 Laravel tests (683 assertions) and a frontend production build. After compatible security updates to the JavaScript lockfile, a fresh install and production build passed and `npm audit --package-lock-only` reported zero vulnerabilities at release time. Run `php artisan test` against a local test database. Tests use synthetic fixtures and do not establish real Google sign-in, delivery of email, institutional acceptance, legal signature validity or production readiness. Browser workflows require local setup and locally created accounts; legacy e2e persona fixtures are disabled by default. External frontend CDNs/fonts and OAuth require separate network access when used.

No new license or redistribution permission is granted by this snapshot. Review applicable ownership and third-party dependency licenses before reuse.
