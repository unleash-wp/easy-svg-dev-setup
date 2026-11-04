# Easy SVG Pro abilities

An ability is a named action that WordPress (6.9 and newer) offers to code
and to AI clients through its Abilities API. Easy SVG Pro registers up to 41
of them under the category `easy-svg-pro`. All are marked `mcp.public`, so an
MCP server for WordPress offers them to an AI client.

## Try it

```sh
# Every icon whose name contains "heart", from every source
wp eval 'print_r( wp_get_ability( "easy-svg-pro/list-icons" )->execute( array( "search" => "heart" ) ) );' --user=admin
```

The answer has `items` (each with `slug`, `label`, `collection`, `source`),
`total`, `page`, `per_page`, `has_more` and `truncated`.

Over HTTP, with an application password of an administrator:

```sh
curl -u "admin:APPLICATION PASSWORD" \
  "https://example.com/wp-json/wp-abilities/v1/abilities/easy-svg-pro/list-icons/run?input%5Bsearch%5D=heart"
```

## Rules

- The switch is **AI agent abilities (MCP)** under
  **Easy SVG > Settings > Features**. It is on by default.
- Every call needs `manage_options` and the right of the screen behind it.
  Without them the answer is 401 or 403.
- An ability whose switch is off is not registered at all, so a client is
  never offered something that can only fail.
- The HTTP method follows the ability: GET for reading, DELETE for a
  destructive one that is safe to repeat, POST for the rest. GET and DELETE
  take `input` in the query string, POST takes `{"input": {...}}` as JSON.
  The wrong method answers 405.
- In PHP: `wp_get_ability( 'easy-svg-pro/<name>' )->execute( $input )`. See
  `abilities.php`. `wp_get_ability()` gives `null` when the ability is not
  registered.

There is no ability for the license, for a person's quick insert state or
for deleting icons of the free library. Those stay with the person at the
screen.

## Always there

Registered as soon as the abilities switch is on.

| Ability | Method |
|---|---|
| `list-icons` | GET |
| `add-icon` | POST |
| `add-icons-to-set` | POST |
| `remove-icon-from-set` | DELETE |
| `list-collections` | GET |
| `copy-icons` | POST |
| `get-settings` | GET |
| `update-settings` | POST |
| `get-features` | GET |
| `update-features` | POST |
| `apply-quick-setup` | POST |
| `list-pack-collections` | GET |
| `export-pack` | GET |
| `export-icons-pack` | GET |
| `export-pack-bundle` | GET |
| `export-icon-plugin` | GET |
| `preview-bulk-rename` | GET |
| `bulk-rename` | POST |
| `bulk-copy` | POST |
| `bulk-move` | POST |
| `get-documentation` | GET |
| `search-documentation` | GET, asks support.unleash-wp.com |

## With the Collections switch

| Ability | Method |
|---|---|
| `list-sets` | GET |
| `get-set` | GET |
| `create-set` | POST |
| `rename-set` | POST |
| `relabel-set-icon` | POST |
| `save-set` | DELETE |
| `delete-sets` | DELETE |
| `restore-sets` | POST |
| `move-icons` | POST |
| `icon-usage` | GET |
| `media-markup` | GET |
| `import-pack` | POST |

## With the Templates switch

Templates are ready-made sets from UnleashWP.

| Ability | Method |
|---|---|
| `list-presets` | GET |
| `install-preset` | POST |
| `update-preset` | DELETE |

## With the Share links switch

| Ability | Method |
|---|---|
| `list-share-links` | GET |
| `create-share-link` | POST |
| `revoke-share-link` | DELETE |
| `import-pack-from-link` | POST |

## Things that trip people up

- **`save-set` replaces the whole set.** It is destructive on purpose: icons
  you leave out of the list are gone from the set. To add one icon, use
  `add-icons-to-set`.
- **A GET sends every value as a string.** `per_page=5` arrives as `"5"`;
  the abilities accept that. Lists go as `input[refs][]=a&input[refs][]=b`.
