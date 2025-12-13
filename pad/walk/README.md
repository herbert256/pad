# PAD Framework - Tree Walking Utilities

## Overview

The `walk/` directory contains the tree/structure walking utilities for the PAD framework. These files implement the core iteration and traversal logic that enables PAD to process nested data structures, tags, and content hierarchies.

The walker system manages the state transitions and processing flow as PAD traverses through complex data structures and tag trees.

## Purpose

This module provides the fundamental walking/iteration mechanism for PAD's processing engine, enabling:
- Traversal of nested data structures
- Sequential processing of tag hierarchies
- State management during iteration
- Result collection and aggregation
- Event notification during walk operations

## Files

### next.php
Handles the "next" step in tree walking operations.

**Key Responsibilities:**
- Triggers walk event notifications via `events/walk.php` (when `$padInfo` is set)
- Sets the walk state to 'next' in `$padWalk[$pad]`
- Attempts to execute the next level via `level/go` through the try/try.php mechanism
- Processes level flags via `level/flags.php`
- Stores tag results in `$padData[$pad]` for array-based processing
- Resets the data array pointer to beginning after iteration

**Processing Flow:**
```
1. Check $padInfo and fire walk event if needed
2. Set $padWalk[$pad] = 'next'
3. Try to execute next level (level/go)
4. Process level flags
5. If walk continues and array mode is active:
   - Store tag result in $padData[$pad]
   - Reset array pointer to start
```

**Global Variables Used:**
- `$padInfo` - Controls event triggering
- `$pad` - Current processing level
- `$padWalk[$pad]` - Walk state tracker
- `$padTry` - Try/catch mechanism selector
- `$padArray[$pad]` - Array mode flag
- `$padData[$pad]` - Data storage for current level
- `$padTagResult` - Result from tag processing

### end.php
Handles the "end" step in tree walking operations.

**Key Responsibilities:**
- Triggers walk event notifications via `events/walk.php` (when `$padInfo` is set)
- Sets the walk state to 'end' in `$padWalk[$pad]`
- Invokes the appropriate type handler for final processing
- Manages result transfer between `$padResult[$pad]` and `$padContent`
- Processes level flags after type handling

**Processing Flow:**
```
1. Check $padInfo and fire walk event if needed
2. Set $padWalk[$pad] = 'end'
3. Save $padResult[$pad] to $padContent
4. Extract optional parameter from $padOpt[$pad][1]
5. Execute type handler: types/{$padType[$pad]}.php
6. Store result back in $padResult[$pad]
7. Process level flags
```

**Global Variables Used:**
- `$padInfo` - Controls event triggering
- `$pad` - Current processing level
- `$padWalk[$pad]` - Walk state tracker
- `$padResult[$pad]` - Processing result storage
- `$padContent` - Content being processed
- `$padOpt[$pad]` - Tag options array
- `$padParm` - Extracted parameter value
- `$padType[$pad]` - Type identifier for handler selection

## Key Features

### State Management
The walker maintains state through `$padWalk[$pad]` which can be:
- `'next'` - Moving to next item/level in iteration
- `'end'` - Completing current level processing
- Other states (controlled externally)

### Event System Integration
Both walker files integrate with the event system:
```php
if ( $padInfo )
    include PAD . 'events/walk.php';
```
This enables debugging, logging, and monitoring of walk operations.

### Level-Based Processing
The walker operates on a level-based architecture where:
- `$pad` represents the current depth/level
- Each level has its own walk state, data, results, and options
- Levels can be nested for recursive processing

### Array Iteration Support
`next.php` provides special handling for array-based iteration:
- Checks `$padArray[$pad]` flag to determine if in array mode
- Stores tag results in `$padData[$pad]` array
- Resets array pointer after storing result

### Dynamic Type Dispatch
`end.php` uses dynamic type loading:
```php
include PAD . "types/" . $padType [$pad] . ".php";
```
This enables flexible type handling without hardcoded type checks.

## Integration with PAD Framework

### Walker Lifecycle
1. **Initialization** - Level is set up with data, type, and options
2. **Next Phase** (next.php) - Iterate through structure, fire events, try next level
3. **End Phase** (end.php) - Process results through type handler, finalize level
4. **Completion** - Results stored, flags processed, level cleaned up

### Relationship with Types System
The walker is tightly integrated with the type system:
- `end.php` dynamically loads type handlers based on `$padType[$pad]`
- Type handlers process the accumulated results from walking
- Results flow: walk → accumulate → type handler → final result

### Level System Integration
Both walker files interact with the level system:
- `level/go` - Attempts to process next level
- `level/flags.php` - Processes level-specific flags after walk operations
- Level hierarchy maintained through `$pad` variable

### Try/Catch Mechanism
`next.php` uses the try/try.php system:
```php
$padTry = 'level/go';
include PAD . 'try/try.php';
```
This provides error handling and graceful failure for level transitions.

### Event System
Walk events enable:
- Debugging and tracing of walk operations
- Performance monitoring
- Custom hooks during tree traversal
- State inspection at walk points

## Usage Context

Walker files are not called directly by user code. Instead, they are:
1. Included by the PAD processing engine during structure traversal
2. Called automatically when processing nested tags and data
3. Operating within the context of global PAD variables
4. Managing state transitions transparently

## Processing Model

### Data Flow in next.php
```
Input: Current level state
  ↓
Fire walk event (if $padInfo set)
  ↓
Set walk state to 'next'
  ↓
Try to execute next level
  ↓
Process level flags
  ↓
If array mode: store result and reset pointer
  ↓
Output: Ready for next iteration or end
```

### Data Flow in end.php
```
Input: Accumulated results in $padResult[$pad]
  ↓
Fire walk event (if $padInfo set)
  ↓
Set walk state to 'end'
  ↓
Transfer result to $padContent
  ↓
Extract optional parameter
  ↓
Execute type handler
  ↓
Store processed result back in $padResult[$pad]
  ↓
Process level flags
  ↓
Output: Final processed result
```

## Global Variables Reference

### Walk State Variables
- `$padWalk[$pad]` - Current walk state ('next', 'end', etc.)
- `$padInfo` - Debug/event information flag

### Data Variables
- `$padData[$pad]` - Data array for current level
- `$padResult[$pad]` - Processing result for current level
- `$padContent` - Content being processed
- `$padTagResult` - Result from tag processing

### Configuration Variables
- `$pad` - Current processing level/depth
- `$padType[$pad]` - Type handler identifier
- `$padOpt[$pad]` - Tag options and parameters
- `$padParm` - Extracted parameter value
- `$padArray[$pad]` - Array mode flag
- `$padTry` - Try/catch mechanism selector

## Architecture Notes

- Walker files are stateless snippets that rely on global context
- State is maintained through global arrays indexed by `$pad` level
- The walker supports recursive/nested processing through level management
- Event firing is conditional based on `$padInfo` flag
- Type handlers are dynamically loaded for maximum flexibility
- Array pointer management enables iteration through data structures
- The try/try.php mechanism provides error resilience during level transitions
