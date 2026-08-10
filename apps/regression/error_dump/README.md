# Regression: the 'dump' error action

## Introduction

Regression test for `$padErrorAction = 'dump'`. The boom page raises an engine-level PHP
warning - an undefined variable - and the index fetches it and asserts what this action
promises to do with an error. The crawl compares the index, so an action that stops
behaving turns the page from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the boom page and states the verdict |
| `boom.php/pad` | A page whose .php reads an undefined variable |
| `_config/config.php` | Chooses the 'dump' action, `_common` off |
