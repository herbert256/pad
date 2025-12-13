# PAD Handling Directory

## Overview

The `handling/` directory implements the option handling system for PAD data arrays. It processes special options (like sorting, pagination, deduplication) that can be applied to data sets within PAD tags.

## Purpose in PAD Framework

This module provides post-processing capabilities for data retrieved in PAD tags. After data is fetched, handling options allow it to be sorted, paginated, filtered, shuffled, or otherwise transformed before being rendered in the template.

## Files and Subdirectories

### Main File
- **handling.php** - Main handler dispatcher
  - Iterates through tag parameters looking for option-type parameters
  - Loads appropriate handler from `types/` directory
  - Supports negative handling mode for certain operations
  - Tracks handling events when `$padInfo` is enabled

### Subdirectories

#### types/
Contains individual handler implementations for different data operations:

**Array Modification**
- **dedup.php** - Remove duplicate entries from single-field arrays
- **reverse.php** - Reverse array order
- **shuffle.php** - Randomize array order
- **random.php** - Select random elements
- **trim.php** - Trim array elements

**Sorting & Organization**
- **sort.php** - Multi-field sorting with ASC/DESC and sort flags
  - Supports field-specific sort directions
  - Handles multiple sort keys
  - Flexible sort type flags (NUMERIC, STRING, etc.)

**Pagination & Slicing**
- **first.php** - Get first N elements
- **last.php** - Get last N elements
- **start.php** - Start from specific position
- **end.php** - End at specific position
- **slice.php** - Extract array slice
- **splice.php** - Remove/replace array portion
- **row.php** - Get specific row
- **rows.php** - Get specific number of rows
- **page.php** - Paginate results (with page number and rows per page)

#### negative/
Handles negative/inverse operations:
- **inits.php** - Initialize negative handling mode
- **exits.php** - Finalize negative handling mode

## Key Features

### Dynamic Handler Loading
Handlers are loaded dynamically based on option parameters:
```php
if (file_exists(PAD . "handling/types/$padPrmName.php"))
    include PAD . "handling/types/$padPrmName.php";
```

### Option Processing Flow
1. Check if data array has elements
2. Check if parameter is an option type (not sequential)
3. Verify handler file exists
4. Extract handler parameters
5. Execute handler in normal or negative mode
6. Track handling event if info mode enabled

### Pagination System
The `page.php` handler provides full pagination support:
- Page number parameter
- Rows per page parameter
- Automatic start/end calculation
- Integration with data slicing

### Multi-field Sorting
The `sort.php` handler supports complex sorting:
- Multiple sort fields separated by semicolons
- Per-field sort direction (ASC/DESC)
- Sort type flags (NUMERIC, STRING, NATURAL, etc.)
- Uses `array_multisort` for efficient multi-column sorting

## How This Module Fits Into PAD Architecture

### Execution Flow
```
PAD Tag with Options
    |
    v
handling.php (dispatcher)
    |
    +-- Iterate tag parameters
    |
    +-- For each option parameter:
        |
        +-- Load handler from types/
        |
        +-- Apply transformation to $padData[$pad]
        |
        +-- Track handling event (if enabled)
    |
    v
Transformed Data Array
    |
    v
Continue Tag Processing
```

### Integration Points
- **Called by**: PAD tag processing after data retrieval
- **Operates on**: `$padData[$pad]` array
- **Uses**: `$padPrm[$pad]` for option parameters
- **Events**: Triggers `events/handling.php` when `$padInfo` enabled
- **Modifies**: In-place modification of data arrays

### Design Principles
- **Modular**: Each handler is a separate, focused operation
- **Composable**: Multiple handlers can be applied in sequence
- **Non-destructive**: Original data preserved until handler execution
- **Convention-based**: Handler names match option parameter names
- **Performance-aware**: Only loads handlers actually used

### Usage Example
When a PAD tag includes options like:
```
<pad:users sort="name ASC" page="2" rows="10" dedup>
```

The handling system will:
1. Apply deduplication
2. Sort by name ascending
3. Paginate to page 2 with 10 rows per page

This module is crucial for making PAD data flexible and powerful, allowing complex data transformations with simple declarative options.
