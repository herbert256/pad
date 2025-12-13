# PAD - PHP Application Driver

PAD is an Inversion of Control PHP template engine where templates drive the application flow, not the other way around. Instead of PHP code including templates, PAD templates orchestrate everything from data retrieval to output generation.

## Hello World

**1. Create the entry point** (`www/index.php`):
```php
<?php
define('APP', '/path/to/apps/myapp/');
define('DAT', '/path/to/DATA/myapp/');
include '/path/to/pad/pad.php';
?>
```

**2. Create your application** (`apps/myapp/`):

`hello.php` - provides data:
```php
<?php
return ['name' => 'World'];
?>
```

`hello.pad` - the template:
```html
<!DOCTYPE html>
<html>
<head><title>Hello</title></head>
<body>
  <h1>Hello, {$name}!</h1>
</body>
</html>
```

**3. Visit** `/hello` in your browser.

PAD automatically pairs `.php` and `.pad` files. The PHP file returns data, the PAD template renders it.

## Directory Structure

```
pad/
├── pad/      # PAD framework core
├── apps/     # PAD applications
├── www/      # Web server entry points
└── DATA/     # Runtime data (logs, cache, dumps)
```

### pad/

The PAD framework itself - the template engine, tag processors, expression evaluator, and all supporting modules.

See [pad/README.md](pad/README.md) for complete framework documentation.

### apps/

PAD applications. Each subdirectory is a self-contained application with its own templates, data files, and libraries.

See [apps/README.md](apps/README.md) for application development guidelines.

### www/

Web server document root files. Contains PHP entry points that configure `APP` and `DAT` constants and include the PAD framework.

See [www/README.md](www/README.md) for web server setup.

### DATA/

Runtime data directory for generated files:
- Cache files
- Log files
- Debug dumps
- Session data
- Temporary files

This directory should be writable by the web server and excluded from version control.
