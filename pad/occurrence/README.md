# Template Occurrence Tracking

This module handles the tracking and processing of template occurrences (iterations) within the PAD framework. When templates loop through data, this module manages each occurrence's state, variables, and output.

## Overview

The occurrence module is responsible for managing individual iterations when PAD templates process arrays or datasets. Each occurrence represents one iteration through a data element, maintaining proper state and variable scoping for that iteration. This is a core component of PAD's template iteration system.

## Files

- **occurrence.php** - Main entry point that orchestrates the occurrence processing flow
- **init.php** - Initializes occurrence state including counters, variables, and data pointers
- **set.php** - Sets up global variables and data for the current occurrence
- **table.php** - Manages table relationships and data structures for the current occurrence
- **end.php** - Finalizes the occurrence, appending output and resetting state

## Key Features

### Occurrence State Management
- Tracks occurrence counter (`$padOccur`) for each template tag
- Maintains current data element and key position
- Stores occurrence-specific variables and output

### Variable Scoping
- Sets global variables for the current occurrence based on:
  - Tag parameters
  - Table data structures
  - Current data element
  - Occurrence-specific overrides (`$padSetOcc`)
- Evaluates dynamic expressions within occurrence context

### Data Flow
- Captures current data element from `$padData`
- Updates walk data array for iteration tracking
- Manages output accumulation through `$padResult`

### Table Relations
- Processes table relationships and virtual tables
- Calls `padTableGetInfo()` to populate related data structures
- Handles table tag associations

### Event Integration
- Triggers `occurStart` event at occurrence beginning (if `$padInfo` enabled)
- Triggers `occurEnd` event at occurrence completion
- Supports callback processing for row-level operations

## Integration with PAD Framework

The occurrence module is central to PAD's template iteration capabilities:

1. **Template Processing**: Called by loop/iteration tags to process each data element
2. **Variable System**: Works with PAD's global variable system to provide scoped data access
3. **Callback System**: Integrates with callbacks for custom row processing
4. **Event System**: Fires occurrence-level events for debugging and monitoring
5. **Table System**: Coordinates with table/relation system for joined data

## Typical Flow

1. **Init** - Increment occurrence counter, set up data pointers and keys
2. **Table** - Load related table data and virtual tables
3. **Set** - Assign variables to global scope based on occurrence data
4. **Process** - Template content processes with occurrence variables
5. **End** - Append output to result and reset occurrence state

This module ensures that each iteration through template data maintains proper isolation while providing access to the correct data context.
