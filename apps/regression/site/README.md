# Regression: a site to export

## Introduction

The fixture `pad export` is tested on (regression/cli): three pages - one in a
subdirectory - that link to each other as `?page`, as `{$pad}page` and with an anchor, a
stylesheet and an image beside the entry point in `www/regression/site/`, and a link that
leaves the site. The export has to turn the page links into relative `.html` files, keep the
asset links working from the subdirectory, copy the assets and leave the outside link alone.

## Files

| File | Description |
|------|-------------|
| `index.pad` | Links in every form, and the stylesheet |
| `about.pad` | A link to the site's root |
| `docs/intro.pad` | A page one directory down: a link home and the image |
| `_config/config.php` | `_common` off |
