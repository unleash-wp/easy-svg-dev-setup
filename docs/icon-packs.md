# Icon packs

A pack is a folder of SVG files with a `manifest.json` beside them. Easy SVG
Pro reads it as one collection: in the block editor, the Icon block,
Elementor, WPBakery, ACF, Meta Box and `easy_svg_icon()`. Nothing is
downloaded at runtime; every file is sanitised by Pro on the way in.

The same folder ships three ways:

- **As a plugin**: `templates/icon-pack-plugin/`, installed like any plugin.
- **As a drop-in**: copied to `wp-content/easy-svg-icons/<slug>/`.
- **As a ZIP**: imported under **Icons, Packs, Import a pack**.

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
  `^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$`. An invalid one is skipped and reported
  on the Packs screen, never rewritten. Pages point at `slug/name`, so renaming
  later breaks them.
- **`license`, `author` and `source`** are shown under the collection on the
  Packs screen. Free text, never fetched, never a link.
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
- **No DTD entities** (`<!ENTITY`). They are a known way past sanitisers.
- **Size.** Each file under 2 MB, or Pro skips it. Under 1000 icons per
  folder, or raise `easy_svg_pro_source_max_icons`.
- **Unique names**, each listed once, no two file names that differ only in
  case.
- **Plain files.** `file` is a bare name ending in `.svg`. No symbolic links.
- **Licence fields filled.**
- **Only icons you may pass on.** A licence to use icons on your own site is
  not a licence to ship them in a plugin.

## Things that trip people up

- **Two switches.** Pro reads folders only with **Theme & folder sources** on,
  and hands them to the Icon block only with **Multiple collections** on as
  well. Both are off by default. A pack cannot switch them on.
- **The folder name is a key.** Pro keys folders by their last path segment.
  Name each folder after its collection slug, as the template does, and two
  packs only collide when they really ship the same slug.
- **A plugin and an import of the same slug become one collection.** Use one
  of the two ways per site.
- **Strokes survive.** Outline icons keep their `stroke`. In the Icon block
  that needs Easy SVG Support 5.4.0 or newer.
- **`currentColor`** lets an icon follow the text colour.

## The folder as a ZIP

Zip the contents flat, so `manifest.json` sits at the top:

```sh
cd icons/starter-shapes && zip -X ../../starter-shapes.zip manifest.json *.svg
```

The import needs **Icon set editor** on. Do not import the plugin ZIP itself:
Pro reads a manifest only at the root of the archive.

## Shipping somebody else's icons

Pro's sanitiser strips comments, so a notice inside the files does not
survive. Put the credit in the manifest.

**Font Awesome Free**, per its own `LICENSE.txt`: the SVG icons are CC BY 4.0,
attribution to Fonticons, Inc. (https://fontawesome.com) is a condition, and
changes should be indicated, for example
`"source": "Font Awesome Free 7.3.1, sanitised on import"`. Brand icons are
trademarks of their owners; think twice before shipping `brands`.

## Updates

The template has no updater, so a pack gets new versions by being replaced.
Pro's own plugin export uses the export time as the version, so a later export
always installs over an earlier one.
