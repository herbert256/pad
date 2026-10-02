# Regression: the configuration word check

## Introduction

Regression test for the configuration validation: this application's config chooses an
output type that does not exist, and every request answers with the word named -
"there is no output type named 'nosuchtype'" - instead of dying on a raw missing
include. The Regression suite's store declares that answer, so the suite counts it as
expected.

## Files

| File | Description |
|------|-------------|
| `index.pad` | A page that never renders - the config check answers first |
| `../regression/config_typo/index.txt` | Declares the expected HTTP 500, for the Regression suite |
| `_config/config.php` | Chooses the output type 'nosuchtype', `_common` off |
