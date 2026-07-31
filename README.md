# globalrandom — GLOBAL RANDOM for Nextcloud

A Nextcloud app that embeds **[GLOBAL RANDOM](https://github.com/gh0stless/global-random)** — a self-contained HTML radio that plays music from 247 countries via real, fairness-weighted randomness (MusicBrainz + Spotify), with live translation into up to 87 languages — inside a private, password-protected Nextcloud instance.

Live reference (original, non-Nextcloud deployment): **[crazy-midi.de/global-random](https://crazy-midi.de/global-random/)**

## What this repo is

A feasibility-study wrapper, not the app itself: `global-random.html` at the repo root is a synced, unmodified copy of the canonical [global-random](https://github.com/gh0stless/global-random) source, embedded via iframe so its own global CSS/fonts don't collide with Nextcloud's. All actual feature work happens in the canonical repo and is manually synced here (see commit history — "Sync: ..." commits). Closed-circle use case (a private Nextcloud instance), not a public deployment.

## Structure

| Path | Purpose |
|---|---|
| `appinfo/info.xml` | Nextcloud app manifest |
| `appinfo/routes.php` | Routes: framed page + raw HTML passthrough |
| `lib/AppInfo/Application.php` | Bootstrap class |
| `lib/Controller/PageController.php` | Serves the framed page + the raw `global-random.html`, with a scoped CSP for the required external domains (Spotify, MusicBrainz, Wikipedia, MyMemory, Open-Meteo) |
| `templates/index.php` | Iframe shell |
| `css/style.css` | Full-bleed iframe layout inside Nextcloud's normal header/nav |
| `global-random.html` | Synced copy of the canonical app |

## Install

Standard Nextcloud app sideload: place this folder under `apps/` (or `custom_apps/`), then `occ app:enable globalrandom`. Requires Nextcloud 28–34.

## License

This wrapper app (Nextcloud integration code) is licensed under **AGPL-3.0-or-later**.

The embedded artwork (`global-random.html`) is licensed separately under **[CC BY-NC-ND 4.0](https://creativecommons.org/licenses/by-nc-nd/4.0/)** — no derivatives/modifications permitted. See the [canonical repo](https://github.com/gh0stless/global-random) for details.

© 2026 Andreas S.
