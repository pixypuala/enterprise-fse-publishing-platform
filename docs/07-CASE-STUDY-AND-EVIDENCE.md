# Public Case Study — Enterprise FSE Publishing Platform

## Headline

Use an outcome that can be proven. Avoid “revolutionary,” “perfect,” “unbreakable,” or “enterprise-grade” without defined criteria.

## Required structure

### 1. Context

Describe the reference organization and clearly label it as a fixture, fictional scenario, internal project, client-approved case, or production system.

### 2. PCAAP

- **Problem:** Organizations need editors to publish complex pages quickly, but unrestricted blocks, copied layouts, theme-owned business logic, and editor/frontend differences create drift and defects.
- **Cost:** Slow reviews, inconsistent pages, inaccessible content, fragile redesigns, accidental brand changes, plugin lock-in, and expensive maintenance.
- **Answer:** Build a generic enterprise block theme plus a portable site-core plugin. Use theme.json, templates, template parts, patterns, server-rendered dynamic blocks, the Interactivity API, typed content models, role-aware editorial controls, migrations, and CI.
- **Advantage:** The theme remains presentation-focused while durable content rules and workflows live in the plugin. Editors receive governed flexibility instead of either rigid templates or unrestricted chaos.
- **Proof:** link directly to reports and tagged code.
- **Ask:** Review editor/frontend parity, the theme/plugin boundary ADR, and the release evidence; then assess the developer for senior WordPress, Gutenberg, or platform work.

### 3. Your contribution

State what you personally designed, implemented, tested, documented, and reviewed. Credit collaborators and upstream projects.

### 4. Architecture decisions

Show one high-level diagram and three decisions with alternatives and tradeoffs.

### 5. Evidence

- fresh install from a Playground blueprint
- editor-to-frontend visual parity set
- PHPUnit and block unit tests
- Playwright editorial journeys
- WCAG 2.2 AA audit record
- performance budgets and query profile
- security threat model and abuse tests
- migration and rollback demonstration

For each metric, include date, version/commit, environment, test data, tooling, sample size, and limitations.

### 6. Failures and changes

Describe at least one design or implementation decision that failed, what evidence exposed it, and how it changed. Honest correction demonstrates senior judgment.

### 7. What remains

List known gaps, deferred work, unsupported use cases, and the evidence needed before expanding claims.

## Evidence directory convention

```text
docs/evidence/
├── release-<version>/
│   ├── test-summary.md
│   ├── compatibility.json
│   ├── accessibility.md
│   ├── performance.md
│   ├── security-review.md
│   ├── screenshots/
│   └── traces/
└── README.md
```
