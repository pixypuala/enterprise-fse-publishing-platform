#!/usr/bin/env bash
#
# Build the distributable plugin from the repository.
#
# Unlike a single-plugin repo, the plugin here is one directory inside a
# monorepo that also carries the FSE theme, the docs set, the test suite, and
# the JS toolchain. What ships is only wordpress/plugins/enterprise-publishing/,
# minus everything in .distignore — the TypeScript block sources in blocks/,
# whose compiled output in build/ is the code WordPress actually executes.
#
# The plugin has no runtime Composer dependencies (see composer.json: php only),
# so no vendor/ is shipped and the PSR-4 fallback autoloader in the plugin
# bootstrap is the loading path in production.
#
# The block build output is required, not optional: without build/ the plugin
# registers no block. The script refuses to produce a silently degraded artifact.
#
# Run the project's block build first (the package.json "build" script).
#
# Usage:
#   bin/build-plugin.sh          # build dist/enterprise-publishing/
#   bin/build-plugin.sh --zip    # ...and dist/enterprise-publishing.zip
#
set -euo pipefail

SLUG="enterprise-publishing"
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE="${ROOT}/wordpress/plugins/${SLUG}"
DIST="${ROOT}/dist"
TARGET="${DIST}/${SLUG}"

if [ ! -r "${SOURCE}/build/program-list/block.json" ]; then
	echo "error: compiled block output missing at ${SOURCE}/build." >&2
	echo "       Run the project's block build (the package.json \"build\" script) first." >&2
	exit 1
fi

rm -rf "${TARGET}"
mkdir -p "${TARGET}"

# Build the rsync exclude list from .distignore, ignoring blanks and comments.
EXCLUDES=()
while IFS= read -r line; do
	[ -z "${line}" ] && continue
	case "${line}" in \#*) continue ;; esac
	EXCLUDES+=("--exclude=${line}")
done < "${ROOT}/.distignore"

rsync -a "${EXCLUDES[@]}" "${SOURCE}/" "${TARGET}/"

echo "Built ${TARGET}"

if [ "${1:-}" = "--zip" ]; then
	( cd "${DIST}" && rm -f "${SLUG}.zip" && zip -qr "${SLUG}.zip" "${SLUG}" )
	echo "Packaged ${DIST}/${SLUG}.zip"
fi
