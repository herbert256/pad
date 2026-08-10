# Regression: the 'boot' error action

## Introduction

Regression test for `$padErrorAction = 'boot'` - the machine-readable action the harness
itself uses for local tooling. The boom page raises an engine-level PHP warning - an
undefined variable - and the index fetches it and asserts the promise: a 500 whose body is
the JSON engine dump, message, file and line included. The crawl compares the index, so an
action that stops behaving turns the page from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the boom page and states the verdict |
| `boom.php/pad` | A page whose .php reads an undefined variable |
| `_config/config.php` | Chooses the 'boot' action, `_common` off |
