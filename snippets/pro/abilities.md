# Easy SVG Pro abilities

Sixty abilities under the category `easy-svg-pro`, for the WordPress 6.9+
Abilities API and every MCP client that speaks it. All are `mcp.public`.

- Switch **AI agent abilities** under Icons, Features. It is on by default.
- Every call needs `manage_options` and the right of the screen behind it.
- An ability whose feature switch is off is not registered at all, so an agent
  is never offered something that only answers 403.
- PHP: `wp_get_ability( 'easy-svg-pro/list-icons' )->execute( $input )`, see
  `abilities.php`.
- REST: `/wp-json/wp-abilities/v1/abilities/easy-svg-pro/<name>/run`. Reading
  abilities run with GET, destructive idempotent ones with DELETE, the rest
  with POST.

No ability for: the licence, a person's quick insert state, client mode,
and deleting free library icons. Those belong to the person at the screen.

## Icons and sets

| Ability | Feature | Kind |
|---|---|---|
| `list-icons` | | reads |
| `add-icon` | | writes |
| `add-icons-to-set` | | writes |
| `remove-icon-from-set` | | destructive |
| `list-collections` | | reads |
| `icon-usage` | editor | reads |
| `move-icons` | editor | destructive |
| `media-markup` | editor | reads |
| `list-sets` | editor | reads |
| `get-set` | editor | reads |
| `create-set` | editor | writes |
| `rename-set` | editor | writes |
| `relabel-set-icon` | editor | writes |
| `save-set` | editor | destructive |
| `replace-icons-in-set` | editor | destructive |
| `delete-sets` | editor | destructive |
| `restore-sets` | editor | writes |

## Settings and features

| Ability | Feature | Kind |
|---|---|---|
| `get-settings` | | reads |
| `update-settings` | | writes |
| `get-features` | | reads |
| `update-features` | | writes |
| `apply-quick-setup` | | writes |

## Packs and presets

| Ability | Feature | Kind |
|---|---|---|
| `list-pack-collections` | | reads |
| `export-pack` | | reads |
| `export-icons-pack` | | reads |
| `export-pack-bundle` | | reads |
| `export-icon-plugin` | | reads |
| `import-pack` | editor | writes |
| `list-presets` | presets | reads |
| `install-preset` | presets | writes |
| `update-preset` | presets | destructive |

## Media cleaner, repair, history

| Ability | Feature | Kind |
|---|---|---|
| `get-media-audit` | audit | reads |
| `scan-media` | audit | writes |
| `clean-media-svg` | audit | destructive |
| `get-sources-audit` | audit | reads |
| `scan-sources` | audit | writes |
| `clean-stored-icon` | audit | destructive |
| `list-originals` | audit | reads |
| `get-original` | audit | reads |
| `discard-original` | audit | destructive |
| `get-repair-report` | repair | reads |
| `scan-repair` | repair | writes |
| `repair-svg` | repair | destructive |
| `get-set-history` | history | reads |
| `get-library-history` | history | reads |
| `restore-set-version` | history | destructive |
| `restore-library-version` | history | destructive |

## Bulk, replace, styles

| Ability | Feature | Kind |
|---|---|---|
| `preview-bulk-rename` | bulk | reads |
| `bulk-rename` | bulk | destructive |
| `bulk-copy` | bulk | writes |
| `bulk-move` | bulk | destructive |
| `preview-replace` | replace | reads |
| `replace-icon-everywhere` | replace | destructive |
| `get-styles` | styles | reads |
| `save-style-templates` | styles | destructive |
| `assign-style` | styles | writes |
| `get-icon-colours` | styles | reads |
| `set-icon-colours` | styles | writes |

## Documentation

| Ability | Feature | Kind |
|---|---|---|
| `get-documentation` | | reads |
| `search-documentation` | | reads, asks support.unleash-wp.com |
