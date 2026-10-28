#!/usr/bin/env bash
# Zip a pack plugin for upload: bash tools/build-pack.sh [plugin dir] [out dir]
# Every collection folder is checked first; one problem and nothing is written.
set -euo pipefail
cd "$(dirname "$0")/.."

src="${1:-templates/icon-pack-plugin}"
out="${2:-dist}"
main=$(grep -l '^ \* Plugin Name:' "$src"/*.php | head -n1)
[ -n "$main" ] || { echo "no plugin file in $src"; exit 1; }
slug=$(basename "$main" .php)

for dir in "$src"/icons/*/; do
  php tools/check-pack.php "${dir%/}"
done

stage=$(mktemp -d)
trap 'rm -rf "$stage"' EXIT
mkdir -p "$stage/$slug" "$out"
cp -R "$src"/. "$stage/$slug/"
rm -f "$out/$slug.zip"
(cd "$stage" && zip -qrX - "$slug") > "$out/$slug.zip"
echo "$out/$slug.zip"
