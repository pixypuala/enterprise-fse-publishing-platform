# Baseline Audit

> Canvas §5 (Phase Zero — Discovery and Baseline Audit). Every result below is
> pasted from a command actually run on the checkout. Where a canvas command has
> no analogue in this repository (no `npm`, no live WordPress, no E2E), that is
> stated as a documented boundary rather than reported as a pass.

## Environment

| Field | Value |
|-------|-------|
| Branch | `main` |
| Commit (audit time) | `87847488aeb941915b2f9b9488085e31f1583f0a` |
| Plugin version (header) | `0.1.0` |
| Requires at least (WP) | `6.5` |
| Requires PHP | `8.1` |
| Local PHP running the tools | `PHP 8.5.8 (cli)` |
| PHPUnit | `10.5.64` |
| Audit timestamp (UTC) | `2026-07-18T14:58:36Z` |

## 5.1 Repository discovery — what actually exists

Verified by reading every non-vendor source file. This is a **plugin plus bundled
FSE theme**; it deliberately has **no** REST routes, AJAX handlers, upload
handling, custom SQL, or remote requests (verified by grep — see §Attack surface).

### Plugin: `wordpress/plugins/enterprise-publishing/`

| Component | File | Responsibility |
|-----------|------|----------------|
| Bootstrap | `enterprise-publishing.php` | Valid plugin header; `declare(strict_types=1)`; `ABSPATH` guard; Composer-or-fallback PSR-4 autoload; boots on `plugins_loaded`; registers activation/deactivation hooks. |
| Wiring | `src/Plugin.php` | Composes domain with WordPress adapters; hooks `init` (model registration, migrations), `admin_menu` (health screen, admin only). |
| Content model (domain) | `src/ContentModels/ContentModel.php` | Framework-free immutable value object. Asserts post-type key validity at the boundary (`1–20 chars`, `^[a-z][a-z0-9_]*$`) — fails loudly instead of letting WordPress silently truncate. Derives a **custom per-model capability base** (governance hinge). |
| Registry (domain) | `src/ContentModels/Registry.php` | Single data-driven source of truth for which models exist (Program, Event, Story). Registration glue and capability matrix both read it, so they cannot drift. |
| Registrar (adapter) | `src/ContentModels/ModelRegistrar.php` | Only caller of `register_post_type()`; applies `capability_type` + `map_meta_cap`. |
| Capability map (domain) | `src/Capabilities/CapabilityMap.php` | Pure computation of the role→capability grant table for contributor/editor/administrator. Server-authoritative policy, fully unit-tested. |
| Capability installer (adapter) | `src/Capabilities/CapabilityInstaller.php` | Idempotently writes computed grants onto real roles; skips roles absent on the install rather than fabricating them. |
| Schema version (domain) | `src/Migrations/SchemaVersion.php` | Ordered, idempotent migration ledger (`CURRENT = 2`). Decides pending steps; **refuses to downgrade** by throwing `OutOfRangeException` when installed > current. |
| Migration runner (adapter) | `src/Migrations/MigrationRunner.php` | Applies pending steps, persisting the version option **after each step** so an interrupted run resumes. |
| Health screen (admin) | `src/Admin/HealthScreen.php` | Tools-menu page; re-checks `manage_options` on render (defence in depth); escapes all dynamic output; shows schema state + governed models + published counts. |
| SEO builder (domain) | `src/Seo/ProgramSchema.php` | Framework-free Schema.org `EducationalOccupationalProgram` JSON-LD builder; asserts required name; omits empty properties. |

### Theme: `wordpress/themes/enterprise-fse/`

Block (FSE) theme: `theme.json` tokens, `style.css`, `templates/` (index, single-
and archive-`ep_program`), `parts/` (header, footer), `patterns/` (hero, program-
card-grid), minimal `functions.php` (registers one pattern category + text
domain). Presentation only — no business logic.

### Tests, CI, tooling

- `tests/unit/` — 4 framework-free test files (`CapabilityMapTest`, `ContentModelTest`, `ProgramSchemaTest`, `SchemaVersionTest`).
- `.github/workflows/ci.yml` — PHP matrix job: composer validate, install, PHP lint, PHPCS/WPCS, PHPUnit.
- `composer.json` (PSR-4, dev tooling), `phpcs.xml.dist`, `phpunit.xml.dist`, `playground/blueprint.json`.

## 5.2 Baseline commands — real output

Canvas §5.2 lists `npm`, `wp`, and E2E commands. This repository has **no
`package.json`, no build step, and no live WordPress instance** in the checkout,
so those commands do not apply — recorded as documented boundaries below, not as
passes. The commands that do apply were run:

### `composer validate --strict`
```
./composer.json is valid
```

### `find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l`
All 31 non-vendor PHP files reported `No syntax errors detected`. Filtering the
output for anything that is *not* that line produced zero lines (no errors).

### `vendor/bin/phpunit`
```
PHPUnit 10.5.64 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.8
.......................................                           39 / 39 (100%)

Time: 00:00.031, Memory: 8.00 MB

OK (39 tests, 89 assertions)
```

### `vendor/bin/phpcs --report=summary`
```
............................... 31 / 31 (100%)

Time: 1.65 secs; Memory: 14MB

PHPCS_EXIT=0
```
Zero errors, zero warnings across 31 files. Standard is defined in `phpcs.xml.dist`
(WordPress + PHPCompatibilityWP).

## Baseline result table (canvas §5.3)

| Item | Result |
|------|--------|
| Current branch | `main` |
| Current commit | `87847488aeb941915b2f9b9488085e31f1583f0a` |
| Installation (`composer install`) | Succeeds; `vendor/` present, dev tools resolve. |
| Composer validation | `./composer.json is valid` (`--strict`). |
| Activation | Not executed — no live WordPress in checkout (documented boundary). Activation path (`Plugin::on_activate`) reviewed by reading: registers types, runs migrations, flushes rewrites. |
| Deactivation | Not executed — documented boundary. `Plugin::on_deactivate` reviewed: flushes rewrites only, preserves data and capabilities. |
| Build | Not applicable — no JS/build pipeline exists in this repository. |
| Test result | PASS — 39 tests, 89 assertions, PHPUnit 10.5.64. |
| Coding-standard result | PASS — PHPCS/WPCS, 31 files, 0 issues. |
| Static-analysis result | Not executed — no PHPStan config present (documented boundary; PHP `-l` syntax lint passes on all files). |
| Known security findings | None found in review. Reduced attack surface (no REST/AJAX/upload/SQL/remote). See THREAT-MODEL.md. |
| Known performance findings | None in current scope; health screen uses `wp_count_posts` per model (bounded by 3 models). No unbounded queries. |
| Known accessibility findings | Not audited against WCAG with tooling/screen reader (documented boundary). Templates/patterns include designed empty states. |
| Known compatibility findings | CI runs PHP 8.1–8.4 (post-change). Live WP-version matrix not executed (documented boundary). |
| Known data-integrity findings | None found. Migration ledger persists per step; downgrade refused loudly. |

## Missing tests (honest gap list)

- No WordPress integration tests (activation, deactivation, `register_post_type`, capability install applied to real roles, migration option persistence). Domain policy is unit-tested; WordPress adapters are verified by reading only.
- No REST tests — no REST routes exist to test.
- No E2E / Playwright editorial-journey tests.
- No accessibility, performance, migration-under-load, or multisite tests.

## Missing documentation (honest gap list)

- Several canvas §23 documents are not yet present (e.g. `docs/architecture/CAPABILITY-MODEL.md`, `docs/operations/ROLLBACK.md`, `docs/security/PRIVACY.md`). Architecture and ADRs exist under `docs/02-…`.
- No machine-readable compatibility report (`artifacts/compatibility/*`) — CI runs the matrix but does not emit the canvas §16.4 report files.

## Existing technical debt / production risks

- `ProgramSchema` is built and unit-tested but **not yet wired to output**; no template echoes the JSON-LD into a `<script type="application/ld+json">`. Documented boundary, not a silent stub.
- Three of the six briefed content models (people, resources, campaigns) are intentionally not registered yet; the registry is honest about the finished set.
- Capability installer runs inside `MigrationRunner` step 2 on `init` when behind; correct and idempotent, but capability changes to already-migrated sites require a schema bump to re-apply.

## Attack surface verification

```
grep -rniE "register_rest_route|wp_ajax|move_uploaded_file|wp_handle_upload|\$wpdb|->query\(|wp_remote_|curl_" wordpress/
→ (no matches)
```
No custom REST write routes, AJAX handlers, upload handling, direct SQL, or
outbound HTTP. This is a deliberately reduced attack surface, carried into the
threat model.
