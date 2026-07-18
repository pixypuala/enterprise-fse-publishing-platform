# Enterprise FSE Publishing Platform

## Portfolio purpose

A production-style WordPress publishing system that proves modern block-theme, plugin, editorial-governance, accessibility, security, performance, and release capabilities.

This project is not considered complete when the UI looks good. It must demonstrate discovery, architecture, code quality, accessibility, security, performance, test design, deployment, recovery, documentation, and public communication.

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
