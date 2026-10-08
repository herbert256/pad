# Regression: maintenance mode

## Introduction

Regression test for maintenance mode (pad/lib/maintenance.php, `pad down` and `pad up`).
The index takes its own application down with a secret and fetches it: a page answers 503
with a Retry-After, through `_errors/503.pad`; without error pages (`?plain`) the message
is one plain line; a page that is not there is 503 too; `?<secret>` redirects to the front
page with the bypass cookie, a wrong one is 503, and the cookie lets the browser through.
Brought up again, the page answers as before.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Takes the application down, fetches it every way, brings it up |
| `about.pad` | The page fetched |
| `_errors/503.pad` | The application's page for maintenance |
| `_config/config.php` | `_common` off; `?plain` switches the error pages off |
