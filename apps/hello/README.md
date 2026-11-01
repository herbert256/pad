# Hello World Application

## Introduction

The simplest possible PAD application demonstrating the basic page pairing concept.

## Files

| File | Description |
|------|-------------|
| `index.php` | Data file - sets `$message` variable |
| `index.pad` | Template file - renders the message |
| `_inits.pad`, `_exits.pad` | The document around every page, with the brand every PAD application shows top left - `{common:padBrand/}` |
| `_config/config.php` | Switches `_common` off: the document is the application's own |

## Code

**index.php**:
```php
<?php
  $message = 'Hello World!';
?>
```

**index.pad**:
```html
<h1>{$message}</h1>
```

## Concept

This demonstrates PAD's core principle: **page pairing**. The `.php` file provides data, and the `.pad` template renders it. PAD automatically pairs these files based on their name.

## Access

Via web browser: `http://server/hello/`
