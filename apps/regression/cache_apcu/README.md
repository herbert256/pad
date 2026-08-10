# Regression: the apcu page cache

## Introduction

Regression test for the 'apcu' server-side page cache - the local shared-memory backend.
The application caches its pages for 60 seconds; the probe page embeds its build moment in
nanoseconds, and the index fetches it twice - two identical bodies prove the second fetch
was answered from the cache. The crawl compares the index, so a backend that stops caching
turns the page from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the probe twice and states the verdict |
| `probe.php/pad` | A page whose body differs on every build |
| `_config/config.php` | Switches the apcu cache on, 60 seconds, `_common` off |
