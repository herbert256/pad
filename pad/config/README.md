# PAD Configuration Directory

This directory contains the core configuration files for the PAD (PHP Application Development) framework. These configuration files control various aspects of PAD's behavior including error handling, output modes, caching, database connections, and debugging capabilities.

## Overview

The configuration system in PAD uses PHP variables to control framework behavior. The main configuration file (`config.php`) defines default settings that can be overridden by application-specific configurations. Additional configuration modules in subdirectories provide specialized settings for information tracking and output handling.

## Files

### Core Configuration Files

- **config.php** - Main configuration file containing:
  - Error handling settings (`$padErrorAction`, `$padErrorLevel`, etc.)
  - Information/debugging mode selection (`$padInfo`)
  - Output type configuration (`$padOutputType`)
  - Cache control settings (`$padCache`)
  - Database connection parameters (internal PAD and application databases)
  - File/directory permissions (`$padDirMode`, `$padFileMode`)
  - Date/time formatting defaults
  - Session variable tracking
  - Data processing defaults
  - HTML tidying options
  - Miscellaneous settings (gzip, cookies, FastLink length)

- **cache.php** - Comprehensive caching configuration:
  - Server-side cache age settings
  - Proxy cache control
  - Client-side cache age
  - Cache storage types (file/db/memory)
  - Memcached connection settings
  - Database cache settings
  - File cache directory and permissions

- **sequence.php** - Sequence generation settings:
  - Default number of rows for sequences (`$padSeqDefaultRows`)
  - Default number of retry attempts (`$padSeqDefaultTries`)

- **tidy.php** - HTML output formatting configuration:
  - lib tidy settings (PHP Tidy extension configuration)
  - Character encoding settings
  - HTML formatting rules (indentation, wrapping, cleaning)
  - myTidy settings (custom PAD HTML formatter when lib tidy is unavailable)

## Subdirectories

### info/

Contains configuration presets for various debugging and tracking modes. Files can be loaded by setting `$padInfo` in the main configuration.

- **all.php** - Enables ALL tracking and debugging features (maximum verbosity)
- **none.php** - Disables all tracking and debugging features
- **go.php** - Minimal configuration (basic operation only)
- **info.php** - General information tracking
- **myInfo.php** - Custom information tracking preset
- **stats.php** - Runtime statistics tracking (time and CPU usage)
- **trace.php** - Internal PAD operation tracing with balanced verbosity
- **track.php** - Session and request tracking (file-based by default)
- **xml.php** - XML structure generation for PAD pages
- **xref.php** - Cross-reference directory building

### output/

Contains configuration presets for different output modes. The appropriate file is loaded based on `$padOutputType`.

- **web.php** - Web output configuration (HTTP headers, content type, etag support)
- **file.php** - File output configuration (filename patterns, directory, timestamp options)
- **download.php** - Download output configuration (force file download via HTTP)
- **console.php** - Console/CLI output configuration (disables HTML tidying)

## Key Features

### Error Handling Modes

The framework provides multiple error handling strategies:
- `pad` - Full PAD error handler with reporting and logging
- `boot` - Lightweight bootstrap error handler
- `php` - Standard PHP error handling
- `stop` - Stop processing with PAD cleanup
- `exit` - Immediate exit without cleanup
- `ignore` - Continue despite errors
- `log` - Log to Apache error log and continue
- `dump` - Dump errors to DATA directory and continue

### Information Tracking

The `$padInfo` variable can load one or more presets from the `info/` directory to enable various tracking features:
- Request/session tracking
- Performance statistics
- Internal operation tracing
- XML structure documentation
- Cross-reference generation

### Output Types

Output can be directed to different destinations:
- `web` - Standard web output with HTTP headers
- `file` - Save to file system
- `download` - Force browser download
- `console` - Command-line interface output

### Caching

Multi-level caching support:
- Server-side (file, database, or memcached)
- Proxy caching
- Client-side caching
- Configurable cache ages for each level

## Integration with PAD Framework

This configuration directory is central to PAD's operation:

1. **Bootstrap Phase**: Main `config.php` is loaded during PAD initialization
2. **Output Mode**: The appropriate `output/*.php` file is loaded based on `$padOutputType`
3. **Info Mode**: Files from `info/*.php` are loaded based on `$padInfo` setting
4. **Runtime**: Configuration variables control behavior throughout request processing
5. **Error Handling**: Error handler configuration affects how PAD responds to PHP errors and exceptions
6. **Performance**: Cache settings affect response time and resource usage
7. **Debugging**: Info settings control visibility into PAD's internal operations

## Usage

To customize PAD behavior, modify variables in `config.php` or create application-specific configuration that overrides these defaults. The framework loads these configurations in a specific order, allowing for flexible configuration inheritance.
