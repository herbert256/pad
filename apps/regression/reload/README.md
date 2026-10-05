# Regression: live reload

## Introduction

Regression test for `$padReload` (pad/lib/reload.php). The application's config switches
live reload on for the sample page alone; the verdict fetches it and asserts that a local
whole page carries the script with the stamp the `&padReload` poll answers, that a file
saved moves that stamp, that a bare fragment and a forwarded request get no script - the
forwarded poll is an ordinary page - and that a `pad export` of the page carries none. The
crawl compares the verdict, so live reload that stops behaving turns a yes into a NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the sample page and its poll, touches the sample, states the verdicts |
| `verdict.php/pad` | The same check run on every load, so the crawl holds live reload to it |
| `sample.pad` | The page live reload is switched on for |
| `_config/config.php` | `_common` off, live reload for the sample page only |
