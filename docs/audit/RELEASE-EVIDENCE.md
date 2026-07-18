# Release Evidence

> Canvas §28. Every value here references a command actually run against the
> checkout. Sections the canvas requires but that were **not executed** are
> listed explicitly under "Not executed" with the residual risk accepted — they
> are never reported as passing.

## Build identity

| Field | Value |
|-------|-------|
| Plugin version (from header) | `0.1.0` |
| Commit SHA (evidence time) | `aaf9b7bf9ed0e459b2a51eeee5d8d3fcff98b2fb` |
| Branch | `main` |
| Evidence timestamp (UTC) | `2026-07-18T14:58:36Z` |
| Build environment | Local: `PHP 8.5.8 (cli)`, Composer v2, PHPUnit `10.5.64`. CI: `ubuntu-latest`, `shivammathur/setup-php@v2`. |
| Requires WP / Requires PHP | `6.5` / `8.1` |

## Executed results (real command output)

### Composer validation
```
$ composer validate --strict
./composer.json is valid
```

### PHP syntax lint (all 18 non-vendor files)
```
$ find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
(every file: "No syntax errors detected"; zero error lines)
```

### Unit tests
```
$ vendor/bin/phpunit
PHPUnit 10.5.64 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.5.8
....................                                              20 / 20 (100%)
OK (20 tests, 43 assertions)
```

### Coding standards
```
$ vendor/bin/phpcs --report=summary
.................. 18 / 18 (100%)
PHPCS_EXIT=0
```
WordPress Coding Standards + PHPCompatibilityWP (`phpcs.xml.dist`); 0 errors, 0 warnings.

### PHP compatibility matrix (CI)
`.github/workflows/ci.yml` runs the full gate (composer validate → install → PHP
lint → PHPCS/WPCS → PHPUnit) across **PHP 8.1, 8.2, 8.3, 8.4** (`fail-fast:
false`). Local tool run additionally executed cleanly on PHP 8.5.8.

## Result summary table (canvas §28)

| Evidence item | Result |
|---------------|--------|
| Unit-test result | PASS — 20 tests / 43 assertions |
| Coding-standard result | PASS — PHPCS/WPCS, 0 issues, 18 files |
| Static-analysis result | Not executed — no PHPStan config (PHP `-l` lint passes) |
| PHP matrix result | PASS in CI — 8.1 / 8.2 / 8.3 / 8.4 |
| Composer validation | PASS — valid (`--strict`) |

## Not executed — accepted residual risks

The following canvas §28 items were **not run**. They are documented boundaries,
not passes; the accepted residual risk is stated for each.

| Item | Status | Accepted residual risk |
|------|--------|------------------------|
| WordPress version matrix | Not executed | Runtime behaviour on specific WP versions unproven; header declares WP 6.5+ but no live-boot matrix ran. |
| Dependency matrix | Not executed | No third-party runtime dependencies exist to matrix; dev deps pinned via `composer.lock`. |
| Integration tests | Not executed | WordPress adapters (activation, `register_post_type`, capability application, migration option persistence) verified by review only. |
| E2E result | Not executed | No browser/Playwright coverage of editorial journeys. |
| Security-test result | Not executed (automated) | No automated security scan; manual review + reduced attack surface documented in THREAT-MODEL.md. |
| Accessibility result | Not executed | No WCAG/axe/screen-reader audit; conformance unknown. |
| Performance result | Not executed | No profiling/budget evidence; review found no unbounded queries. |
| Migration result (live) | Not executed | Downgrade refusal + ordering unit-tested; live upgrade/interruption not integration-tested. |
| Multisite result | Not executed | Multisite behaviour undefined/untested. |
| Plugin Check result | Not executed | `plugin-check` not run. |
| Package checksum / release archive | Not produced | No build-a-zip pipeline yet; canvas §20.2 artifacts absent. |
| Final reviewer approval | Not present | No sign-off recorded. |

## Known limitations

- `ProgramSchema` JSON-LD builder is unit-tested but not yet wired to template output.
- Three briefed content models (people, resources, campaigns) not yet registered.
- No structured logging, Site Health integration, or diagnostics export.

## Rollback artifact

None produced. The schema layer forbids downgrade by design
(`SchemaVersion::pending()` throws on `installed > CURRENT`); a documented
rollback runbook remains outstanding.

## Certification

**No §30 certification statement is issued.** Required tests (E2E, multisite,
accessibility, performance, live migration) were not executed. Per canvas §29 the
build is classified **Standard WordPress Plugin**; see `FINAL-AUDIT.md`.
