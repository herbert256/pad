# walk/

Iterator/walker system for traversing data during tag iteration.

## Files
- `next.php` - Move to next iteration item
- `end.php` - Handle end of iteration

## Purpose
Manages the iteration state when processing tags that loop over data (arrays, database results, sequences). Controls advancing through items and detecting when iteration is complete.

## Integration
Used by level processing to handle `{tag}...{/tag}` pairs that iterate over data, managing the `$padWalk` state variable.
