# Enterprise FSE Publishing Platform

## Portfolio purpose

A production-style WordPress publishing system that proves modern block-theme, plugin, editorial-governance, accessibility, security, performance, and release capabilities.

This project is not considered complete when the UI looks good. It must demonstrate discovery, architecture, code quality, accessibility, security, performance, test design, deployment, recovery, documentation, and public communication.

## Getting started

Requires PHP 8.1+ and Composer.

```bash
composer install     # install dev tooling (PHPUnit, PHPCS, WPCS)
composer test        # run the pure-domain unit suite (no WordPress needed)
composer lint        # WordPress coding standards (PHPCS)
composer lint:fix    # auto-fix fixable standards violations
```

To run the live demo in the browser with [WordPress Playground](https://wordpress.org/playground/)
(Node 18+), mounting the theme and plugin from this checkout:

```bash
npx @wp-playground/cli@latest server \
  --blueprint=playground/blueprint.json \
  --mount=wordpress/themes:/wordpress/wp-content/mu-mount/themes \
  --mount=wordpress/plugins:/wordpress/wp-content/mu-mount/plugins
```

### Repository layout

| Path | Owns |
|------|------|
| `wordpress/plugins/enterprise-publishing/` | Durable domain: content models, capabilities, migrations, health screen. Survives a theme swap. |
| `wordpress/themes/enterprise-fse/` | Presentation only: `theme.json` tokens, templates, parts, patterns. |
| `tests/unit/` | Framework-free unit tests for the domain policy. |
| `playground/` | Portable browser demo blueprint. |
| `docs/` | Product brief, architecture/ADRs, quality/security/a11y/perf, test & release plan. |

## Implementation status

Honest snapshot — what runs today versus what is planned. Planned items are
tracked in the internal roadmap; nothing here is claimed as done until it is.

**Built and tested**

- Theme/plugin boundary: content models live in the plugin, presentation in the theme.
- Three governed content models (Program, Event, Story) via a single data-driven registry.
- Server-authoritative capability matrix (contributor/editor/administrator) — unit-tested privilege boundaries.
- Versioned, idempotent migration ledger with downgrade protection.
- Admin health/status screen (models, post counts, schema version).
- Schema.org JSON-LD structured-data builder for programs (`src/Seo/ProgramSchema.php`) plus a framework-free, script-safe renderer (`src/Seo/JsonLdScript.php`, `JSON_HEX_TAG`/`JSON_HEX_AMP` so `<`/`&` can never break out of the tag) — unit-tested; the `wp_head` output (`ProgramSchemaHead`) is thin guarded glue.
- Privacy export/erase data-shapers (`src/Privacy/`) — framework-free classes that build the WordPress personal-data exporter structure and the eraser plan (published content retained as a business record with an honest reason; unpublished traces removable), unit-tested; the exporter/eraser filter registration (`PrivacyRegistrar`) is thin guarded glue.
- Optional AI content-assistant seam (`src/Ai/`) — a framework-free interface with a disabled-by-default null implementation and one example adapter shape; enable AND permission both required, no external calls, unit-tested.
- FSE block theme: design tokens, header/footer parts, index/single/archive templates, hero + program-card-grid patterns with designed empty states.
- CI (PHP 8.1 / 8.2 / 8.3 / 8.4): composer validate, PHP lint, PHPCS/WPCS, PHPUnit.

### Evidence artifacts

Reproducible, command-grounded evidence for this build (WordPress-Proof canvas):

- [`docs/audit/BASELINE-AUDIT.md`](docs/audit/BASELINE-AUDIT.md) — discovery inventory + real install/test/standards/static results and honest gap lists.
- [`docs/security/THREAT-MODEL.md`](docs/security/THREAT-MODEL.md) — assets, boundaries, and threat table grounded in the actual code (reduced attack surface: no REST/AJAX/upload/SQL/remote).
- [`docs/audit/FINAL-AUDIT.md`](docs/audit/FINAL-AUDIT.md) — per-item PASS/N-A/NOT-VERIFIED review, honest quality scorecard, and maturity classification.
- [`docs/audit/RELEASE-EVIDENCE.md`](docs/audit/RELEASE-EVIDENCE.md) — version/commit/PHP results plus an explicit "not executed" section with accepted residual risks.

**Planned (not yet built)**

- Custom server-rendered blocks with the Interactivity API (filters, tabs, accordions).
- Editor TypeScript/React interfaces and block unit tests.
- Playwright editorial journeys, WCAG 2.2 AA audit record, performance budgets.
- Live wiring that still needs a running WordPress: the privacy record collector and JSON-LD head output verified in-browser, and a concrete provider-backed AI adapter behind the seam.

## PCAAP

### Problem

Organizations need editors to publish complex pages quickly, but unrestricted blocks, copied layouts, theme-owned business logic, and editor/frontend differences create drift and defects.

### Cost

Slow reviews, inconsistent pages, inaccessible content, fragile redesigns, accidental brand changes, plugin lock-in, and expensive maintenance.

### Answer

Build a generic enterprise block theme plus a portable site-core plugin. Use theme.json, templates, template parts, patterns, server-rendered dynamic blocks, the Interactivity API, typed content models, role-aware editorial controls, migrations, and CI.

### Advantage

The theme remains presentation-focused while durable content rules and workflows live in the plugin. Editors receive governed flexibility instead of either rigid templates or unrestricted chaos.

### Proof required

- fresh install from a Playground blueprint
- editor-to-frontend visual parity set
- PHPUnit and block unit tests
- Playwright editorial journeys
- WCAG 2.2 AA audit record
- performance budgets and query profile
- security threat model and abuse tests
- migration and rollback demonstration

### Ask

Review editor/frontend parity, the theme/plugin boundary ADR, and the release evidence; then assess the developer for senior WordPress, Gutenberg, or platform work.

## Intended audience

enterprise publisher, university, nonprofit network, media organization, agency platform team.

## Core stack and capabilities

- WordPress 7.x compatibility with a documented support window
- PHP 8.2+ with Composer and PSR-4
- block theme, theme.json, patterns, templates and template parts
- multi-block site-core plugin using block.json
- Interactivity API for frontend interaction; no custom legacy AJAX
- TypeScript/React for editor interfaces
- @wordpress/build or wp-scripts with a documented migration path
- PHPUnit, PHPStan, WPCS/PHPCS, ESLint, Stylelint, Playwright
- wp-env and WordPress Playground blueprints
- GitHub Actions and release artifacts

## Product scope

- governed page templates and locked structural zones
- reusable synced/unsynced patterns with clear ownership
- programs, events, stories, people, resources, and campaigns content models
- dynamic cards, filters, accordions, tabs, statistics and related-content blocks
- editor guidance, content checks, preview states and empty-state design
- role/capability matrix and approval workflow
- SEO metadata and structured data without theme lock-in
- privacy, retention and export/delete handling
- health/status screen and migration version reporting
- optional AI adapter that is disabled by default, permission-bound, logged, and replaceable

## Major risks

- overbuilding editorial workflow without user research
- putting content models in the theme
- using block locking as a substitute for clear editor UX
- claiming accessibility from automated tools alone
- shipping experimental WordPress APIs without fallbacks
- loading every block asset globally

## Milestone order

1. problem interviews and editorial task map
2. information architecture and content model
3. design tokens, templates and base theme
4. site-core plugin and first three dynamic blocks
5. interactive and filtered experiences
6. security, accessibility and performance hardening
7. migration, release and rollback
8. public case study and community extraction

## Public repository opportunity

Extract the generally useful portion as `wp-enterprise-fse-starter`. The public repository must have an open-source license, contribution guide, security policy, support boundary, reproducible local setup, release notes, and a roadmap that distinguishes committed work from ideas.

## Definition of portfolio-ready

- a stranger can run the project from a fresh clone;
- every major claim links to a test, report, trace, screenshot, or explicit limitation;
- no production credentials, personal data, copied proprietary code, or fake testimonials exist;
- repository issues reflect honest known gaps;
- the demo includes at least one controlled failure and recovery;
- architecture decisions explain alternatives and tradeoffs;
- the case study can be understood by both technical and nontechnical readers;
- the latest tagged release passes the documented support matrix.
