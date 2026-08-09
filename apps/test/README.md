# Test Application

## Introduction

A scratch application for trying things out. `_common` is switched off in its
configuration, so everything a page here uses has to come from this application itself.

## Files

| File | Description |
|------|-------------|
| `index.php` | Home page data |
| `index.pad` | Home page template |
| `abc.html` | An html file as a page - `?abc` renders it, PAD tags would resolve in it |
| `_config/config.php` | Sets `$padCommon = FALSE` |
