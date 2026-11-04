# Changelog

## Unreleased

- The README starts with a two-minute path to a first icon, then one short
  section per topic: what it does, an example, the result and a trap.
- Switch and menu names follow the plugins: **Easy SVG > Settings >
  Features**, and one **Collections** switch for sets, folders and packs.
- `filter-registrable-icons.php` and `panel-extension.php` check the plugin's
  API number on `plugins_loaded`, so they also work as mu-plugins.
- The abilities list, the WP-CLI commands and the upload limits match what
  the plugins register today. Gone: `easy_svg_max_mb_ceiling` and the
  abilities of removed features.
- New: adding icons over REST, `easy_svg_pro_source_file_max_bytes`, share
  links, `wp easy-svg config` and the central configuration file.

## 1.0.0

First version: snippets for Easy SVG Support and Easy SVG Pro, templates for an
icon pack plugin, a theme, ACF, Meta Box, Elementor, WPBakery and PHP-only
blocks, the pack checker and the hook checker.
