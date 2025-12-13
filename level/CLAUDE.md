# level/

Core tag processing at each nesting level. This is the heart of PAD's template engine.

## Main Files
- `level.php` - Main level processing loop
- `go.php` - Executes a single tag (includes type handler from `types/`)
- `type.php` - Resolves tag to its type
- `tag.php` - Tag name resolution
- `var.php` - Variable (`$`, `&`, `#`, etc.) processing
- `setup.php` - Level setup and initialization

## Tag Lifecycle
- `start.php` - Tag start processing
- `end.php` - Tag end processing
- `between.php` - Content between open/close tags
- `pair.php` - Paired tag handling
- `close.php` - Closing tag processing
- `flags.php` - Tag flag processing

## Subdirectories
- `parms/` - Parameter parsing
- `pipes/` - Pipe processing (before/after)
- `start_end/` - Start/end lifecycle handlers

## Processing Flow
1. `level.php` loops while tags remain at current level
2. For each tag: resolve type, setup context, execute handler
3. Tag handler returns result, flags processed
4. Content merged into output
5. Move to next tag or descend into nested level
