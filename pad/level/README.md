# PAD Framework Level Management Module

## Overview
The level directory is the heart of the PAD framework's tag processing and scope management system. It handles the parsing, execution, and nesting of PAD tags, managing the hierarchical structure of tag levels, parameters, and data flow throughout the template execution lifecycle.

## Purpose
This module provides the core engine for:
- Tag parsing and type detection
- Level (scope) creation and management
- Parameter processing and validation
- Data iteration and occurrence handling
- Tag opening, processing, and closing
- Function tag execution
- Pipe operations and data transformation

## Directory Structure

### Main Level Files
- **level.php** - Main entry point for level processing, orchestrates the complete tag lifecycle
- **start.php** - Handles tag opening and initialization
- **end.php** - Manages tag closing and cleanup
- **go.php** - Executes the tag type handler and merges content
- **setup.php** - Sets up level variables and initializes the scope

### Tag Processing
- **between.php** - Extracts content between tag delimiters
- **close.php** - Handles tag closing operations
- **split.php** - Splits tag content into components
- **base.php** - Manages base content for the current level
- **data.php** - Handles data assignment and processing
- **name.php** - Processes tag naming
- **set.php** - Manages variable setting operations
- **pair.php** - Handles paired tag operations
- **function.php** - Executes function tags

### Subdirectories
- **parms/** - Parameter parsing and processing
  - `parms.php` - Main parameter processor
  - `parameter.php` - Handles positional parameters
  - `option.php` - Processes named options
  - `variable.php` - Manages variable assignments
- **pipes/** - Pipe operation handlers
  - `start.php` - Pre-processing pipes
  - `before.php` - Before-execution pipes
  - `after.php` - Post-execution pipes
- **start_end/** - Special start/end tag handlers
  - `end1.php` - First end handler
  - `end2.php` - Second end handler
  - `start1.php` - First start handler
  - `start2.php` - Second start handler

## Key Features

### Level Processing Lifecycle

The level.php file orchestrates the complete tag processing lifecycle:

1. **Restart Check** - Handle framework restart requests
2. **End Detection** - Check for closing tag
3. **Start Detection** - Find opening tag delimiter
4. **Between Extraction** - Extract content between delimiters
5. **Comment Handling** - Process and skip comments
6. **Type Detection** - Determine tag type (field, function, tag, etc.)
7. **Execution** - Run the appropriate handler
8. **Cleanup** - Reset level variables and decrement scope

### Level Setup (setup.php)

Creates a new level scope with initialization of:
- Level counter increment ($pad++)
- Unique level ID assignment
- Parameter arrays (options, parameters, parsed parameters)
- Data and current value arrays
- Walk state management
- Occurrence tracking
- Output and result storage
- Boolean flags (hit, null, else, array, etc.)
- Save/delete operation tracking
- Start/end base content
- Function variables
- Event tracking for debugging

### Tag Type Detection

The framework detects various tag types:
- **Variables** ($, !, #, &, ?, @) - Dynamic value references
- **Fields** - Data field access
- **Functions** - Executable function calls
- **Tags** - Named PAD tags
- **Sequences** - Sequential data operations
- **Arrays** - Array data handling
- **Properties** - Object/array property access
- **Constants** - PHP constants
- **Includes** - File inclusion
- **Pages** - Page routing

### Parameter Processing

The parms subdirectory handles three types of parameters:

1. **Variables ($var=value, %var=value)** - Variable assignments
2. **Options (name=value)** - Named options with values
3. **Parameters (positional)** - Unnamed positional arguments

Each parameter is tracked with:
- `padPrmKind` - Type of parameter (variable/option/parameter)
- `padPrmName` - Parameter name
- `padPrmValue` - Parameter value
- `padPrmOrg` - Original unparsed parameter string

### Data Iteration and Occurrences

The level system manages data iteration:
- Multiple occurrences of the same tag for each data item
- Walk states: 'start', 'next', 'end'
- Occurrence counter tracking
- Start/end data management
- Data array navigation

### Pipe Operations

Pipes transform data at different stages:
- **Before pipes** ($padPipeBefore) - Pre-execution transformations
- **After pipes** ($padPipeAfter) - Post-execution transformations
- Support for chained pipe operations

### Content Merging

The go.php file handles content generation:
- Executes tag type handler
- Captures output buffering
- Processes dump options
- Merges content based on hit status
- Handles else conditions
- Applies content transformations

## Level Variables

Each level maintains its own set of variables (defined in padLevelVars):

### Core Variables
- `$padTag` - Tag name
- `$padType` - Tag type
- `$padPair` - Paired tag indicator
- `$padPrm` - Processed parameters
- `$padName` - Tag instance name
- `$padData` - Data array for iteration
- `$padCurrent` - Current data item

### State Variables
- `$padWalk` - Walk state (start/next/end)
- `$padDone` - Completion tracking
- `$padOccur` - Current occurrence number
- `$padStart` - Start position in output
- `$padEnd` - End position in output
- `$padHit` - Tag execution success flag
- `$padNull` - Null value flag
- `$padElse` - Else condition flag

### Content Variables
- `$padBase` - Base content
- `$padOut` - Output buffer
- `$padResult` - Execution result
- `$padSource` - Source content
- `$padOrg` - Original content

### Advanced Features
- `$padLvlFun` - Function tag indicator
- `$padLvlFunVar` - Function variables
- `$padTable` - Table data
- `$padSaveLvl` - Level save operations
- `$padDeleteLvl` - Level delete operations
- `$padOptionsAppStart` - App-level start options

## Integration with PAD Framework

The level module is central to PAD's architecture:

1. **Template Engine Core** - Processes all PAD template tags
2. **Scope Management** - Maintains hierarchical variable scopes (up to 26 levels)
3. **Type System** - Routes tags to appropriate type handlers (PAD/types/)
4. **Event System** - Triggers events for debugging and monitoring
5. **Data Flow** - Manages data through tag hierarchy
6. **Error Handling** - Provides context for error reporting
7. **Performance Tracking** - Integrates with info system for profiling

## Tag Processing Flow

```
level.php (main entry)
  ├── Check for restart
  ├── padLevelEnd() - Find closing }
  │   └── end.php if found
  ├── padLevelStart() - Find opening {
  ├── padLevelBetween() - Extract content between { }
  ├── Comment check and skip if needed
  ├── Type detection (field/function/tag)
  ├── setup.php - Initialize new level
  │   ├── parms/parms.php - Parse parameters
  │   ├── split.php - Split tag components
  │   ├── set.php - Set variables
  │   ├── go.php - Execute tag handler
  │   ├── base.php - Set base content
  │   ├── data.php - Assign data
  │   ├── name.php - Set tag name
  │   └── Options processing (dump, callbacks, etc.)
  └── Occurrence loop or completion
```

## Usage Examples

### Simple Tag Processing
```php
{fieldName}  // Processed by level.php → type detection → field handler
```

### Tag with Parameters
```php
{tag param1 option=value}  // Parameters parsed by parms/parms.php
```

### Nested Tags
```php
{outer}
  {inner}content{/inner}
{/outer}
// Each creates a new level, incrementing $pad
```

### Data Iteration
```php
{tag data=$array}
  {field}  // Repeated for each item in $array
{/tag}
```

## Error Handling

The level system provides error detection for:
- Too many nested levels (>25)
- Closing tags without opening tags
- Invalid tag names
- Type detection failures
- Parameter parsing errors

## Notes

- Maximum nesting depth is 26 levels (0-25)
- Level counter ($pad) starts at -1 and increments with each new tag
- Each level maintains complete independence of variables
- The system supports both single tags and paired tags
- Comments use the format {# comment text #}
- Optional tags can be specified with the 'optional' parameter
- The @start@ and @end@ markers enable special tag behaviors
