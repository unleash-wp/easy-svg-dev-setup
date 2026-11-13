# Easy SVG dev setup

Snippets and templates for building with
[Easy SVG Support](https://wordpress.org/plugins/easy-svg/) (free) and
Easy SVG Pro. Copy what you need into a theme, a plugin or a mu-plugin.

Every hook, function, route and command used here exists in the plugins'
code and was run on a real site. `php tools/check-hooks.php` proves the names
again for the versions you run.

## In 2 minutes

You need WordPress with Easy SVG Support active.

**1. Switch on the icon library.** Open **Easy SVG → Icons** and click
**Switch on the icon manager**, or:

```sh
wp option update easy_svg_settings '{"icons":true}' --format=json
```

**2. Add an icon.** Upload any SVG in **Easy SVG → Icons** and name it
`Heart`. Or send it from code, as an administrator with an
[application password](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/):

```sh
curl -u "admin:APPLICATION PASSWORD" -H "Content-Type: application/json" \
  -d '{"label":"Heart","markup":"<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path d=\"M12 21s-7-4.5-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.5-9.5 9-9.5 9z\" fill=\"currentColor\"/></svg>"}' \
  https://example.com/wp-json/easy-svg/v1/library/add
```

The name becomes the icon's slug, `heart`, and the slug never changes.

**3. Show it.** Pick one:

- In a post: `[easy_svg_icon name="easy-svg/heart" label="Liked"]`
- In the block editor (WordPress 7.1+): the Icon block lists it.
- In a theme:

  ```php
  if ( function_exists( 'easy_svg_icon' ) ) {
      echo easy_svg_icon( 'easy-svg/heart', array( 'label' => __( 'Liked', 'acme' ) ) );
  }
  ```

The result is the SVG itself, inline, ready to style:

```html
<svg class="easy-svg-icon" role="img" focusable="false" aria-label="Liked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">…</svg>
```

**With Easy SVG Pro**, one more minute. Switch on **WP-CLI** and
**Collections** under **Easy SVG → Settings → Features**, then:

```sh
wp easy-svg icons import templates/icon-pack-plugin/icons/starter-shapes --collection=shapes
wp eval 'echo easy_svg_icon( "shapes/circle" );'
```

A whole folder is now a collection named `shapes`, in the same function,
shortcode and Icon block.

## Words used here

- **Icon address**: `collection/slug`, for example `easy-svg/heart`. Every
  page, block and field stores this text, so keep slugs stable.
- **Collection**: any source of icons. The free library is the collection
  `easy-svg`; Pro adds sets, folders and pack plugins.
- **Set**: a collection you edit in Pro, under **Easy SVG → Icons**.
- **Pack**: a folder of SVG files with a `manifest.json`, as a folder, a
  ZIP or a plugin.
- **Templates**: ready-made sets from UnleashWP, under **Easy SVG → Templates**.
- **Ability**: a named action WordPress 6.9+ offers to code and AI clients.

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
  php-only-block/     a block written in PHP only, with an icon picker
docs/icon-packs.md  manifest, security checklist, licenses
docs/rollout.md     Composer, central configuration and WP-CLI for many sites
tools/           pack checker, hook checker
tests/           php tests/run.php
```

Every hook snippet starts with a plugin header: copy the file into
`wp-content/mu-plugins/` and it runs. `render-an-icon.php` is template code;
paste its lines where the icon belongs.

## Easy SVG Support (free)

Only `easy_svg_icon()`, `easy_svg_sanitizer()`, the two write functions, the
hooks below and the `easy-svg/v1` routes are promised. Everything else in the
plugin may change.

### Print an icon

`easy_svg_icon( $name, $args )` returns the inline SVG; `''` when the icon
does not exist. File: `snippets/free/render-an-icon.php`.

```php
echo easy_svg_icon( 'easy-svg/heart', array( 'size' => '24px', 'class' => 'acme-icon' ) );
```

Result: `<svg class="easy-svg-icon acme-icon" style="--easy-svg-icon-size:24px" aria-hidden="true" …>`.
Without a `label` the icon is decorative and hidden from screen readers.

Trap: an SVG with `width` or `height` on its root keeps that size and ignores
`size`. Remove the two attributes from the file.

### Add icons from code

`POST /wp-json/easy-svg/v1/library/add` with a JSON body `label` and
`markup`, as shown in step 2. The answer lists the library, with the new
icon under `added` (`id`, `slug`). Every route needs `manage_options`.

Trap: send JSON. Form fields are ignored and the answer is "That name cannot
be turned into an icon name."

### Change an icon on its way out

`easy_svg_icon_markup( $markup, $slug, $collection )` filters every icon
that `easy_svg_icon()`, the shortcode and page builder elements print. File:
`snippets/free/filter-icon-markup.php` adds a class, and serves a collection
`acme/<slug>` from a theme folder without Pro.

Trap: the Icon block does not use this filter. It draws what was registered,
see the next topic.

### Choose what the Icon block offers

`easy_svg_registrable_icons( $icons )` gets the free library on its way to
WordPress's icon registry (7.1+), as a list of `slug`, `label`, `content`.
File: `snippets/free/filter-registrable-icons.php` hides every icon whose
slug starts with `wip-`.

Trap: return a list. Anything else registers no icon at all.

### Allow more in uploaded SVGs

`esw_svg_allowed_tags`, `esw_svg_allowed_attributes` and
`esw_svg_remove_remote_references` widen the sanitizer. File:
`snippets/free/sanitizer-allow-list.php`.

Trap: the list allows names, not what they do. Never add `script`,
`foreignObject` or the animation elements. `style` is allowed by default.

### Limit file size and complexity

`easy_svg_max_bytes` (default 2 MB, or the server's upload limit when lower),
`easy_svg_max_php_tags`, `easy_svg_max_use_tags` and, on multisite,
`easy_svg_respect_network_filetypes`. File: `snippets/free/upload-limits.php`.

Trap: there is no screen for the size limit. The filter is the setting.

### Change a stored icon

`easy_svg_update_icon_markup( $id, $markup )` answers `updated`,
`not_found`, `no_markup`, `refused` or `failed`.
`easy_svg_rename_icon( $id, $label )` answers `renamed`, `not_found`,
`no_label` or `failed`. File: `snippets/free/write-stored-icons.php`.

Trap: `wp_update_post()` runs the icon through kses and can empty it. Use
these two functions instead.

### Clean SVG in your own plugin

`easy_svg_sanitizer()` returns the site's configured sanitizer, or `null`.
Check `EASY_SVG_API` (1 or higher) first.
File: `snippets/free/addon-sanitizer.php`.

Trap: do not build a sanitizer of your own. It would miss what the site
allowed or refused through the filters above.

### Add a tab to the admin screens

`easy_svg_panel_enqueue` loads your script after the panel's,
`easy_svg_panel_menu_tabs` adds a tab with its own capability, and
`easy_svg_pro_url` changes the Pro link in the plugin row. File:
`snippets/free/panel-extension.php`.

Trap: check `EASY_SVG_PANEL_API` before you hook in. A filter cannot be
probed for.

## Easy SVG Pro

Every Pro feature has a switch under **Easy SVG → Settings → Features**.
**AI agent abilities (MCP)**, **Templates** and **Share links** start on, the
rest start off. They need an active license and the free icon library switched
on.

### Collections from a folder or from code

Pro reads `wp-content/easy-svg-icons/<folder>/` and the theme's
`assets/icons/` by itself. `easy_svg_register_icon_collection( $slug,
$label, $folder_or_icons )` adds one from code. Files:
`snippets/pro/register-collection.php`, `snippets/pro/source-folders.php`,
`templates/theme/`.

```php
add_action( 'init', static function () {
    if ( function_exists( 'easy_svg_register_icon_collection' ) ) {
        easy_svg_register_icon_collection( 'acme', 'ACME Icons', __DIR__ . '/icons' );
    }
} );
```

Result: `easy_svg_icon( 'acme/<file name>' )` works, and the Icon block
offers the icons.

Trap: needs **Collections** on, and a new file shows up within a minute
because Pro caches each folder.

### WP-CLI

`wp easy-svg icons`, `pack`, `share`, `fa` and `config`. Switch: **WP-CLI**.
Every command with output: `snippets/pro/wp-cli.md`.

```sh
wp easy-svg icons validate ./icons --strict
```

Result: one row per file, `ok` or `skipped` with the reason; exit code 1 on
any skip.

### Abilities

Up to 41 abilities under `easy-svg-pro/`, for PHP, REST and AI clients.
Files: `snippets/pro/abilities.php`, `snippets/pro/abilities.md`.

```sh
wp eval 'print_r( wp_get_ability( "easy-svg-pro/list-icons" )->execute( array( "search" => "heart" ) ) );' --user=admin
```

Trap: run it with `--user` set to an administrator. Without a user every
ability refuses.

### More Pro hooks

| File | What |
|---|---|
| `registered-icon.php` | `easy_svg_pro_registered_icon`: the markup a Pro icon gets in the Icon block |
| `icon-set-events.php` | `easy_svg_pro_set_icons_writing`, `easy_svg_pro_set_icons_written`, `easy_svg_pro_set_write_context`, `easy_svg_pro_set_screen_redirect` |
| `import-and-configurator.php` | `easy_svg_pro_import_max_entries`, `easy_svg_pro_import_max_total_bytes`, `easy_svg_pro_import_font_max_bytes`, `easy_svg_pro_dangerous_tags`, `easy_svg_pro_dangerous_attrs` |

## Templates

**Icon pack plugin.** Rename `acme-icons.php`, the folder and the text domain.
Each folder under `icons/` is one collection, named by its `manifest.json`.
This is the layout Pro writes when it exports collections as a plugin.
Check and zip it in one step; one problem in any folder and no ZIP is written:

```sh
bash tools/build-pack.sh templates/icon-pack-plugin dist
```

The manifest fields and the license rules for other people's icons:
[docs/icon-packs.md](docs/icon-packs.md).

**Many sites.** The pack has a `composer.json` and installs from its own Git
repository by tag. Pro comes from `composer.unleash-wp.com`, Free from
wpackagist. Thirty client sites, the license from `wp-config.php` and one
configuration file for every switch: [docs/rollout.md](docs/rollout.md).

**Builders and fields.** Each integration has its own switch under
**Easy SVG → Settings → Features** and stores the same thing: the icon
address. Render it with `easy_svg_icon()`, never with Pro's internal
functions, so a page keeps working when Pro is deactivated.

| Host | Switch | You write |
|---|---|---|
| ACF, Secure Custom Fields | ACF icon picker | an `icon_picker` field with the tab `easy_svg` |
| Meta Box | Meta Box icon field | a field of type `easy_svg_icon` |
| Elementor | Elementor icon picker | any `Controls_Manager::ICONS` control |
| WPBakery | WPBakery icon element | a param of type `easy_svg_icon` |
| Block editor (WordPress 7.0+) | PHP-only block icon field | an attribute with `'unleashwp' => array( 'component' => 'icon' )` |

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

## License

GPL-2.0-or-later, see `LICENSE`. Copy freely into client work. Icons you add
keep their own license.
