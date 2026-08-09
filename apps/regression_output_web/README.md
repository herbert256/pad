# Regression: the 'web' output type

## Introduction

Regression test for `$padOutputType = 'web'`. The payload page renders a marker through a
pipe, and the index fetches it and asserts how this output type is supposed to deliver a
page. The crawl compares the index, so a writer that stops behaving turns the page from
yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the payload and states the verdict |
| `payload.php/pad` | A page with a recognisable body |
| `_config/config.php` | Chooses the 'web' output type, `_common` off |
