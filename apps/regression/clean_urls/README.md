# Regression: clean URLs

## Introduction

Regression test for `$padCleanUrls = TRUE` and the bracketed routes of `pad/lib/route.php`. The
index shows the links `$padGo` writes in the clean form, follows a path to `products/[id]` -
in the `index.php/products/42` form, which every server runs without being told - and reads
where `padRedirect()` sends a routed page back to itself, and fetches `members/eve` - a route
named like the session variable `member`, which the path must not set. The crawl compares the index, and
finds `products/[id]` not found by its own name. `helpers` shows the links `padUrl` writes in the clean form,
and puts the session variable `member` into the session with `padSessionPut` and takes it out with
`padSessionForget` - the next request finds what they did: the helpers keep the variable in step, or the end
of the request would write its old value back over the session.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | The links, the followed route and the redirect |
| `products/[id].php/pad` | The routed page; with `?back` it redirects to itself |
| `members/[member].php/pad` | A route named like a session variable - shows the session's value |
| `helpers.php/pad` | `padUrl` in the clean form, and the session helpers on a session variable |
| `helpers_put.php/pad`, `helpers_forget.php/pad` | Its fixtures: `member` put into the session, and taken out |
| `[slug].pad` | A route at the root, which binds any name the application has no page for |
| `rootroute.php/pad` | A bare query key on a clean URL stays a value of the path's page beside the root route |
| `_config/config.php` | Switches `$padCleanUrls` on, declares the session variable `member`, `_common` off |
