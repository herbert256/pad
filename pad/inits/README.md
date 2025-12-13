# PAD Framework Initialization Module

## Overview
The inits directory contains the core initialization system for the PAD framework. This module bootstraps the framework by loading all necessary components, setting up the environment, and preparing the system for request processing. The `inits.php` file serves as the main entry point that orchestrates the sequential loading of all initialization modules.

## Purpose
This module handles the critical startup sequence of the PAD framework, including:
- Loading framework constants and configuration
- Initializing core libraries and utilities
- Setting up request handling and session management
- Configuring the output environment
- Preparing the database connections
- Establishing error handling and caching mechanisms

## Files and Components

### Core Initialization
- **inits.php** - Main orchestrator that loads all initialization modules in the correct sequence
- **const.php** - Defines framework-wide constants including level variables, storage types, and options
- **vars.php** - Initializes global variables used throughout the framework
- **lib.php** - Loads core library functions and utilities

### Configuration
- **config.php** - Loads main configuration files, output type settings, and application-specific configs
- **configSet.php** - Handles dynamic configuration settings via $padSetConfig
- **app.php** - Initializes the application build system (defines APP2 constant and includes build.php)

### Request Handling
- **page.php** - Sets up page-level variables and routing
- **host.php** - Configures host, scheme, port, and URL generation ($padHost, $padGo, $padGoExt)
- **client.php** - Processes client headers (ETag, If-Modified-Since, Accept-Encoding)
- **cookies.php** - Initializes cookie handling
- **parms.php** - Processes request parameters
- **ids.php** - Sets up session and request ID tracking

### System Services
- **database.php** - Recursively loads all PHP files from the APP _database directory
- **cache.php** - Initializes caching mechanisms
- **fast.php** - Sets up fast-path optimizations
- **error.php** - Configures error handling and reporting
- **level.php** - Initializes the level/scope management system
- **clean.php** - Performs cleanup operations
- **nono.php** - Sets up security restrictions
- **info.php** - Configures information/debugging systems

## Key Features

### Bootstrap Sequence
The initialization follows a carefully ordered sequence:
1. Performance timing markers (microtime and hrtime)
2. Constants and library loading
3. Variable initialization and cleanup
4. Page and request setup
5. Configuration loading
6. Client and host detection
7. Service initialization (cache, database, errors)
8. Level system preparation

### Constants Defined
- **padLevelVars** - Array of variable names used in the level/scope system
- **padStrSto** - Storage-related string variables
- **padStrDat** - Data-related string variables
- **padOptionsStart** - Options processed at tag start (track, before, dedup, page, etc.)
- **padOptionsEnd** - Options processed at tag end (toBool, toContent, toData, etc.)
- **padOptionsCallback** - Callback-related options
- **PQ** - Path to sequence directory
- **PT** - Path to sequence types directory

### Host and URL Setup
Configures the complete request environment:
- Request scheme (http/https)
- HTTP host and port
- Script name and request URI
- Base host URL ($padHost)
- Query string generator ($padGo, $padGoExt)

## Integration with PAD Framework

The initialization module is the foundation of the PAD framework architecture:

1. **Entry Point** - Called early in the request lifecycle to set up the entire framework
2. **Dependency Chain** - Establishes the dependency chain by loading files in the correct order
3. **Global State** - Initializes all global variables and constants that other modules depend on
4. **Configuration Cascade** - Implements a multi-layer configuration system (framework -> app -> custom)
5. **Performance Tracking** - Sets up timing markers for performance monitoring
6. **Environment Detection** - Determines if running in web or CLI mode and adjusts accordingly

## Usage

The initialization module is typically loaded once at the beginning of each request:

```php
if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
if ( ! isset ( $padHR    ) ) $padHR    = hrtime    ( TRUE );

include_once PAD . 'inits/const.php';
include_once PAD . 'inits/lib.php';

include PAD . 'inits/vars.php';
// ... rest of initialization sequence
```

## Configuration Points

The module supports multiple configuration layers:
- Framework default configuration (PAD/config/config.php)
- Output type-specific config (PAD/config/output/$padOutputType.php)
- Application configuration (APP/_config/config.php) - loaded twice to allow overrides
- Dynamic configuration via $padSetConfig array

## Notes

- Files are loaded with `include` (not `include_once`) to allow re-initialization if needed
- Constants and libraries use `include_once` to prevent redefinition
- CLI detection automatically switches output type from 'web' to 'console'
- Performance timing is captured before any other initialization occurs
- Database files are loaded recursively from the application's _database directory
