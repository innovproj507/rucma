# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

RUCMA (Rucma Certifícate) is a CodeIgniter 4 web app for a maritime/STCW training
provider (PAMEL) to manage students, courses, and certificates across multiple
offices ("oficinas"): issue certificates as PDFs, track their state, run reports,
and let anyone verify a certificate's authenticity via a public QR-code URL.
Backend code, DB tables, and internal docs are in Spanish; UI strings go through
a bilingual (en/es) language system.

## Commands

PHP in PATH is 8.1.10, but this app requires **PHP 8.2+**. Laragon also has a
8.2 install; use it explicitly for `spark`/`composer`/`phpunit` unless the
shell's default `php` has already been switched to 8.2:

```
"/c/laragon/bin/php/php-8.2.33-Win32-vs16-x64/php.exe" spark <command>
```

- Run all tests: `composer test` (wraps `phpunit`) or `vendor/bin/phpunit`
- Run a single test: `vendor/bin/phpunit --filter testMethodName path/to/TestFile.php`
- List routes: `spark routes`
- Dev server: `spark serve` (app is normally served via Laragon vhost at `http://rucma2.test`, per `.env`)
- Build Tailwind CSS once: `npm run build:css`
- Watch Tailwind CSS during frontend work: `npm run watch:css`
  (source: `resources/css/input.css` → output: `public/css/tailwind.css`; views live under `app/Views`, which is the Tailwind `@source`)

There are no app-specific PHPUnit tests yet — `tests/` only has the stock
CodeIgniter examples (session/database smoke tests). There are also no
CodeIgniter migrations (`app/Database/Migrations` is empty); the schema is
managed via raw SQL dumps in the repo root (e.g. `rucma_DB (6).sql`,
`fase1_maestras.sql`, `db_changes_for_production_*.sql`) rather than
`spark migrate`.

## Architecture

### Request flow and multi-tenancy ("oficinas")

Every office (oficina) is a tenant-like scope. Almost all operational data
(students, certificates, dashboard, reports) is filtered by office:

- `BaseController::oficinaEfectiva()` is the central helper: for a normal user
  it's always their own `idOficina`; for an admin it's whatever office they've
  selected in the header dropdown (`session('oficinaVista')`), or `null` for
  "all offices". Controllers call this instead of reading the session directly.
- `BaseController::ROLES_TODAS_OFICINAS` (`Owner`, `Administrator`,
  `Administrador`) defines which roles see all offices instead of being locked
  to one.
- `OficinaVista::cambiar/$idOficina` (route `oficina-vista/(:any)`) lets an
  admin switch the office they're viewing; it's a no-op for non-admins.
- Controllers that serve a specific record by ID (e.g. `Certificado::pdf`,
  `Certificado::cancelar`) must re-check the record's `idOficina` against
  `oficinaEfectiva()` even after permission checks pass — this is what stops a
  logged-in user from reaching another office's data by guessing an ID (see
  the comments in `app/Controllers/Certificado.php`).

### Auth, roles, and permissions

- Login (`app/Controllers/Login.php`) checks `sha1(password)` against
  `tbl_users.password` (legacy scheme, not `password_hash`/bcrypt) and, on
  success, stores roles/permissions/office info in the session, including a
  random `tokens` value that `AuthFilter` treats as the "is logged in" flag.
- `BaseController::initController()` calls `refrescarPermisos()` on **every**
  request for a logged-in user, recomputing `roles`, `permisos`, and `isAdmin`
  from the DB. This is intentional so a role/permission change takes effect
  immediately without forcing re-login — don't move this to login-time-only
  caching.
- Permissions are fine-grained codes (e.g. `certificados.cancelar`,
  `estudiantes.crear`), unioned across a user's active roles
  (`UserModel::getPermisos`). Controller actions should start with:
  ```php
  if ($resp = $this->exigirPermiso('some.permission.code')) { return $resp; }
  ```
  `exigirPermiso()` returns a 403 JSON response for AJAX/JSON requests or a
  redirect-with-flash for normal page loads, and returns `null` when allowed.
- Two filters exist: `auth` (session has `tokens`) is applied globally to all
  protected route groups in `app/Config/Filters.php`. `admin` (session
  `isAdmin`) is registered as an alias but intentionally **not** wired into
  `$filters` yet — fine-grained permission checks (`exigirPermiso`) are the
  actual authorization mechanism inside controllers; `isAdmin` mainly gates
  the office-switcher UI.

### Certificates

- `CertificadoModel` is the single place that builds certificate numbers
  (`siguienteConsecutivo()` — atomic per-office consecutive counter in
  `tbl_secuencias`, formatted with a two-letter prefix cycling every 100,000
  certs, replicating the legacy PHP app's `Form::getPrefijo()`/`consecutivo()`)
  and that renders the certificate PDF (`renderizarPdf()`, via mpdf). Both the
  real issuance flow (`Certificado::pdf`) and the template editor's live
  preview (`Plantilla::preview`) call `renderizarPdf()` so they can never
  drift out of sync — extend that method rather than duplicating PDF-building
  logic elsewhere.
- Certificate authenticity uses a salt+hash scheme: `code` is a random salt
  stored alongside the certificate, `hashcode = sha512(codigo . code)`. Public
  verification (`Validate::certificado`, route `consult/validate/(:any)`, no
  login required) re-derives the hash from the stored salt and compares with
  `hash_equals()`. Only certificates with `estado = 'E'` (Emitido/active)
  validate; `estado = 'C'` (Cancelado) fails validation and the PDF renders
  with a "CANCELLED" watermark.
- Certificates are never hard-deleted while active: `cancelar()` toggles
  `estado` between `E`/`C` and writes an audit row to
  `tbl_certificado_historial`; `eliminar()` only permits a real delete once a
  certificate is already `Cancelado`.
- A course (`tbl_cursos`) belongs to one office and only becomes emitible for
  a category once hours are defined for that pairing in
  `tbl_curso_categoria_horas` — `CertificadoModel::cursosPorCategoria()` joins
  both.

### Bilingual UI (en/es)

- Every request re-applies the language from the session (not the URL) in
  `BaseController::aplicarIdioma()`, defaulting to `en`. Switch language via
  `Idioma::cambiar/{en|es}` (route `idioma/(:any)`).
- UI strings live in `app/Language/{en,es}/<Group>.php` and are pulled with
  CodeIgniter's `lang('Group.key')`. When adding user-facing strings, add the
  key to **both** `en/` and `es/` files.

### Frontend conventions

- Server-rendered PHP views (`app/Views/*.php`) assembled per-controller as
  `view('header', $data) . view('page') . view('footer')` — there's no shared
  layout wrapper; controllers concatenate these manually.
- Each major view has a matching hand-written JS file in `public/js/`
  (e.g. `certificates.php` ↔ `certificates.js`), using jQuery + DataTables for
  listing/filtering pages (`*/filter` JSON endpoints feed them) and plain
  fetch/AJAX with `csrf_hash()` round-tripped in JSON responses for
  create/update/delete actions. `public/lib/` vendors many jQuery-era
  libraries (DataTables, select2, summernote, parsleyjs, etc.) — check there
  before adding a new frontend dependency.
- Styling is Tailwind v4 (`@import "tailwindcss"` + `@theme` tokens in
  `resources/css/input.css`, brand color scale `brand-50`..`brand-950`)
  compiled to `public/css/tailwind.css`; the app forces `color-scheme: light
  only` so certificate-template previews in the editor don't get inverted by
  browser dark mode.
