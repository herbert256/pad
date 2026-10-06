# Non-PAD Application

## Introduction

A plain PHP application that runs without the PAD template engine.

## Purpose

Demonstrates that the PAD framework can host traditional PHP applications that don't use PAD templating. Useful for:
- Legacy PHP code integration
- Simple API endpoints
- PHP scripts that don't need templating

## Files

| File | Description |
|------|-------------|
| `_config/config.php` | Sets `$padNoNo = TRUE` - what makes the application plain PHP |
| `index.php` | Plain PHP output without templates |

## Code

**index.php**:
```php
<?php
  echo "plain php without (much) PAD stuff.";
?>
```

## Concept

While PAD typically pairs `.php` data files with `.pad` templates, this app shows that you can run plain PHP: with `$padNoNo = TRUE` in `_config/config.php` the page's `.php` runs on its own, with no template, level loop or wrapper. A `.php` without a `.pad` in an ordinary application still goes through the engine.

## Access

Via web browser: `http://server/nono/`
