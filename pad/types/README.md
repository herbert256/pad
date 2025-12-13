# PAD Framework - Type Handlers

## Overview

The `types/` directory contains the core type handler system for the PAD framework. Each PHP file in this directory implements a specific data type handler that processes different kinds of content, data structures, and operations within the PAD templating and data processing system.

Type handlers are dynamically loaded based on the `$padType[$pad]` variable and are responsible for retrieving, processing, and returning data according to their specific type semantics.

## Purpose

This module serves as the type dispatch system for PAD, providing specialized handlers for:
- Data access and retrieval
- Content processing
- Function execution
- Script invocation
- Database table operations
- Field value access
- Property management
- File inclusion
- Application integration

## Directory Structure

### Core Files

#### Data Type Handlers
- **data.php** - Retrieves data from the `$padDataStore` with optional print formatting
- **array.php** - Handles array value access via `padArrayValue()`
- **field.php** - Retrieves database field values with NULL handling
- **table.php** - Manages database table operations and queries
- **property.php** - Accesses tag properties with optional parameter prefixing
- **local.php** - Loads local data files (PHP, PAD, or raw content) with sandbox support

#### Content & Inclusion
- **content.php** - Processes content via `PAD/get/content.php`
- **include.php** - Handles file inclusion via `PAD/get/include.php`
- **pad.php** - Loads PAD tag files from the `tags/` directory
- **app.php** - Integrates application-specific tags from `APP2/` directory
- **xyz.php** - Loads page content via `PAD/get/page.php`

#### Execution & Processing
- **function.php** - Executes PAD functions with start/end tag support
- **php.php** - Calls PHP functions dynamically using `call_user_func_array()`
- **script.php** - Executes external scripts with argument passing and output capture
- **action.php** - Processes action types via `PQ/start/types/action.php`

#### Primitive Types
- **bool.php** - Returns boolean values from `$padBoolStore`
- **constant.php** - Retrieves PHP constants by name
- **level.php** - Returns level arrays via `padGetLevelArray()`

#### Control Flow & Operations
- **flag.php** - Handles flag operations via `PQ/start/types/flag.php`
- **keep.php** - Keeps data via `PQ/start/types/keep.php`
- **make.php** - Creates/makes entities via `PQ/start/types/make.php`
- **pull.php** - Pulls data via `PQ/start/types/pull.php`
- **remove.php** - Removes data via `PQ/start/types/remove.php`
- **sequence.php** - Manages sequences via `PQ/start/types/sequence.php`

### Subdirectories

#### go/
Contains secondary processing handlers that are invoked by primary type handlers:

- **tag.php** - Loads and processes tag files (`.php` and `.pad` files), combining PHP output with PAD content
- **table.php** - Sets up table context and calls the `padTable()` function
- **local.php** - Processes local files with support for:
  - PHP file execution via `PAD/call/any.php`
  - Sandboxed content processing
  - File type and name parameter handling
  - Automatic data type detection based on file extension

## Key Features

### Dynamic Type Dispatch
All type handlers are loaded dynamically based on the current tag type, enabling flexible content processing without hardcoded type checks.

### Global Variable System
Type handlers operate on a set of global variables:
- `$pad` - Current processing level/index
- `$padTag[$pad]` - Tag name/identifier
- `$padType[$pad]` - Type identifier
- `$padOpt[$pad]` - Tag options/parameters
- `$padContent` - Content to be processed
- `$padResult[$pad]` - Processing result

### Data Source Integration
Type handlers integrate with multiple data sources:
- `$padDataStore` - Main data storage
- `$padBoolStore` - Boolean value storage
- Database fields and tables
- File system (local files, includes)
- External scripts and applications

### Script Execution Security
The `script.php` handler provides:
- Argument escaping with `escapeshellarg()`
- Error code checking and reporting
- Output capture and formatting
- Glob pattern support for script selection

### Local File Processing
The `local.php` type supports multiple processing modes:
- Direct PHP execution for `.php` files
- Sandboxed content processing
- Standard code processing
- Automatic name and type parameter handling

## Integration with PAD Framework

### Type Resolution Flow
1. Tag is parsed and `$padType[$pad]` is determined
2. Corresponding type handler file is included: `PAD/types/{$padType[$pad]}.php`
3. Type handler processes the tag using global variables
4. Result is returned and stored in `$padResult[$pad]`

### Walker Integration
Type handlers are called during tree walking operations:
- `walk/end.php` calls type handlers to process tag results
- `walk/next.php` manages iteration through data structures

### Delegation Pattern
Many type handlers delegate to specialized subsystems:
- `get/` directory for content retrieval
- `call/` directory for function invocation
- `PQ/start/types/` for operation-specific processing
- `types/go/` for secondary processing stages

### Tag System Integration
- `pad.php` and `app.php` load custom tag definitions
- `function.php` integrates with the function system
- `property.php` enables tag property access

## Usage Context

Type handlers are not called directly by user code. Instead, they are:
1. Automatically selected based on tag type during parsing
2. Included and executed by the PAD processing engine
3. Expected to return a value representing the processed result
4. Operating within the context of global PAD variables

## Architecture Notes

- Type handlers are stateless snippets that rely on global context
- Most handlers are simple dispatchers to more complex subsystems
- The system uses PHP's dynamic include mechanism for extensibility
- Type handlers can trigger recursive PAD processing for nested content
- Security considerations exist around dynamic includes, script execution, and function calls
