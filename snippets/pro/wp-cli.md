# Easy SVG Pro on the command line

Switch on **WP-CLI** under **Easy SVG > Settings > Features**. Commands that
store an icon set (`pack import`, `fa apply`) also need **Collections**, and
`share` needs **Share links**. A command whose switch is off stops with an
error that names it.

## Try it

```sh
wp easy-svg icons list
```

```
collection  label        count  origin       problems
acme        ACME Icons   3      dropin acme  0
easy-svg    Easy SVG     2      library      0
```

`easy-svg` is the free library. Each other row is a collection you can print
with `easy_svg_icon( 'acme/circle' )`.

## Collections

```sh
# Every file a folder could not use, and why
wp easy-svg icons problems

# Check a folder or one file without importing; --strict exits with 1 on a skip
wp easy-svg icons validate ./icons --strict

# Copy a folder into wp-content/easy-svg-icons/acme as the collection "acme"
wp easy-svg icons import ./icons --collection=acme --label="ACME Icons"
```

`icons import` copies the files. The collection appears on the next page
load; `--collection` decides its name, whatever the folder's manifest says.

## Packs

A pack is a ZIP of one collection: its SVG files and a `manifest.json`.

```sh
wp easy-svg pack list
wp easy-svg pack list --format=json

# One collection as a pack ZIP
wp easy-svg pack export acme --file=acme.zip

# Read a pack in as a new icon set, optionally under another name
wp easy-svg pack import acme.zip --as=acme-copy

# Several collections as one plugin to install on other sites
wp easy-svg pack export acme acme-copy --as-plugin --name="ACME Icons"
```

The plugin export writes the same layout as `templates/icon-pack-plugin/`:
one folder per collection under `icons/`. Its version is the export time, so
a newer export always installs over an older one.

## Share links

A share link hands one collection to another site. It expires and can be
revoked.

```sh
wp easy-svg share create acme          # prints the link once
wp easy-svg share list                 # id, collection, expiry; never the link
wp easy-svg share revoke <id>
wp easy-svg pack import-link <link>    # on the other site
```

The other site fetches the link only over HTTPS from a public address, so
two local test sites cannot share this way.

## Font Awesome recipes

A recipe picks icons by style and name. You apply it to your own Font
Awesome download, Free or Pro, and get one icon set.

```json
{
  "version": 1,
  "name": "Dental practice",
  "icons": {
    "solid": ["tooth", "phone", "calendar", "location-dot"],
    "regular": ["calendar"]
  }
}
```

```sh
wp easy-svg fa apply dental.json --zip=fontawesome-free-web.zip
wp easy-svg fa apply dental.json --zip=fontawesome-pro-web.zip --as=dental --strict
```

- The set is named from `name` (`dental-practice`), or from `--as`.
- An icon in a second style gets the style as a suffix: `calendar-regular`.
- Without `--strict` a missing icon is a warning and the rest is written.
  With `--strict` nothing is written.
- Styles such as `light` or `duotone` exist only in Font Awesome Pro. A Free
  download lacks them.

## Central configuration

```sh
wp easy-svg config
wp easy-svg config --format=json
```

Shows which switches and settings `wp-content/easy-svg-pro.json` fixes on
this site. See [docs/rollout.md](../../docs/rollout.md).

## In CI

```sh
# A pack folder that would lose a file fails the build
wp easy-svg icons validate wp-content/easy-svg-icons/acme --strict
```

`php tools/check-pack.php <folder>` from this repository needs no WordPress
and is stricter. See [docs/icon-packs.md](../../docs/icon-packs.md).
