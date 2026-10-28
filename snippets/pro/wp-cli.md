# Easy SVG Pro on the command line

Needs the **WP-CLI** switch under Icons, Features. `pack import` stores an
icon set, so it also needs **Icon set editor**.

## Collections

```sh
# Every collection with its icon count, origin and problems
wp easy-svg icons list

# Every file a folder could not use, and why
wp easy-svg icons problems

# Check a folder or one file without importing; --strict exits non-zero on a skip
wp easy-svg icons validate ./icons --strict

# Import a folder as a collection into wp-content/easy-svg-icons
wp easy-svg icons import ./icons --collection=acme --label="ACME Icons"
```

## Packs

```sh
wp easy-svg pack list
wp easy-svg pack list --format=json

# One collection as a pack ZIP
wp easy-svg pack export acme --file=acme.zip

# Read a pack in as a new icon set, optionally under another slug
wp easy-svg pack import acme.zip --as=acme-copy

# Several collections as one installable plugin for other sites
wp easy-svg pack export acme acme-ui --as-plugin --name="ACME Icons"
```

The plugin export writes the same layout as `templates/icon-pack-plugin/`:
one folder per collection under `icons/`, and its version is the export time.

## Font Awesome recipes

```sh
wp easy-svg fa apply dental.json --zip=fontawesome-free-7.3.1-web.zip
wp easy-svg fa apply dental.json --zip=fontawesome-pro-7.3.1-web.zip --as=dental --strict
```

A recipe:

```json
{
  "version": 1,
  "name": "Dental practice",
  "icons": {
    "light": ["tooth", "phone", "calendar", "location-dot"],
    "duotone": ["tooth"]
  }
}
```

`--strict` writes nothing when the download lacks an icon the recipe names.

## In CI

```sh
# A pack folder that would lose a file fails the build
wp easy-svg icons validate wp-content/easy-svg-icons/acme --strict
```

`php tools/check-pack.php <folder>` from this repository runs without
WordPress and is stricter: see the README.
