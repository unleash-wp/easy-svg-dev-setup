# Changelog

## Unreleased

- The README starts with a two-minute path to a first icon, then one short
  section per topic: what it does, an example, the result and a trap.
- Menu names read **Easy SVG → Settings → Features**. One **Collections**
  switch covers sets, folders and packs.
- `filter-registrable-icons.php` and `panel-extension.php` check the plugin's
  API number on `plugins_loaded`, so they also work as mu-plugins.
- The abilities list, the WP-CLI commands and the upload limits match what
  the plugins register. `addon-sanitizer.php` asks for `EASY_SVG_API` 4, the
  number that hardens icons printed inline.
- `sanitizer-allow-list.php` adds `feDropShadow`, an element the sanitizer
  does not allow on its own.
- `presets-catalog.php` is not shipped any more. The Templates catalog is not
  an extension point.
- New: adding icons over REST, `easy_svg_pro_source_file_max_bytes`, share
  links, `wp easy-svg config` and the central configuration file.

## 1.0.0

First version: snippets for Easy SVG Support and Easy SVG Pro, templates for an
icon pack plugin, a theme, ACF, Meta Box, Elementor, WPBakery and PHP-only
blocks, the pack checker and the hook checker.
