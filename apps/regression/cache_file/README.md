# Regression: the file page cache

## Introduction

Regression test for the 'file' server-side page cache. The application caches its pages for
60 seconds; the probe page embeds its build moment in nanoseconds, and the index fetches it
twice - two identical bodies prove the second fetch was answered from the cache. The crawl
compares the index, so a backend that stops caching turns the page from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the probe twice and states the verdict |
| `json.php/pad` | A page that declares itself JSON |
| `probe.php/pad` | A page whose body differs on every build |
| `typed.php/pad` | Fetches the JSON page twice: a page that chose its own content type is never answered from the cache, which would send the configured type |
| `identity.php/pad` | A request with a cookie of its own is built fresh, never answered with the anonymous visitor's cached page |
| `guarded.php/pad` | A page under a `_guard.php` is built fresh for the request the guard lets in, and refused to one it does not - never answered from the cache |
| `locked/` | The guarded directory: its `_guard.php` wants the header `X-Key: open`, its probe differs on every build |
| `nonce.php/pad` | A page holding the request's CSP nonce (`script.pad`) is built fresh for each fetch, never stored |
| `meta.php/pad` | Fetches `metaoff` twice: a page with `{meta cache=0}` is built every time though the application caches |
| `metaoff.php/pad` | A page that keeps itself out of the cache with `{meta cache=0}`, its body its build moment |
| `validators.php/pad` | The cache's validators belong to one URL: the probe's ETag gets a 304 from the probe only, and a date older than the cached copy gets the page |
| `etaglist.php/pad` | On a cache miss the 304 reads the whole If-None-Match list: its tag second in a list, and `*`, get a 304, another tag the page |
| `stable.pad` | A page that never changes, fetched by etaglist at a fresh address each time |
| `_config/config.php` | Switches the file cache on, 60 seconds, `_common` off |
