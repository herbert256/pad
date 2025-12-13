# handling/

Special handling for edge cases and negative scenarios during tag processing.

## Files
- `handling.php` - Main handling dispatcher

## Subdirectories
- `negative/` - Handlers for negative/falsy results
- `types/` - Type-specific handling overrides

## Usage
Used internally when standard tag processing needs special behavior for certain conditions like empty data, missing tags, or type mismatches.
