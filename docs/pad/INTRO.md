# PAD Framework

**PHP Application Driver** - An Inversion of Control Template Engine

## Introduction

PAD is a PHP template engine that inverts the traditional web application architecture. Instead of PHP code including templates, PAD templates drive the execution flow, seamlessly integrating data access, control structures, and presentation in a unified template syntax.

### Philosophy

Traditional PHP frameworks follow a "code-first" approach where PHP scripts control the flow and include template fragments. PAD inverts this paradigm: templates are first-class citizens that orchestrate everything from data retrieval to output generation. This "template-first" approach creates a natural separation where the visual structure of the application mirrors its logical structure.

### Key Benefits

- **Visual-Logical Unity**: Template structure reflects application flow
- **Reduced Boilerplate**: No manual routing, controller classes, or view binding
- **Hierarchical Inheritance**: Templates inherit from parent directories automatically
- **Clean Syntax**: Minimal, readable template syntax with `{tags}`
- **Zero Configuration**: Convention over configuration - just create files and directories

## Requirements

- PHP 8.0 or higher
- Web server (Apache, Nginx, etc.)

## Installation

1. Set up two constants before including PAD:
   - `APP` - Path to your application directory (must end with `/`)
   - `DAT` - Path to your data directory (must end with `/`)

2. Include the PAD framework:

```php
<?php
define('APP', '/path/to/your/app/');
define('DAT', '/path/to/your/data/');
include '/path/to/pad/pad.php';
?>
```

## Hello World

Create a simple page in your APP directory:

**APP/hello.pad**
```
<h1>Hello, {$name}!</h1>
<p>Welcome to PAD.</p>
```

**APP/hello.php**
```php
<?php
return ['name' => 'World'];
?>
```

Visit `/hello` in your browser - PAD automatically pairs the `.php` and `.pad` files.

## Next Steps

- [DOCUMENTATION.md](DOCUMENTATION.md) - Detailed documentation
- [REFERENCE.md](REFERENCE.md) - API reference index
- [INDEX.md](INDEX.md) - Module documentation index
