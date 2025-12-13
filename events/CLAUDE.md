# events/

Event tracking hooks for debugging, tracing, and monitoring PAD execution.

## Event Types
Triggered when `$padInfo` is set. Each file logs/tracks specific events:

### Processing Events
- `build.php` - Template build completion
- `levelStart.php` / `levelEnd.php` - Level processing start/end
- `occurStart.php` / `occurEnd.php` - Occurrence processing start/end
- `go.php` - Tag execution
- `type.php` - Type resolution

### Field/Variable Events
- `fieldStart.php` / `fieldEnd.php` - Field evaluation
- `fieldAt.php` - @ property access
- `fieldClassic.php` - Classic field syntax
- `var_start.php` / `var_end.php` - Variable processing

### Other Events
- `callStart.php` / `callEnd.php` - PHP file inclusion
- `parms.php` / `parse.php` - Parameter parsing
- `options.php` / `option.php` - Option processing
- `atGroups.php` / `atProperties.php` / `atTypes.php` - @ syntax events
- `curl.php` / `sql.php` / `data.php` - Data loading events

## Subdirectory
- `eval/` - Evaluation-specific events
