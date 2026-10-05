# Regression: remote data

## Introduction

Regression test for remote data with a cache and for parallel fetching. Every page here
fetches pages of this same application, so they run in the Regression suite, which fetches
one page at a time: with the pages suite's dozen at once, two pages waiting on fetches of
their own could leave the local server no worker to answer them.

## Files

| File | Description |
|------|-------------|
| `stamp.php` | A JSON document new on every fetch |
| `slow.php` | A JSON source that takes 200 ms and says when it started and ended |
| `curl_ttl.pad` | `{curl ..., ttl=60}` twice answers alike |
| `curl_data_ttl.php/pad` | A `_data/*.curl` file with a `<ttl>`, and `data=` with the ttl option |
| `curl_data_self.pad` | `data='SELF://...'` is fetched from this server, as `{curl}` has it |
| `prefetch.php/pad` | `padPrefetch`: two slow sources served together, each read as named data |
| `prefetch_ttl.php/pad` | `padPrefetch` with a ttl: the second prefetch is answered from the copy |
| `_data/stampKept.curl` | A `<curl>` document with a url and a ttl |
| `_config/config.php` | `_common` off |
