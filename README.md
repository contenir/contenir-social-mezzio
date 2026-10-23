# contenir/contenir-social-mezzio

[![Continuous Integration](https://github.com/contenir/contenir-social-mezzio/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/contenir/contenir-social-mezzio/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/contenir/contenir-social-mezzio/graph/badge.svg)](https://codecov.io/gh/contenir/contenir-social-mezzio)

The Mezzio adapter for [contenir/contenir-social](https://github.com/contenir/contenir-social):

- `SiteProfiles`, a service holding the site's profiles: their links as escaped HTML with `rel="me"`, icons and
  accessible names, their addresses for JSON-LD `sameAs`, or the profiles themselves;
- `SiteShare`, a service writing a page's share buttons, and the small script behind "Copy link" and "Share…";
- `socialProfiles()` and `share()`, thin laminas-view helpers around them, for layouts rendered by
  mezzio-laminasviewrenderer.

The services return strings and data, so any template engine, or a handler, can use them. Profiles and share
buttons are set in the CMS, by the [Social add-on](https://github.com/contenir-extension/social).

## Requirements

- PHP 8.3, 8.4 or 8.5
- A PSR-11 container. The `ConfigProvider` is written for laminas-servicemanager.
- For the view helpers: laminas-view, as mezzio/mezzio-laminasviewrenderer installs it. The helpers are plain
  invokable classes, so this package does not require it.

## Install

```bash
composer require contenir/contenir-social-mezzio
```

laminas-component-installer offers to add `Contenir\Social\Mezzio\ConfigProvider` to `config/config.php`. Without
it, add the provider to the `ConfigAggregator` yourself. It registers:

| Key | Name | Is |
|---|---|---|
| `dependencies.factories` | `Contenir\Social\Mezzio\SiteProfiles` | the site's profiles |
| `dependencies.factories` | `Contenir\Social\Mezzio\SiteShare` | the site's share buttons |
| `view_helpers` | `socialProfiles`, `SocialProfiles` | `Contenir\Social\Mezzio\View\Helper\SocialProfiles` |
| `view_helpers` | `share`, `Share` | `Contenir\Social\Mezzio\View\Helper\Share` |

## Settings

A Mezzio site's settings are its **top-level config**, where `config/autoload/settings.global.php` is merged, with
**`config['settings']` laid over it** (`array_replace_recursive`), because the CMS writes `settings.local.php` under
a `settings` key. Nothing needs configuring on the site.

| Settings key | Read for |
|---|---|
| `social.social_facebook`, `social.social_instagram`, `social.social_linkedin`, `social.social_twitter` (X), `social.social_youtube`, `social.social_tiktok`, `social.social_pinterest`, `social.social_threads`, `social.social_bluesky`, `social.social_mastodon` | the profiles, as addresses or handles |
| `extensions.social.share_networks` | the share buttons, in order (the site has contenir-social's defaults until it chooses) |
| `extensions.social.share_labels` | whether share buttons show their names beside their icons |
| `extensions.social.share_utm` | whether shared addresses carry UTM tags |
| `site.url` | the address that page addresses starting with `/` are made absolute on |

The keys are contenir-social's `SettingKeys`, apart from `site.url`. The settings are read once, when the services
are built, which suits Mezzio's cached config: a change in the CMS is seen once the config cache is cleared.

## Profiles

```php
<?= $this->socialProfiles() ?>
<?= $this->socialProfiles(['block' => 'footer', 'labels' => true]) ?>
```

```html
<ul class="social social--profiles">
  <li class="social__item social__item--instagram">
    <a class="social__link" href="https://www.instagram.com/yourname/" rel="me noopener" target="_blank"
       aria-label="Instagram (opens in a new tab)"><svg class="social__icon" …></svg></a>
  </li>
  …
</ul>
```

Every form the CMS has stored is read (an `@handle`, a handle on its own, an address), the canonical address is
written and escaped, and anything that isn't a profile on its network is left out.

`socialProfiles()` returns a string, so for data, ask laminas-view for the helper itself:

```php
<?php
$profiles = $this->plugin('socialProfiles');
$profiles->urls();                     // ['instagram' => 'https://www.instagram.com/yourname/', …]
$profiles->sameAs();                   // ['https://www.instagram.com/yourname/', …]
$profiles->profiles()->twitterSite();  // all(), get(Network), sameAs() and twitterSite()
?>
```

Outside a template, take `SiteProfiles` from the container: it has the same `render()`, `urls()`, `sameAs()` and
`profiles()`.

```php
$schema['sameAs'] = $container->get(\Contenir\Social\Mezzio\SiteProfiles::class)->sameAs();
```

## Share buttons

Mezzio services are shared across requests, so `share()` is given the page to share every time it is called:

```php
<?= $this->share($metadata) ?>
<?= $this->share(['url' => $entry->url, 'title' => $entry->title, 'image' => $entry->image]) ?>
```

The page can be:

- contenir-resource's `Contenir\Resource\Core\Metadata\PageMetadata`, which `ResourceMiddleware` attaches to each
  routed page (`ResourceAttribute::metadata($request)`), passed as it is;
- an array with `url`, `title`, `description` and `image`;
- any object with those as public properties;
- a `Contenir\Social\Share\Page`.

Missing or `null` values are empty. The address is required, and Pinterest needs the image. Addresses starting with
`/` are made absolute on the site's `site.url`; without one, they are refused with contenir-social's
`InvalidPageException`, as any address that isn't absolute http or https is.

`share()` writes the buttons the site chose in the CMS, in its order. Nothing reaches a network until a visitor
clicks: the buttons are plain links, with no third-party scripts, counts or cookies.

For markup of a site's own, `$this->plugin('share')->links($page)` gives the `ShareLink`s, and `SiteShare` in the
container has the same `render()`, `links()`, `script()` and `config()`.

### The script and the CSP nonce

When the buttons include "Copy link" or "Share…", `share()` writes the script behind them straight after. The nonce
is per request, so the layout gives it:

```php
<?= $this->share($metadata, ['nonce' => $nonce]) ?>
```

The script runs once however often a page includes it. To write it once on a page with two sets of buttons, or to
place it yourself:

```php
<?= $this->share($metadata, ['script' => false]) ?>
…
<?= $this->plugin('share')->script($nonce) ?>
```

Without the script, "Copy link" and "Share…" stay hidden and every other button still works.

## Options

Both helpers, and the services' `render()`, take an array or a `Contenir\Social\Html\Options`:

| Option | Default | |
|---|---|---|
| `icons` | `true` | draw each network's icon |
| `labels` | profiles: `false`; share: the site's setting | show each name beside its icon (always, without icons) |
| `new_tab` | `true` | open links in a new tab, and say so in each link's accessible name |
| `block` | `social` | the BEM block the classes are named after |
| `class` | | more classes for the list |

`share()` also takes `script` (`false` leaves the script out) and `nonce`.

The classes are `{block}`, `{block}--profiles` or `{block}--share`, `{block}__item`, `{block}__item--{network}`,
`{block}__link`, `{block}__button`, `{block}__icon`, `{block}__label` and `{block}__status` ("Copied"). No CSS is
included. Icons are 24 by 24 and take the text colour.

To change the defaults for the whole site, in the application's config (not its settings):

```php
return [
    'contenir_social' => [
        'options' => ['block' => 'site-social', 'new_tab' => false],
    ],
];
```

## How it differs from contenir-social-laminas-mvc

| | laminas-mvc | Mezzio |
|---|---|---|
| Settings | `config['settings']` | the top-level config, with `config['settings']` laid over it |
| Services | none: the helpers do the work | `SiteProfiles` and `SiteShare`, usable without laminas-view |
| Registration | `Module` and `ConfigProvider` | `ConfigProvider` (`extra.laminas.config-provider`) |
| The page to share | read from `headMeta()`, `headLink()`, `headTitle()` and the request, or given | always given: `PageMetadata`, an array, an object or a `Page` |
| Root-relative addresses | made absolute on the current request's host | made absolute on the site's `site.url` |
| CSP nonce | a `NonceProviderInterface` service, or given | given (`['nonce' => $nonce]`) |
| Script | after the first set of buttons on a page that needs it | after every set that needs it, unless `['script' => false]` |
| Data from the helpers | `->profiles()` | `->profiles()`, `->urls()`, `->sameAs()`, and `share`'s `->links($page)` |

The page, the nonce and the script differ for one reason: a Mezzio container, and the laminas-view helper manager in
it, is shared by every request a process serves, so nothing about the current page can be kept by a service or a
helper between calls.

## Licence

BSD-3-Clause. See [LICENSE.md](LICENSE.md). The icons' licences are in contenir/contenir-social's ICONS.md.
