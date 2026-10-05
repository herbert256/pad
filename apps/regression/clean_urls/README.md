# Regression: clean URLs

## Introduction

Regression test for `$padCleanUrls = TRUE` and the bracketed routes of `pad/lib/route.php`. The
index shows the links `$padGo` writes in the clean form, follows a path to `products/[id]` -
in the `index.php/products/42` form, which every server runs without being told - and reads
where `padRedirect()` sends a routed page back to itself. The crawl compares the index, and
finds `products/[id]` not found by its own name.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | The links, the followed route and the redirect |
| `products/[id].php/pad` | The routed page; with `?back` it redirects to itself |
| `_config/config.php` | Switches `$padCleanUrls` on, `_common` off |
