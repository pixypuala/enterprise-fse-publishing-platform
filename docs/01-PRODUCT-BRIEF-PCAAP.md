# Product Brief — Enterprise FSE Publishing Platform

## Outcome

Create a public, reproducible reference project that demonstrates the ability to solve a real enterprise publisher, university, nonprofit network, media organization, agency platform team problem from discovery through maintenance.

## Problem and cost

**Problem:** Organizations need editors to publish complex pages quickly, but unrestricted blocks, copied layouts, theme-owned business logic, and editor/frontend differences create drift and defects.

**Cost:** Slow reviews, inconsistent pages, inaccessible content, fragile redesigns, accidental brand changes, plugin lock-in, and expensive maintenance.

## Users and jobs to be done

1. **Primary operator:** completes the central workflow without developer assistance.
2. **Administrator:** configures permissions, integrations, and policy safely.
3. **Developer/maintainer:** updates the system, diagnoses failures, and extends it through documented contracts.
4. **Reviewer/auditor:** verifies security, accessibility, performance, and release evidence.
5. **Recruiter/client:** understands the outcome and the developer's contribution without reading every source file.

## Functional scope

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

## Explicit non-goals for the first release

- no fake production scale or fabricated customers;
- no paid-vendor dependency required to run the core demo;
- no feature that exists only for a resume keyword;
- no hidden setup steps performed manually by the author;
- no broad compliance certification claim;
- no unsupported browser, PHP, WordPress, or provider promise.

## Acceptance outcomes

- The central workflow is documented as Given/When/Then scenarios.
- Every destructive action has authorization, confirmation, auditability where appropriate, and recovery documentation.
- Empty, loading, error, permission-denied, offline/unavailable, and stale-data states are designed.
- Accessibility is tested by keyboard and at least one screen-reader workflow, plus automation.
- Performance budgets are tied to user journeys, not a homepage-only score.
- CI produces useful artifacts when a test fails.
- A tagged release can be installed from a clean environment.
- The case study distinguishes measured results, fixture results, estimates, and unvalidated hypotheses.

## Success measures

Use measurements that the project can truthfully collect:

- task completion and error rate in a small documented usability test;
- regression count detected before release;
- build/test duration and flake rate;
- Core Web Vitals or controlled-lab journey metrics with environment stated;
- accessibility issues by severity and resolution status;
- query count/time for defined requests;
- recovery time during a scripted failure drill;
- external repository clones, issues, pull requests, or stars only as descriptive adoption data, never as quality proof.

## Ask

Review editor/frontend parity, the theme/plugin boundary ADR, and the release evidence; then assess the developer for senior WordPress, Gutenberg, or platform work.
