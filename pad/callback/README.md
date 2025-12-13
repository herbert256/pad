# Callback Module

This module provides a lightweight wrapper system for executing callback functions at specific points in PAD's execution lifecycle. It enables developers to hook into the framework's processing flow through standardized callback points.

## Overview

The callback module implements a simple but powerful callback system that:
- Provides standardized callback invocation points
- Executes user-defined callback functions at key lifecycle stages
- Integrates with PAD's options system
- Supports multiple callback types (init, row, exit)
- Enables custom processing and data transformation

## Directory Structure

### Files
- **callback.php** - Core callback dispatcher that executes the callback
- **init.php** - Initialization callback wrapper
- **row.php** - Row/iteration callback wrapper
- **exit.php** - Exit/cleanup callback wrapper

## Key Components

### callback.php
Main callback execution dispatcher.

**Process:**
1. Sets `$padCallback` variable to specify callback type
2. Includes `options/go/callback.php` which handles actual callback execution

**Global Variable Used:**
- `$padCallback` - Type of callback to execute ('init', 'row', or 'exit')

### init.php
Initialization callback invocation point.

**Usage:**
```php
include PAD . 'callback/init.php';
```

**Process:**
1. Sets `$padCallback = "init"`
2. Includes `callback.php` to execute the callback

**Purpose:** Called before processing begins, useful for:
- Setting up data structures
- Initializing variables
- Preparing resources
- Loading configuration

### row.php
Row/iteration callback invocation point.

**Usage:**
```php
include PAD . 'callback/row.php';
```

**Process:**
1. Sets `$padCallback = "row"`
2. Includes `callback.php` to execute the callback

**Purpose:** Called for each row/iteration, useful for:
- Transforming row data
- Filtering items
- Calculating derived values
- Logging/tracking iterations

### exit.php
Exit/cleanup callback invocation point.

**Usage:**
```php
include PAD . 'callback/exit.php';
```

**Process:**
1. Sets `$padCallback = "exit"`
2. Includes `callback.php` to execute the callback

**Purpose:** Called after processing completes, useful for:
- Cleanup operations
- Finalizing data
- Releasing resources
- Post-processing aggregations

## Callback Lifecycle

```
┌─────────────────────────────────┐
│  Tag/Loop Processing Begins     │
└───────────────┬─────────────────┘
                │
                ▼
        ┌───────────────┐
        │ callback/init │
        │  Initialize   │
        └───────┬───────┘
                │
                ▼
        ┌───────────────┐
    ┌──▶│ callback/row  │
    │   │  Process Row  │
    │   └───────┬───────┘
    │           │
    │   ┌───────▼────────┐
    └───┤  More Rows?    │
        └───────┬────────┘
                │ No
                ▼
        ┌───────────────┐
        │ callback/exit │
        │   Cleanup     │
        └───────┬───────┘
                │
                ▼
┌───────────────────────────────────┐
│  Tag/Loop Processing Complete     │
└───────────────────────────────────┘
```

## Integration with Options System

Callbacks are defined through PAD's options system. The actual callback function is specified in tag options and executed by `options/go/callback.php`.

### Example Tag Usage
```pad
{padLoop for="products" callback="processProduct"}
  {name} - {price}
{/padLoop}
```

In this example:
- `callback="processProduct"` specifies the callback function
- `processProduct` function is called for each row (via row.php)
- Function can modify data before display

## Callback Function Patterns

### Init Callback
```php
function myInitCallback($data) {
  // Called once before processing
  // Can modify initial data structure
  // Can set up variables in global scope
  return $data; // Return modified data
}
```

### Row Callback
```php
function myRowCallback($row, $index) {
  // Called for each row/item
  // Can transform row data
  // Can filter items (return FALSE to skip)
  $row['processed'] = true;
  $row['displayName'] = strtoupper($row['name']);
  return $row; // Return modified row
}
```

### Exit Callback
```php
function myExitCallback($result) {
  // Called once after all processing
  // Can finalize results
  // Can perform cleanup
  return $result; // Return final result
}
```

## Usage Examples

### Example 1: Data Transformation
```php
// In tag options or configuration
$options['callback'] = 'transformProducts';

// Callback function
function transformProducts($row) {
  $row['priceFormatted'] = '$' . number_format($row['price'], 2);
  $row['inStock'] = $row['quantity'] > 0;
  return $row;
}

// PAD template
{padLoop for="products" callback="transformProducts"}
  {name}: {priceFormatted} - {inStock}
{/padLoop}
```

### Example 2: Filtering
```php
function onlyActiveProducts($row) {
  if (!$row['active']) {
    return FALSE; // Skip this row
  }
  return $row; // Include this row
}

{padLoop for="products" callback="onlyActiveProducts"}
  {name}
{/padLoop}
```

### Example 3: Aggregation
```php
// Init callback - set up accumulator
function initTotal($data) {
  global $total;
  $total = 0;
  return $data;
}

// Row callback - accumulate values
function addToTotal($row) {
  global $total;
  $total += $row['price'];
  return $row;
}

// Exit callback - finalize result
function finalizeTotal($result) {
  global $total;
  // Store total somewhere or log it
  return $result;
}
```

### Example 4: Logging/Debugging
```php
function logRow($row) {
  error_log("Processing: " . $row['id']);
  return $row; // Don't modify data
}

{padLoop for="items" callback="logRow"}
  {name}
{/padLoop}
```

## Integration with PAD Framework

The callback module integrates with several PAD components:

### Tag System
- Tags can specify callback functions via options
- Callbacks execute during tag processing
- Multiple tags can use same or different callbacks

### Data System
- Callbacks can transform data from `padData()`
- Can filter, sort, or modify data arrays
- Can add computed fields

### Options System
- Callback names specified in tag options
- `options/go/callback.php` handles execution
- Options can pass parameters to callbacks

### Loop System
- Row callbacks execute for each iteration
- Init callbacks before loop starts
- Exit callbacks after loop completes

## Callback Types in Detail

### Init Callbacks
**When Called:** Before any rows are processed

**Common Uses:**
- Initialize counters or accumulators
- Set up global state
- Prepare data structures
- Load additional resources
- Set configuration values

**Receives:** Initial data set or configuration

**Returns:** Modified data set or configuration

### Row Callbacks
**When Called:** For each row/item in a loop or data set

**Common Uses:**
- Transform field values
- Add computed fields
- Filter out unwanted items
- Validate data
- Format output
- Track iteration state

**Receives:** Current row data and possibly index

**Returns:**
- Modified row data (to include)
- FALSE (to skip this row)
- NULL (to skip without filtering)

### Exit Callbacks
**When Called:** After all rows have been processed

**Common Uses:**
- Finalize aggregations
- Clean up resources
- Log summary information
- Post-process results
- Save state
- Generate reports

**Receives:** Final result set or summary data

**Returns:** Final processed result

## Performance Considerations

- Callbacks add minimal overhead (single function call per invocation)
- No overhead if no callback is specified
- Row callbacks are called for every iteration (can impact performance on large datasets)
- Init and exit callbacks called only once per tag
- Callback functions should be efficient as they're in the critical path

## Best Practices

1. **Keep callbacks focused**: Each callback should do one thing well
2. **Avoid heavy processing in row callbacks**: They run on every iteration
3. **Use init callbacks for setup**: Don't repeat initialization in row callbacks
4. **Return values explicitly**: Always return data from callbacks
5. **Handle edge cases**: Check for missing fields, null values, etc.
6. **Document callback requirements**: Note what data structure is expected
7. **Use consistent naming**: Prefix callback functions clearly (e.g., "callback_")
8. **Consider performance**: Profile callbacks on large datasets
9. **Avoid side effects when possible**: Keep callbacks pure functions
10. **Test callbacks independently**: Unit test callback functions separately

## Error Handling

Callbacks should handle their own errors gracefully:

```php
function safeCallback($row) {
  try {
    // Process data
    $result = processRow($row);
    return $result;
  } catch (Exception $e) {
    // Log error
    error_log("Callback error: " . $e->getMessage());
    // Return original row or default
    return $row;
  }
}
```

## Debugging Callbacks

To debug callback execution:

```php
function debugCallback($row) {
  // Log incoming data
  error_log("Row data: " . print_r($row, true));

  // Process
  $processed = doSomething($row);

  // Log outgoing data
  error_log("Processed: " . print_r($processed, true));

  return $processed;
}
```

## Advanced Patterns

### Stateful Callbacks
```php
// Use static variables to maintain state
function statefulCallback($row) {
  static $counter = 0;
  $row['index'] = ++$counter;
  return $row;
}
```

### Conditional Callbacks
```php
function conditionalCallback($row) {
  if ($row['type'] === 'special') {
    return specialProcessing($row);
  }
  return normalProcessing($row);
}
```

### Chained Callbacks
```php
function chainedCallback($row) {
  $row = validateRow($row);
  $row = transformRow($row);
  $row = enrichRow($row);
  return $row;
}
```
