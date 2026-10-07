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
| `disposition.php` | A download named `report.file` whose body is the name of a data file here |
| `curl_data_disposition.pad` | That download is read as the text it is - `_data/localOnly.json` is never read |
| `debugged.php/pad` | A page with a `{debug}` box, which only a local request sees |
| `curl_ttl_stores_no_debug.php/pad` | A ttl fetch of it for a local request shows the box but keeps no copy |
| `prefetch.php/pad` | `padPrefetch`: two slow sources served together, each read as named data |
| `prefetch_ttl.php/pad` | `padPrefetch` with a ttl: the second prefetch is answered from the copy |
| `_data/stampKept.curl` | A `<curl>` document with a url and a ttl |
| `_data/localOnly.json` | The data file the download names, which no fetch may read |
| `_config/config.php` | `_common` off |
