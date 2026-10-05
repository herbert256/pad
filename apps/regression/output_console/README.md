# Regression: the 'console' output type

## Introduction

Regression test for `$padOutputType = 'console'`. The payload page renders a marker through a
pipe, and the index fetches it and asserts how this output type is supposed to deliver a
page. The crawl compares the index, so a writer that stops behaving turns the page from
yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the payload and states the verdict |
| `payload.php/pad` | A page with a recognisable body |
| `broken.pad` | A page that fails with a long message |
| `error.php/pad` | Fetches the broken page as a browser would: the console error report carries the message whole |
| `_config/config.php` | Chooses the 'console' output type, `_common` off |
