# config/

Configuration files for PAD behavior.

## Main Config (`config.php`)
- `$padErrorAction` - Error handling mode ('pad', 'boot', 'php', 'stop', 'exit', 'ignore', 'log', 'dump')
- `$padErrorLevel` - PHP error levels to handle ('none', 'error', 'warning', 'notice', 'all')
- `$padOutputType` - Output destination ('web', 'file', 'download', 'console')
- `$padCache` - Enable/disable caching
- `$padTidy` - Enable HTML tidying
- SQL connection parameters for PAD and application databases

## Subdirectories
- `info/` - Configuration for info/tracing features
- `output/` - Output type-specific configurations

## Defaults
- `$padDataDefaultStart` / `$padDataDefaultEnd` - Default function pipes for variables
- `$padDirMode` / `$padFileMode` - File permission modes
- Date/time format strings
