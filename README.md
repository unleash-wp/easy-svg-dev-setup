# Easy SVG dev setup

Snippets and templates for developers and agencies building with
[Easy SVG Support](https://wordpress.org/plugins/easy-svg/) and Easy SVG Pro.
Copy what you need into a theme, a plugin or a mu-plugin.

Everything here is checked against the plugins' own code: every hook,
function and constant a snippet uses exists there, with the arguments shown.

## Start in five minutes

1. Install and activate **Easy SVG Support**. Under **Icons, Settings** switch
   on SVG uploads and the icon library.
2. Add an icon under **Icons, Icon library**, for example `heart`.
3. Print it from your theme:

   ```php
   if ( function_exists( 'easy_svg_icon' ) ) {
       echo easy_svg_icon( 'easy-svg/heart', array( 'label' => __( 'Liked', 'acme' ) ) );
   }
   ```

   Or without PHP: `[easy_svg_icon name="easy-svg/heart"]`.
4. With **Easy SVG Pro**: switch on **Theme & folder sources** under
   **Icons, Features**, put SVGs and a `manifest.json` into your theme's
   `assets/icons/` (copy `templates/theme/assets/icons/`), and use
   `easy_svg_icon( 'acme-theme/circle' )`.
5. Need your icons in a builder or a field? Pick the template below and switch
   on its integration under **Icons, Features**.

An icon is always addressed as `collection/slug`. The free library is
`easy-svg`.

Every hook snippet carries a plugin header: copy the file into
`wp-content/mu-plugins/` and it runs. `render-an-icon.php` is template code;
paste its lines where the icon belongs.

## What is in here

```
snippets/free/   hooks and functions of Easy SVG Support
snippets/pro/    hooks, WP-CLI and abilities of Easy SVG Pro
templates/       copy and rename
  icon-pack-plugin/   your own icon pack as a plugin
  theme/              theme icons, with and without Pro
  acf/                ACF or Secure Custom Fields icon picker
  metabox/            Meta Box field type easy_svg_icon
  elementor/          an Elementor widget with an icon control
  wpbakery/           a WPBakery element with the easy_svg_icon param
  php-only-block/     a PHP-only block with an icon picker (WordPress 7.0+)
docs/icon-packs.md  manifest, security checklist, licences
tools/           pack checker, hook checker
tests/           php tests/run.php
```

## Easy SVG Support (free)

| File | What |
|---|---|
| `render-an-icon.php` | `easy_svg_icon( $name, $args )`: class, size, label, decorative; the shortcode |
| `filter-icon-markup.php` | `easy_svg_icon_markup`: change an icon on its way out, or serve a collection of your own |
| `filter-registrable-icons.php` | `easy_svg_registrable_icons`: what WordPress's Icon block gets |
| `sanitizer-allow-list.php` | `esw_svg_allowed_tags`, `esw_svg_allowed_attributes`, `esw_svg_remove_remote_references` |
| `upload-limits.php` | `easy_svg_max_mb_ceiling`, `easy_svg_max_bytes`, `easy_svg_max_php_tags`, `easy_svg_max_use_tags`, `easy_svg_respect_network_filetypes` |
| `write-stored-icons.php` | `easy_svg_update_icon_markup()`, `easy_svg_rename_icon()` |
| `addon-sanitizer.php` | `easy_svg_sanitizer()` and `EASY_SVG_API` |
| `panel-extension.php` | `easy_svg_panel_enqueue`, `easy_svg_panel_menu_tabs`, `easy_svg_pro_url`, `EASY_SVG_PANEL_API` |

Only `easy_svg_sanitizer()`, `easy_svg_icon()`, the two write functions and
the hooks above are promised. Everything else in the plugin may change.

## Easy SVG Pro

| File | What |
|---|---|
| `register-collection.php` | `easy_svg_register_icon_collection()` from a folder or inline markup |
| `source-folders.php` | `easy_svg_pro_source_dirs`, `easy_svg_pro_source_max_icons`, `easy_svg_pro_theme_collection_slug` |
| `registered-icon.php` | `easy_svg_pro_registered_icon`: Pro icons on their way to the Icon block |
| `icon-set-events.php` | `easy_svg_pro_set_icons_writing`, `easy_svg_pro_set_icons_written`, `easy_svg_pro_set_write_context`, `easy_svg_pro_set_screen_redirect` |
| `import-and-configurator.php` | `easy_svg_pro_import_max_entries`, `easy_svg_pro_import_max_total_bytes`, `easy_svg_pro_import_font_max_bytes`, `easy_svg_pro_dangerous_tags`, `easy_svg_pro_dangerous_attrs` |
| `presets-catalog.php` | `easy_svg_pro_presets_endpoint`, `easy_svg_pro_presets_token`, for staging and tests |
| `abilities.php`, `abilities.md` | the 60 abilities under `easy-svg-pro/` |
| `wp-cli.md` | `wp easy-svg icons`, `wp easy-svg pack`, `wp easy-svg fa` |

Every Pro feature is off until you switch it on under **Icons, Features**,
and most need an active licence.

## Templates

**Icon pack plugin.** Rename `acme-icons.php`, the folder and the text domain.
Each folder under `icons/` is one collection, named by its `manifest.json`.
This is the layout Pro writes when you export collections "As a plugin".
Check and zip it in one step; one problem in any folder and no ZIP is written:

```sh
bash tools/build-pack.sh templates/icon-pack-plugin dist
```

Details, the manifest fields and the licence rules for other people's icons:
[docs/icon-packs.md](docs/icon-packs.md).

**Builders and fields.** Each integration has its own switch under
**Icons, Features** and stores the same thing: the icon's address. Render it
with `easy_svg_icon()`, never with Pro's internal functions, so a page keeps
working when Pro is deactivated.

| Host | Switch | You write |
|---|---|---|
| ACF, Secure Custom Fields | ACF icon picker | an `icon_picker` field with the tab `easy_svg` |
| Meta Box | Meta Box icon field | a field of type `easy_svg_icon` |
| Elementor | Elementor icon picker | any `Controls_Manager::ICONS` control |
| WPBakery | WPBakery icon element | a param of type `easy_svg_icon` |
| Block editor | PHP-only block icon field | an attribute with `'unleashwp' => array( 'component' => 'icon' )` |

Anywhere else the shortcode works: `[easy_svg_icon name="acme/front-door"]`.

## Checks

```sh
php tests/run.php
bash tools/test-check-pack.sh
phpcs
php tools/check-hooks.php ../easy-svg ../easy-svg-pro
```

- `tests/run.php` loads every snippet and template against small stubs and
  runs its callbacks. CI runs it on every pull request, with the pack checks
  and the WordPress Coding Standards (`phpcs.xml.dist`).
- `check-hooks.php` reads the plugins' source and fails when a snippet names a
  hook, function or constant that is not there. Run it against the versions
  you support.
- `tests/smoke-wordpress.php` runs the templates in a real site with Free, Pro,
  ACF, Meta Box, Elementor and WPBakery active, the `acme-icons` plugin, a
  theme carrying `templates/theme/` and the templates loaded from a
  mu-plugin:

  ```sh
  wp eval-file tests/smoke-setup.php
  wp eval-file tests/smoke-wordpress.php --user=admin
  ```

## Licence

GPL-2.0-or-later, see `LICENSE`. Copy freely into client work. Icons you add
keep their own licence.
