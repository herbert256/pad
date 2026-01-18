# Hello World Application

## Introduction

The simplest possible PAD application demonstrating the basic page pairing concept.

## Files

| File | Description |
|------|-------------|
| `index.php` | Data file - sets `$message` variable |
| `index.pad` | Template file - renders the message |

## Code

**index.php**:
```php
<?php
  $message = 'Hello World!';
?>
```

**index.pad**:
```html
<html>
<head>
  <title>Hello</title>
</head>
<body>
  <h1>{$message}</h1>
</body>
</html>
```

## Concept

This demonstrates PAD's core principle: **page pairing**. The `.php` file provides data, and the `.pad` template renders it. PAD automatically pairs these files based on their name.

## Access

Via web browser: `http://server/hello/`
