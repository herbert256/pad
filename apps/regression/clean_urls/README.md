# Regression: clean URLs

## Introduction

Regression test for `$padCleanUrls = TRUE` and the bracketed routes of `pad/lib/route.php`. The
index shows the links `$padGo` writes in the clean form, follows a path to `products/[id]` -
in the `index.php/products/42` form, which every server runs without being told - and reads
where `padRedirect()` sends a routed page back to itself, and fetches `members/eve` - a route
named like the session variable `member`, which the path must not set. The crawl compares the index, and
finds `products/[id]` not found by its own name.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | The links, the followed route and the redirect |
| `products/[id].php/pad` | The routed page; with `?back` it redirects to itself |
| `members/[member].php/pad` | A route named like a session variable - shows the session's value |
| `_config/config.php` | Switches `$padCleanUrls` on, declares the session variable `member`, `_common` off |
