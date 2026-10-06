# Regression: the sitemap

## Introduction

Regression test for `$padSitemap`: the application answers `?sitemap.xml` from its own file
tree and `?robots.txt` pointing to it. The index fetches both and shows them, the dates as
their shape. The tree holds one of every kind the sitemap leaves out - a name in
`$padSitemapSkip`, a directory with a `_guard.php`, an action page (one that redirects, one
that goes back with `padBack`), a bracketed route - beside the pages it lists.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the sitemap and robots.txt and shows them |
| `about.pad`, `docs/index.pad`, `docs/intro.html` | Pages the sitemap lists |
| `thanks.pad` | Left out by `$padSitemapSkip` |
| `admin/_guard.php`, `admin/panel.pad` | A guarded directory, left out |
| `send.php` | An action that only redirects, left out |
| `back.php` | An action that only goes back (`padBack`), left out |
| `products/[id].pad` | A route with no one address, left out |
| `[slug].pad` | A route at the root, which binds any name but leaves `?sitemap.xml` and `?robots.txt` to the engine |
| `_config/config.php` | Switches `$padSitemap` on, `_common` off |
