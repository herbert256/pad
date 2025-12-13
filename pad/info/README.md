# PAD Info Directory

## Overview

The `info/` directory implements the debugging, tracing, and profiling system for the PAD framework. It provides comprehensive tracking of PAD execution flow, performance statistics, XML output, cross-references, and detailed trace information.

## Purpose in PAD Framework

This module serves as PAD's observability layer, allowing developers to understand how templates are processed, track performance, debug issues, and analyze the execution flow of PAD applications. It can be enabled/disabled via configuration flags and outputs detailed information about PAD's internal operations.

## Files and Subdirectories

### Main Configuration Files

#### start/
- **config.php** - Initializes info tracking system
  - Checks if `$padInfo` is enabled
  - Increments info counter
  - Loads library functions
  - Starts all enabled info subsystems (track, stats, xref, trace, xml)
  - Sets `$padInfoStarted` flag

- **tag.php** - Tag-level info initialization

#### end/
- **config.php** - Finalizes info tracking
  - Stops all active info subsystems in reverse order
  - Decrements info counter

- **tag.php** - Tag-level info finalization

### Subdirectories

#### _lib/
- **_lib.php** - Core library functions for info system
  - `padInfoPadOccur()` - Gets pad occurrence identifier
  - `padInfoGet()` - Reads info files safely
  - `padInfoDelete()` - Recursively deletes info directories
  - `padInfoSet()` - (referenced but not shown) Sets up info context

#### types/
Contains different info tracking subsystems:

**trace/** - Execution trace tracking
- **start.php** - Begin trace collection
- **end.php** - Finalize trace output
- **level/** - Level-specific trace handling
- **occur/** - Occurrence-specific trace handling
- **_lib.php** - Trace library functions (10KB+ of trace logic)

**stats/** - Performance statistics
- **start.php** - Begin stats collection
- **end.php** - Output performance statistics
  - Tracks timing, memory usage, execution counts

**track/** - Execution tracking
- **start.php** - Begin execution tracking
- **end.php** - Finalize tracking data

**xref/** - Cross-reference tracking
- **start.php** - Begin cross-reference collection
- **end.php** - Output cross-reference data
  - Tracks relationships between tags, includes, and variables

**xml/** - XML output generation
- **start.php** - Begin XML collection
- **end.php** - Generate XML output
  - Produces structured XML representation of execution

## Key Features

### Multiple Tracking Modes
The info system supports five independent tracking subsystems:
- **Track** (`$padInfoTrack`) - Basic execution tracking
- **Stats** (`$padInfoStats`) - Performance statistics
- **Xref** (`$padInfoXref`) - Cross-reference analysis
- **Trace** (`$padInfoTrace`) - Detailed execution trace
- **XML** (`$padInfoXml`) - XML output generation

### Conditional Activation
All info tracking is gated by `$padInfo` flag, ensuring zero overhead when disabled.

### Occurrence Tracking
The `padInfoPadOccur()` function tracks multiple occurrences of the same tag, adding occurrence numbers (e.g., `user/2` for second user tag).

### Nested Execution Support
The `$padInfoCnt` counter tracks nesting depth, allowing proper handling of recursive or nested PAD processing.

## How This Module Fits Into PAD Architecture

### Execution Flow
```
PAD Processing Begins
    |
    v
info/start/config.php
    |
    +-- Initialize subsystems
    +-- Start tracking (if enabled)
    |
    v
[PAD Tag/Operation Processing]
    |
    +-- events/handling.php (logs events)
    +-- Trace points throughout framework
    |
    v
info/end/config.php
    |
    +-- Stop tracking
    +-- Output results
    +-- Cleanup
```

### Integration Points
- **Initialized by**: PAD core at start of processing
- **Used by**: All PAD components can emit info events
- **Outputs to**: Files in info output directory, or inline in response
- **Controlled by**: Global configuration variables (`$padInfo*`)

### Event System
Throughout PAD execution, various components include info event files:
```php
if ($padInfo)
    include PAD . 'events/handling.php';
```

These events feed data to active info subsystems.

### Design Patterns
- **Observer Pattern**: Info system observes PAD execution without affecting it
- **Feature Flags**: Each subsystem independently enabled/disabled
- **Lazy Initialization**: Only loads subsystems that are enabled
- **Separation of Concerns**: Info tracking separated from core functionality
- **Cleanup**: Proper start/end lifecycle with cleanup

### Use Cases
1. **Debugging**: Use trace mode to see exact execution flow
2. **Performance**: Use stats mode to identify bottlenecks
3. **Documentation**: Use xref mode to map template dependencies
4. **Integration**: Use XML mode for machine-readable output
5. **Development**: Use track mode for general development visibility

This module is essential for PAD development and debugging, providing deep visibility into the framework's operation without impacting production performance when disabled.
