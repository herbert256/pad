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
| `_config/` | Configuration (minimal) |
| `index.php` | Plain PHP output without templates |

## Code

**index.php**:
```php
<?php
  echo "plain php without (much) PAD stuff.";
?>
```

## Concept

While PAD typically pairs `.php` data files with `.pad` templates, this app shows that you can run plain PHP when no `.pad` template exists.

## Access

Via web browser: `http://server/nono/`
