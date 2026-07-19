# Stack commands

Machine-readable command list for the JavaScript/TypeScript block toolchain.
Each line below is `key: command`. Automation and delivery gates read these keys
directly, so keep the format stable.

The PHP domain suite (PHPUnit, PHPCS/WPCS) is driven through Composer and
documented in `README.md`; the keys here cover the block build tooling only.

## Package manager

This project standardises on pnpm via Corepack. Install dependencies with:

```bash
corepack pnpm install --ignore-workspace
```

The pnpm lockfile is intentionally not tracked (see `.gitignore`); versions are
pinned by ranges in `package.json`.

## Commands

lint: corepack pnpm run lint
type-check: corepack pnpm run type-check
test-unit: corepack pnpm run test:unit
build: corepack pnpm run build

## What each command does

- `lint` — ESLint (via `wp-scripts lint-js`) over the block sources, with the
  `@wordpress/scripts` flat config and Prettier formatting rules.
- `type-check` — `tsc --noEmit` against `tsconfig.json` (strict mode).
- `test-unit` — Jest (via `wp-scripts test-unit-js`) for the framework-free
  block logic (`filter.ts`).
- `build` — compiles the blocks into `wordpress/plugins/enterprise-publishing/build/`
  (editor script, Interactivity API view module, copied `block.json` and
  `render.php`, plus asset manifests). Run `corepack pnpm run start` for the
  watch-mode equivalent during development.
