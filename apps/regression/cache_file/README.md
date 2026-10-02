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
| `validators.php/pad` | The cache's validators belong to one URL: the probe's ETag gets a 304 from the probe only, and a date older than the cached copy gets the page |
| `_config/config.php` | Switches the file cache on, 60 seconds, `_common` off |
