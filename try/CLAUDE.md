# try/

Try-catch error handling wrapper for PHP includes.

## Files
- `try.php` - Main try-catch wrapper that includes files safely

## Subdirectory
- `catch/` - Catch handlers for different contexts
  - `call/` - Call-specific catch handling
  - `eval/` - Evaluation catch handling
  - `level/` - Level processing catch handling

## Usage
Wraps PHP `include` statements in try-catch blocks to handle errors gracefully. The `$padTry` variable specifies which catch handler to use.

## Pattern
```php
$padTry = 'level/var';
return include PAD . 'try/try.php';
```
