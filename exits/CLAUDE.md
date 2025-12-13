# exits/

Final output processing and exit handling.

## Files
- `exits.php` - Main exit orchestration (tidying, caching, output)
- `exit.php` - Clean exit handler
- `output.php` - Output dispatcher
- `tidy.php` - HTML tidy processing
- `myTidy.php` - Custom tidy implementation

## Subdirectory
- `output/` - Output type handlers (web, file, console, download)

## Exit Flow
1. Convert result to output string via `padOutput()`
2. Apply tidy if `$padTidy` or `$padMyTidy` enabled
3. Generate ETag
4. Handle caching if enabled
5. Send output via appropriate handler
