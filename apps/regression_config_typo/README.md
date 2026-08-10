# Regression: the configuration word check

## Introduction

Regression test for the configuration validation: this application's config chooses an
output type that does not exist, and every request answers with the word named -
"there is no output type named 'nosuchtype'" - instead of dying on a raw missing
include. The index declares that answer, so the crawl counts it as expected.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | A page that never renders - the config check answers first |
| `index.txt` | Declares the expected HTTP 500, for the crawl |
| `_config/config.php` | Chooses the output type 'nosuchtype', `_common` off |
