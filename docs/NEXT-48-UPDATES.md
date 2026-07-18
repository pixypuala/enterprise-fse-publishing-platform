# Next 48 Updates — 01-ENTERPRISE-FSE-PUBLISHING-PLATFORM

## Why this file exists

This is a sequenced, honest backlog of at least 48 planned updates that keeps the repository genuinely active over time. Each item is a real unit of work (one issue or pull request) that advances capability, testing, security, documentation, or maintenance — not artificial busywork. Items are ordered so that early work unblocks later work, and grouped into six release milestones. Nothing here is claimed as already done: this is the forward plan.

## How to use it

Convert each checkbox into a tracked issue, attach it to the matching milestone, and close it with the pull request that satisfies it. Aim for a steady cadence (for example one to three items per week) so commit history, releases, and changelog entries reflect continuous, verifiable progress. Re-open or add items whenever revalidation, upstream releases, or user reports surface new work.

Total planned updates: **48** across **6** milestones.

## M1 — v0.1 Foundations & scaffolding

- [ ] **#01** Scaffold the block theme with theme.json v3 tokens and a documented style layer
- [ ] **#02** Create the site-core plugin skeleton with autoloading and a bootstrap guard
- [ ] **#03** Define the editorial content model (CPTs, taxonomies, meta) in the plugin, not the theme
- [ ] **#04** Add a locked block-pattern category and register the first three approved patterns
- [ ] **#05** Wire a local dev environment via wp-env with seeded editorial fixtures
- [ ] **#06** Establish coding standards (PHPCS WordPress ruleset, ESLint, Prettier) with a pre-commit hook
- [ ] **#07** Add a CI job that lints PHP, JS, and CSS on every pull request
- [ ] **#08** Publish an initial ADR set covering theme/plugin split and Interactivity API choice

## M2 — v0.2 Core capability

- [ ] **#09** Build a dynamic 'editorial callout' block using the Interactivity API
- [ ] **#10** Implement block variations and locking rules to prevent layout drift
- [ ] **#11** Add a governed template-part system for header, footer, and post templates
- [ ] **#12** Create a role-aware editor experience that hides non-approved blocks
- [ ] **#13** Implement server-rendered block bindings for the content-governance fields
- [ ] **#14** Add a reusable query-loop preset for curated editorial collections
- [ ] **#15** Build an editor sidebar plugin for publishing-readiness checks
- [ ] **#16** Add conditional asset loading so block assets enqueue only when used

## M3 — v0.3 Testing, evidence & negative proof

- [ ] **#17** Add editor/frontend parity snapshot tests for every dynamic block
- [ ] **#18** Introduce Playwright end-to-end tests for the publish workflow
- [ ] **#19** Add a known-bad fixture proving the block-locking guard rejects drift
- [ ] **#20** Add axe-core automated accessibility checks to the E2E suite
- [ ] **#21** Write PHPUnit tests for the content-model registration and capability checks
- [ ] **#22** Add visual regression snapshots for the three flagship patterns
- [ ] **#23** Create an evidence index linking each requirement to its test
- [ ] **#24** Add a coverage gate that blocks merges below the agreed threshold

## M4 — v0.4 Security, compatibility & performance

- [ ] **#25** Run a manual WCAG 2.2 AA audit of the editor and record findings
- [ ] **#26** Add nonce + capability verification tests for every write path
- [ ] **#27** Introduce a Content Security Policy and document allowed sources
- [ ] **#28** Add a performance budget (LCP/CLS/TBT) enforced in CI on key templates
- [ ] **#29** Profile and reduce editor bundle size with code-splitting
- [ ] **#30** Add a WordPress/PHP version support matrix and test the lowest supported pair
- [ ] **#31** Document a rollback procedure for a bad theme/plugin release
- [ ] **#32** Add dependency and secret scanning to the pipeline

## M5 — v0.5 Documentation, DX & adoption

- [ ] **#33** Write a case study documenting one editorial defect prevented by governance
- [ ] **#34** Record a demo script and reproducible Playground blueprint
- [ ] **#35** Publish contributor docs: local setup, standards, and review checklist
- [ ] **#36** Add inline block documentation and a pattern catalog page
- [ ] **#37** Create a migration guide for adopting the governance layer on an existing site
- [ ] **#38** Add an architecture diagram and keep it in sync with the ADRs
- [ ] **#39** Document the editor onboarding path for non-technical publishers
- [ ] **#40** Add a troubleshooting guide for common editor-parity issues

## M6 — v1.0+ Community, release cadence & maintenance

- [ ] **#41** Adopt semantic versioning and a Keep a Changelog changelog
- [ ] **#42** Add protected-tag release automation that attaches the evidence bundle
- [ ] **#43** Set a monthly WordPress-core-compatibility revalidation cadence
- [ ] **#44** Triage and label incoming issues within a documented SLA
- [ ] **#45** Add a quarterly accessibility re-audit to the roadmap
- [ ] **#46** Publish a deprecation policy for blocks and patterns
- [ ] **#47** Add a 'good first issue' set to invite contributors
- [ ] **#48** Schedule dependency-update review and changelog curation each release

## Honesty note

These updates are planned, not completed. They do not assert the software is already built, adopted, certified, bug-free, or secure in every environment. They describe the intended, testable path of work and the cadence for keeping the repository maintained.
