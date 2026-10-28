#!/usr/bin/env bash
# The checker has to refuse a pack that breaks every rule, each rule on its own
# file, or a rule that stopped firing would go unnoticed behind the others.
set -euo pipefail
cd "$(dirname "$0")/.."

t=$(mktemp -d)
trap 'rm -rf "$t"' EXIT
cp -R tools/fixtures/bad-pack/. "$t/pack"
# Two cases a checkout cannot carry everywhere: a link, and names that differ
# only in case (a case-insensitive disk cannot hold both).
ln -s /etc/hosts "$t/pack/link.svg"
cp "$t/pack/onload.svg" "$t/pack/OnLoad.svg" 2>/dev/null || true
case_sensitive=$(ls "$t/pack" | grep -ci '^onload\.svg$' || true)

status=0
php tools/check-pack.php "$t/pack" --max-bytes=512 --max-icons=9 2> "$t/out" || status=$?
cat "$t/out"
[ "$status" -eq 1 ] || { echo "expected exit 1, got $status"; exit 1; }

missing=0
while IFS= read -r expect; do
  [ -n "$expect" ] || continue
  # Clash needs a case-sensitive disk; macOS skips it, CI does not.
  if [ "$expect" = ": file-name-clash" ] && [ "$case_sensitive" -lt 2 ]; then
    echo "skipped here (case-insensitive disk): $expect"; continue
  fi
  # The whole reason, not a prefix of a longer one: `duplicate` is not `duplicate-file`.
  awk -v e="$expect" 'index($0, e " (") || substr($0, length($0) - length(e) + 1) == e { f = 1 } END { exit !f }' "$t/out" \
    || { echo "NOT REPORTED: $expect"; missing=1; }
done < tools/fixtures/bad-pack.expected
[ "$missing" -eq 0 ] && echo "every rule fired"
exit "$missing"
