# occurrence/

Parses template content to find tag occurrences and builds the processing table.

## Files
- `occurrence.php` - Main occurrence orchestration
- `init.php` - Initialize occurrence parsing
- `set.php` - Set occurrence data
- `table.php` - Build occurrence table
- `end.php` - Finalize occurrence parsing

## Purpose
Before level processing begins, the occurrence system scans the template to:
1. Find all `{tag}` patterns
2. Match opening and closing tags
3. Build a table of tag positions and nesting
4. Prepare data structures for iteration

## Output
Creates `$padOccur` array with tag positions, enabling efficient iteration during level processing.
