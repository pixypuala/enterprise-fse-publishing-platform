# Changelog

All notable changes to this project are documented here. The format is based on
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Repository scaffolding: governance files, docs, and CI skeleton.
- Theme/plugin boundary vertical slice: 3 governed content models, server-authoritative capability matrix, migration ledger, admin health screen.
- Enterprise FSE block theme: design tokens, templates, parts, and patterns.
- Schema.org JSON-LD structured-data builder for programs (`src/Seo/ProgramSchema.php`): framework-free `EducationalOccupationalProgram` mapping with required-name validation and optional-field/provider omission.
- 20 PHPUnit domain tests; PHPCS/WPCS clean; CI on PHP 8.1, 8.2, 8.3, and 8.4.
- WordPress Playground demo blueprint.
- WordPress-Proof canvas evidence set: `docs/audit/BASELINE-AUDIT.md`, `docs/security/THREAT-MODEL.md`, `docs/audit/FINAL-AUDIT.md`, and `docs/audit/RELEASE-EVIDENCE.md` — all grounded in reproducible command output with honest gap lists and documented boundaries.

### Changed
- Widened the CI PHP matrix from `8.1, 8.3` to `8.1, 8.2, 8.3, 8.4`.
