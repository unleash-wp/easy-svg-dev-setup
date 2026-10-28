# Rolling out to many sites

One icon pack, Easy SVG and Easy SVG Pro on thirty client sites, from Git,
with Composer and WP-CLI. Nothing here needs a person in wp-admin except one
login per site to activate the licence.

The official guides this builds on:
[Install with Composer](https://support.unleash-wp.com/en/help/account/composer),
[Composer quick start](https://support.unleash-wp.com/en/guides/quick-start/quick-start-composer) and
[Global settings with per-site overrides](https://support.unleash-wp.com/en/guides/quick-start/quick-start-central-config).

## 1. Your pack as a Composer package

Copy `templates/icon-pack-plugin/` into a Git repository of its own and rename
it (see the README). Its `composer.json` makes it a `wordpress-plugin` package:

```json
{
  "name": "acme/acme-icons",
  "type": "wordpress-plugin",
  "license": "GPL-2.0-or-later",
  "require": { "php": ">=7.4", "composer/installers": "^1.0 || ^2.0" }
}
```

Release a version by tagging it. Check every folder first, with the checker
from a checkout of this repository:

```sh
php ../easy-svg-dev-setup/tools/check-pack.php icons/acme-icons
git tag v1.1.0 && git push --tags
```

The tag is the version Composer sees. Raise `Version:` in the plugin header to
the same number, so the Plugins screen agrees.

## 2. One composer.json per site

```json
{
  "repositories": [
    { "type": "composer", "url": "https://wpackagist.org", "only": ["wpackagist-plugin/*", "wpackagist-theme/*"] },
    { "type": "composer", "url": "https://composer.unleash-wp.com", "only": ["unleashwp/*"] },
    { "type": "vcs", "url": "git@github.com:acme/acme-icons.git" }
  ],
  "require": {
    "composer/installers": "^2.0",
    "wpackagist-plugin/easy-svg": "^5.3.1",
    "unleashwp/easy-svg-pro": "^1.0",
    "acme/acme-icons": "^1.0"
  },
  "config": { "allow-plugins": { "composer/installers": true } }
}
```

- `only` keeps each repository to its own packages.
- A private pack repository needs the build server's deploy key, or
  `COMPOSER_AUTH` with a GitHub token (`github-oauth`).
- The licence key is the password for `composer.unleash-wp.com` and never goes
  into Git:

```sh
export COMPOSER_AUTH='{"http-basic":{"composer.unleash-wp.com":{"username":"unleashwp","password":"'"$UNLEASHWP_LICENSE_KEY"'"}}}'
composer install --no-dev --no-interaction --prefer-dist
```

One Composer password per host means one licence per project. Give each
client project its own key.

## 3. The licence on each site

```php
// wp-config.php
define( 'EASY_SVG_PRO_LICENSE_KEY', getenv( 'EASY_SVG_PRO_LICENSE_KEY' ) );
```

Set the variable on the server. The site activates itself the next time an
administrator opens wp-admin, and uses one seat. A deploy script alone cannot
do it: the activation runs only for a signed-in administrator.

## 4. Switch the features on

Either per site with WP-CLI (every key you leave out is off):

```sh
wp option update easy_svg_settings '{"svg_upload":true,"icons":true,"max_mb":2}' --format=json
wp option update easy_svg_pro_features '{"collections":true,"sources":true,"cli":true}' --format=json
```

Or once for every site from a must-use plugin that filters the options, as
the "Global settings" guide shows. With thirty sites, the mu-plugin wins: it
ships with the code and nobody has to remember a command.

Add the integrations your clients use: `icon_field_acf`, `metabox`,
`elementor`, `wpbakery`, `icon_field_blocks`.

## 5. Thirty sites with WP-CLI aliases

```yaml
# wp-cli.yml in your deploy repository
@client-a:
  ssh: deploy@client-a.example:/var/www/html
@client-b:
  ssh: deploy@client-b.example:/var/www/html
@clients:
  - @client-a
  - @client-b
```

```sh
wp @clients plugin activate easy-svg easy-svg-pro acme-icons
wp @clients easy-svg icons list
wp @clients easy-svg icons problems
```

`icons list` shows each collection with its count and origin. `icons problems`
names every file a folder could not use. Both need the `cli` switch.

## 6. Updating the pack later

1. Change the icons, run the checker, tag the new version.
2. In each site project: `composer update acme/acme-icons`, then deploy.
3. `wp @clients easy-svg icons problems` should print that every folder
   registered everything.

Keep icon names stable. A page points at `collection/name`, so a renamed icon
shows nothing where it was used.
