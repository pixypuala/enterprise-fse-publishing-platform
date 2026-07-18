# Threat Model

> Canvas §24. Grounded in the real code at commit
> `aaf9b7bf9ed0e459b2a51eeee5d8d3fcff98b2fb`. Mitigations cite actual files.
> Where a mitigation is designed but not yet test-proven, the residual risk says
> so — no mitigation is claimed as verified beyond what the unit suite covers.

## Scope and a key fact about attack surface

This plugin **exposes no custom REST write routes, no AJAX handlers, no upload
handling, no direct SQL, and no outbound HTTP requests.** Verified:

```
grep -rniE "register_rest_route|wp_ajax|move_uploaded_file|wp_handle_upload|\$wpdb|->query\(|wp_remote_|curl_" wordpress/
→ (no matches)
```

The entire classes of injection, SSRF, unsafe-upload, CSRF-on-custom-endpoint,
and IDOR-on-custom-route threats therefore have **no surface in this build**.
Content flows through WordPress core (post types registered with `show_in_rest`),
so those paths inherit core's own authorization and nonce handling rather than
this plugin re-implementing them. This is the single most important property of
the threat model and it materially reduces risk.

## Assets being protected

- **Editorial content** in the governed post types (`ep_program`, `ep_event`, `ep_story`).
- **The authorization policy itself** — the role→capability grant table. Its integrity is the product's core value; a wrong grant is a governance failure.
- **Schema/migration version option** (`enterprise_publishing_schema_version`) — data-integrity anchor.
- **Role capability sets** on contributor/editor/administrator.

## Trusted vs. untrusted

| Trusted | Untrusted |
|---------|-----------|
| WordPress core APIs (`register_post_type`, roles/caps, options, `current_user_can`, `wp_die`, escaping funcs). | Any HTTP request actor, including authenticated low-privilege users. |
| Composer-installed dev tooling (not shipped to production). | The value stored in the schema-version option (treated as possibly-tampered — see downgrade threat). |
| The pure domain layer (deterministic, unit-tested). | Post/program field values consumed by `ProgramSchema` once wiring exists. |

## Untrusted input in this build

Minimal by design. The only runtime inputs are:
- The admin health screen request (gated by `manage_options` **at render**, not just on the menu registration).
- The stored schema-version option read by `MigrationRunner`/`SchemaVersion` (cast to `int`; a too-high value triggers the downgrade refusal).
- Future: `ProgramSchema::build()` input array (already validates required `name`, trims/omits empties) — not yet fed by live post data.

## User types and privilege boundaries

| User type | Governed rights (from `CapabilityMap`) |
|-----------|----------------------------------------|
| Anonymous visitor | Read published content only (core front-end). No plugin admin surface. |
| Contributor | Create/edit/delete **own drafts** of each governed model. **Cannot publish**, cannot touch others' or published content. |
| Editor | Contributor rights + publish + edit/delete **others'** content, but **cannot delete published** content (guards accidental brand loss). |
| Administrator | Every governed capability, including deleting published content, plus `manage_options` (health screen). |

The per-model **custom capability base** (`ContentModel::capability_base()`,
applied via `capability_type` + `map_meta_cap` in `ModelRegistrar`) is the
privilege boundary: granting rights on Programs never leaks rights on core Posts.

## Attack surfaces / abuse cases and data flows

1. **Admin health screen** — `admin_menu` → `HealthScreen::render()`. Flow: capability re-check → read option → `wp_count_posts` per model → escaped table output.
2. **Migration on `init`** — read option → compute pending → apply step(s) → persist. No user input drives it beyond the stored version.
3. **Capability install** — writes computed grants to roles; idempotent.
4. **Content authoring** — handled entirely by WordPress core using the plugin's custom capabilities; the plugin adds no custom write endpoint.

## Threat table

| # | Threat | Likelihood | Impact | Existing mitigation (real code) | Residual risk |
|---|--------|-----------|--------|----------------------------------|---------------|
| T1 | Privilege escalation — a role gains a destructive capability it should not have | Low | High | `CapabilityMap` computes grants in pure PHP; `CapabilityMapTest` asserts contributor cannot publish, editor cannot delete published, only admin gets `delete_published_*`. Custom per-model base prevents core-post leakage. | Policy is unit-tested but **not proven applied to live roles** (no integration test that `CapabilityInstaller` writes exactly these caps). Medium until an integration test exists. |
| T2 | Broken authorization on the admin screen (menu present but page renders for the unprivileged) | Low | Medium | `HealthScreen::render()` re-checks `current_user_can('manage_options')` and `wp_die`s otherwise — defence in depth beyond the menu capability. | Not E2E-verified; logic reviewed and standard WP pattern. Low. |
| T3 | Schema downgrade / data corruption from a stale or tampered version option | Low | High | `SchemaVersion::pending()` throws `OutOfRangeException` when `installed > CURRENT`, refusing to run backward. `SchemaVersionTest` covers it. Runner persists after each step (resume-safe). | Refusal is unit-proven; behaviour of an interrupted live migration not integration-tested. Low–Medium. |
| T4 | Invalid post-type key silently truncated/ignored by WordPress | Low | Medium | `ContentModel::assert_valid_key()` throws on empty, >20 chars, or bad charset before WordPress ever sees it. `ContentModelTest` covers rejects. | None material — fails loudly at construction. Low. |
| T5 | Stored XSS via governed content on the health screen | Low | Medium | All dynamic values escaped on output (`esc_html`, `esc_html__`, integer casts, `<code>` around escaped keys). | Reviewed, not automated-scanned. Low. |
| T6 | Injection (SQLi) | N/A | — | No custom SQL anywhere (grep-verified); core handles all storage. | No surface. |
| T7 | SSRF / unsafe outbound request | N/A | — | No `wp_remote_*` / cURL (grep-verified). | No surface. |
| T8 | Unsafe file upload / path traversal / zip-slip | N/A | — | No upload handling (grep-verified). | No surface. |
| T9 | CSRF on a state-changing plugin endpoint | N/A | — | Plugin exposes no custom state-changing browser endpoint; content edits use core (core nonces). | No plugin-owned surface. |
| T10 | Malformed structured data poisoning search/aggregators | Low | Low | `ProgramSchema::build()` asserts non-empty `name`, omits empty properties. Unit-tested. | Not yet wired to output; when wired, output escaping (`wp_json_encode` in a script context) must be verified. Deferred boundary. |
| T11 | Supply-chain (dev dependency compromise) | Low | Medium | Dev-only deps (PHPUnit/PHPCS/WPCS); `composer.lock` committed; none shipped to production. | No automated dependency-audit / secret-scan in CI yet. Medium. |
| T12 | Multisite cross-tenant capability/data leakage | Low | Medium | Installer skips roles absent on an install rather than fabricating; no cross-site iteration. | Multisite behaviour **not executed/tested** (documented boundary). Medium. |

## Logging, backup, supply-chain notes

- **Logging risk:** the plugin writes no logs and stores no secrets, so no
  sensitive-log-leakage vector exists in this build.
- **Backup risk:** only a small integer option and standard post data are owned;
  no bespoke backup handling required.
- **Supply chain:** see T11 — add dependency audit + secret scanning to CI as the
  next hardening step.

## Required next mitigations (not yet done)

- Integration tests proving `CapabilityInstaller` applies exactly the mapped caps to real roles (closes T1 residual).
- Migration integration/interruption tests (closes T3 residual).
- Multisite behaviour tests (closes T12).
- CI dependency audit + secret scanning (closes T11).
- Output-escaping verification once `ProgramSchema` is wired (closes T10).
