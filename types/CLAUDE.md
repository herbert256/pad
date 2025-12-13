# types/

Tag type handlers that execute the actual tag logic.

## Core Type Handlers
- `app.php` - Application-defined tags (from APP2 directory)
- `pad.php` - Built-in PAD tags (from `tags/` directory)
- `function.php` - Function execution
- `data.php` - Data store access
- `content.php` - Content store access

## Reference Types
- `field.php` - Database/form field access
- `property.php` - Tag property access (via `@`)
- `local.php` - Local data file access
- `constant.php` - PHP constant access
- `php.php` - PHP function calls
- `array.php` - Array access
- `level.php` - Level variable access

## Include Types
- `include.php` - Include files
- `page.php` - Page inclusion
- `script.php` - Script inclusion
- `sequence.php` - Sequence type

## Action Types
- `action.php` - Action execution
- `flag.php` - Boolean flag
- `keep.php` / `pull.php` / `remove.php` / `make.php` - Store operations

## Subdirectory
- `go/` - Type execution helpers (tag.php, etc.)

## How It Works
When a tag is processed, `level/type.php` determines the type, then `level/go.php` includes the appropriate handler from this directory via:
```php
include PAD . "types/" . $padType[$pad] . ".php";
```
