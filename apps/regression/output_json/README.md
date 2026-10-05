# Regression: the 'json' output type

## Introduction

Regression test for `$padOutputType = 'json'`, the data answer: every page of this application
answers the variables its `.php` names in `$padExpose`, as one JSON object, and renders no
template. The payload page exposes two variables and keeps a third; its template would fail if
it ran. The index fetches the payload as JSON, and again with `&padFormat=csv` as a table, and
exposes its verdicts. The crawl compares both pages, so a writer that stops behaving turns a
verdict from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php` | Fetches the payload in both formats and exposes the verdicts |
| `payload.php/pad` | A page that exposes two variables; its template fails if it runs |
| `_config/config.php` | Chooses the 'json' output type, `_common` off |
