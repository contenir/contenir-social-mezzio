# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased] - 2026-10-23

### Added

- `SiteProfiles`, a service holding the site's profiles from its settings: `render()` writes them as escaped links
  with `rel="me"`, icons and accessible names; `sameAs()` lists their addresses for JSON-LD; `urls()` gives them by
  network; `profiles()` gives contenir-social's `Profiles`.
- `SiteShare`, a service writing a page's share buttons from the site's share settings, with the script for
  "Copy link" and "Share…" and the CSP nonce it is given. The page is given with each call, as contenir-resource's
  `PageMetadata`, an array, any object with `url`, `title`, `description` and `image`, or a `Page`; addresses
  starting with `/` are made absolute on the site's `site.url`.
- `socialProfiles()` and `share()` laminas-view helpers around the services, as plain invokable classes.
- `ConfigProvider`, registering the services under `dependencies` and the helpers under `view_helpers`, and listed
  in `extra.laminas.config-provider`.
- Settings read as a Mezzio site holds them: the top-level config, with `config['settings']` laid over it.
- Default options for both services under `contenir_social.options`.
