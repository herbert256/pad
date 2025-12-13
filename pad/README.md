# PAD Framework

**PHP Application Driver** - An Inversion of Control Template Engine

PAD is a PHP template engine that inverts the traditional web application architecture. Instead of PHP code including templates, PAD templates drive the execution flow, seamlessly integrating data access, control structures, and presentation in a unified template syntax.

## Documentation

| Document | Description |
|----------|-------------|
| [INTRO.md](INTRO.md) | Introduction, installation, and Hello World example |
| [DOCUMENTATION.md](DOCUMENTATION.md) | Detailed documentation of concepts, syntax, and architecture |
| [INDEX.md](INDEX.md) | Index of all module README files |
| [REFERENCE.md](REFERENCE.md) | Index of all API reference files |
| [EXPLAIN.md](EXPLAIN.md) | Technical deep dives into complex subsystems |
| [DEVELOPMENT.md](DEVELOPMENT.md) | Resources for maintainers (analysis, known bugs) |

## Quick Start

```php
<?php
define('APP', '/path/to/your/app/');
define('DAT', '/path/to/your/data/');
include '/path/to/pad/pad.php';
?>
```

Create `hello.php` returning data and `hello.pad` as the template - PAD pairs them automatically.

See [INTRO.md](INTRO.md) for the complete Hello World example.
