# Nono Application

A plain PHP application that does **not** use the PAD templating framework.

## Purpose

This application demonstrates that the PAD directory structure can also host traditional PHP applications without using PAD's template engine.

## Files

| File | Description |
|------|-------------|
| `index.php` | Plain PHP file with direct echo output |
| `_config/config.php` | Configuration file |

## Code

**index.php**:
```php
<?php
  echo "plain php without (much) PAD stuff.";
?>
```

## When to Use

Use this pattern when:
- You need a simple PHP script without templating
- You want to integrate non-PAD code into a PAD installation
- You're testing PHP functionality outside of PAD

## Access

Via web browser: `http://server/nono/`
