# callback/

Handles callback execution during tag iteration.

## Files
- `callback.php` - Main callback dispatcher
- `row.php` - Row-level callback (called for each iteration)
- `init.php` - Initialization callback
- `exit.php` - Exit/cleanup callback

## Usage
Callbacks are triggered via the `callback` tag parameter, allowing custom PHP code to execute at different points during data iteration.
