# call/

Handles PHP file inclusion with error handling and output buffering.

## Files
- `_call.php` - Main include logic with try/catch wrapper
- `_init.php` - Setup before include
- `_exit.php` - Cleanup after include
- `_return.php` - Return value handling
- `_once.php` / `once.php` - Include-once variants
- `_try.php` / `_tryOnce.php` - Try-catch wrapped includes
- `ob.php` / `obNoOne.php` - Output buffered includes
- `any.php` / `noOne.php` - Alternative include modes

## Usage
Used internally to safely include PHP files (tags, functions, types) with consistent error handling and event tracking.
