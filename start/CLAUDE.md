# start/

Processing start/entry points and lifecycle management.

## Entry Points
- `enter/` - Main entry handling
  - `start.php` - Primary entry (loads inits, starts level processing, runs exits)
  - `restart.php` - Restart handling

## Lifecycle
- `start/` - Start phase handlers
- `end/` - End phase handlers

## Files
- `level.php` - Initiates level processing loop
- `start.php` - Tag start handling
- `end.php` - Tag end handling
- `pad.php` - PAD-specific start handling
- `page.php` - Page start handling
- `parms.php` - Parameter start handling
- `function.php` - Function start handling
- `code.php` - Code block handling

## Processing Flow
1. `enter/start.php` loads initialization
2. `level.php` starts the main processing loop
3. `exits.php` handles output and cleanup
