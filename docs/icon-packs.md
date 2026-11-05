# Icon packs

A pack is a folder of SVG files with a `manifest.json` beside them. Easy SVG
Pro reads it as one collection: in the block editor, the Icon block,
Elementor, WPBakery, ACF, Meta Box and `easy_svg_icon()`. Nothing is
downloaded while a page loads; Pro sanitizes every file on the way in.

The same folder ships three ways:

- **As a plugin**: `templates/icon-pack-plugin/`, installed like any plugin.
- **As a drop-in**: copied to `wp-content/easy-svg-icons/<slug>/`.
- **As a ZIP**: imported as an icon set, in **Easy SVG → Icons** or with
  `wp easy-svg pack import`.

## The manifest

```json
{
  "version": 1,
  "slug": "starter-shapes",
  "label": "Starter Shapes",
  "license": "GPL-2.0-or-later",
  "author": "Your name",
  "source": "https://example.com/my-icon-pack",
  "icons": [
    { "file": "circle.svg", "name": "circle", "label": "Circle", "keywords": ["round", "dot"] }
  ]
}
```

- **`name` is the address**, verbatim, and must match
  `^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$`: lower case letters, digits, `-` and
  `_`, not at the start or end. An invalid one is skipped and reported under
  **Easy SVG → Templates** and by `wp easy-svg icons problems`, never
  rewritten. Pages point at `slug/name`, so renaming
  later breaks them.
- **`license`, `author` and `source`** are shown with the collection under
  **Easy SVG → Templates**. Free text, never fetched, never a link.
- **A manifest describes, it does not restrict.** An SVG without an entry
  still loads, named after its file.
- **`version`** is the manifest schema, not your pack's version. Leave it at
  `1`: a higher number makes Pro skip the whole folder.

| Field | Pro | `tools/check-pack.php` |
|---|---|---|
| `icons` | required, not empty | required |
| `version` | optional, `1` | `1` or absent |
| `slug` | optional; an invalid one falls back to the folder name | required, valid |
| `label` | optional | required |
| `license`, `author`, `source` | optional | required |
| per icon `name`, `label`, `keywords` | optional; `name` falls back to the file name | `name` valid and unique |

Pro asks for little. A pack you hand to other people has to say what it is,
so the checker asks for more.

## Security checklist for a pack you share

Run `php tools/check-pack.php <folder>` before every release.
`bash tools/test-check-pack.sh` proves each rule still refuses a bad file.

- **Drawings only.** No `<script>`, no `on…` attributes, no `javascript:`.
- **Nothing from elsewhere.** Every `href`, `src` and `url()` points inside
  the file (`#id`), and no `@import`.
- **No DTD entities** (`<!ENTITY`). They are a known way past sanitizers.
- **Size.** Each file under 2 MB, or Pro skips it
  (`easy_svg_pro_source_file_max_bytes`). Under 1000 icons per folder, or
  raise `easy_svg_pro_source_max_icons`.
- **Unique names**, each listed once, no two file names that differ only in
  case.
- **Plain files.** `file` is a bare name ending in `.svg`. No symbolic links.
- **License fields filled.**
- **Only icons you may pass on.** A license to use icons on your own site is
  not a license to ship them in a plugin.

## Things that trip people up

- **One switch.** Pro reads folders only with **Collections** on, under
  **Easy SVG → Settings → Features**. It is off by default, and a pack cannot
  switch it on. With it on, the icons also reach the Icon block.
- **Up to a minute.** Pro caches each folder's scan. A new or changed file
  shows up within a minute.
- **The folder name is a key.** Pro keys folders by their last path segment.
  Name each folder after its collection slug, as the template does, and two
  packs only collide when they really ship the same slug.
- **A taken slug gets a number.** Import `acme` twice and the second one is
  `acme-2`. Pages keep pointing at the first.
- **Strokes survive.** Outline icons keep their `stroke`, also in the Icon
  block.
- **`currentColor`** lets an icon follow the text color.

## The folder as a ZIP

Zip the contents flat, so `manifest.json` sits at the top:

```sh
cd icons/starter-shapes && zip -X ../../starter-shapes.zip manifest.json *.svg
```

The import needs **Collections** on. A ZIP that Pro exported as a plugin
imports as well: each collection becomes one set.

## Shipping somebody else's icons

Pro's sanitizer strips comments, so a notice inside the files does not
survive. Put the credit in the manifest.

**Font Awesome Free**, per its own `LICENSE.txt`: the SVG icons are CC BY 4.0,
attribution to Fonticons, Inc. (https://fontawesome.com) is a condition, and
changes should be indicated, for example
`"source": "Font Awesome Free, sanitized on import"`. Brand icons are
other companies' trademarks; think twice before shipping `brands`.

## Updates

The template has no updater, so a pack gets new versions by being replaced.
Pro's own plugin export uses the export time as the version, so a later export
always installs over an earlier one.
