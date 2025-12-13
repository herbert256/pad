# Event Handling System

The events module is PAD's hook and lifecycle management system, providing extension points throughout the template processing pipeline for monitoring, modification, and integration.

## Overview

This module implements a comprehensive event system that:
- Provides hooks at every stage of template processing
- Enables debugging, tracing, and profiling
- Supports conditional logic and flow control
- Manages data transformation and caching
- Facilitates cross-reference tracking and statistics

Events fire at specific points during PAD execution, allowing external code to observe or modify the processing pipeline without altering core framework code.

## Directory Structure

### Lifecycle Events
Core template processing lifecycle:
- **start.php** - Template processing start
- **end.php** - Template processing end
- **parse.php** - During template parsing
- **walk.php** - During template traversal
- **build.php** - During output building

### Variable/Tag Events
Template variable and tag processing:
- **var_start.php** - Variable processing start
- **var_end.php** - Variable processing end
- **tag.php** - Tag processing
- **page.php** - Page tag processing

### Field Events
Database/data field handling:
- **fieldStart.php** - Field processing start
- **fieldEnd.php** - Field processing end
- **fieldClassic.php** - Classic field syntax
- **fieldAt.php** - Field @ syntax

### Data Events
Data access and manipulation:
- **data.php** - Data access
- **get.php** - Get operations
- **put.php** - Put operations
- **store.php** - Store operations
- **content.php** - Content handling

### Control Flow Events
Conditional and flow control:
- **true.php** - True condition handling
- **false.php** - False condition handling
- **else.php** - Else condition handling
- **optional.php** - Optional content handling

### Level/Occurrence Events
Hierarchical data and looping:
- **levelStart.php** - Level processing start
- **levelEnd.php** - Level processing end
- **occurStart.php** - Occurrence/loop start
- **occurEnd.php** - Occurrence/loop end
- **resultOcc.php** - Result occurrence handling

### Function/Call Events
Function call tracking:
- **call.php** - Function call event
- **callStart.php** - Function call start
- **callEnd.php** - Function call end
- **functions.php** - Function tracking
- **functionsFast.php** - Fast function tracking

### Evaluation Events (`eval/` subdirectory)
Expression evaluation hooks:
- **parse.php** - Expression parsing
- **start.php** - Evaluation start
- **end.php** - Evaluation end
- **go.php** - Evaluation execution
- **after.php** - Post-evaluation processing
- **error.php** - Evaluation errors
- **fast.php** - Fast evaluation tracking

### Type/Property Events
Type and property handling:
- **type.php** - Type processing
- **atTypes.php** - Type @ syntax
- **atProperties.php** - Property @ syntax
- **atGroups.php** - Group @ syntax

### Specialized Events
- **sequence.php** - Sequence processing
- **go.php** - General go/execution events
- **handling.php** - Special handling events
- **flags.php** - Flag processing
- **parms.php** - Parameter processing
- **option.php** - Single option processing
- **options.php** - Multiple options processing
- **setup.php** - Setup events
- **return.php** - Return value handling
- **base.php** - Base template handling

### Integration Events
External system integration:
- **cache.php** - Cache operations
- **sql.php** - SQL query events
- **curl.php** - HTTP/cURL events
- **stats.php** - Statistics tracking

## Key Features

### Event Hook Pattern
Each event file typically follows this pattern:
```php
if ( $GLOBALS['padInfo'] )
  if ( $GLOBALS['padInfoTrace'] )
    if ( $GLOBALS['padInfoTraceSpecific'] )
      padInfoTrace('category', 'type', $data);
```

This allows fine-grained control over:
- Whether info tracking is enabled globally
- Whether tracing is enabled
- Which specific traces to capture

### Conditional Tracing
Events check multiple conditions before firing:
- `$GLOBALS['padInfo']` - Master info switch
- `$GLOBALS['padInfoTrace']` - Trace enabled
- Specific flags like:
  - `$padInfoTraceCall` - Function calls
  - `$padInfoTraceEval` - Expression evaluation
  - `$padInfoTraceParse` - Template parsing
  - `$padInfoTraceLevelBase` - Level base tracking
  - `$padInfoTraceContent` - Content tracking
  - `$padInfoTraceDouble` - Double operation tracking

### Cross-Reference Tracking
Many events support cross-reference tracking via:
- `padInfoXref()` - Records what code uses what resources
- Tracks functions, fields, tags, types, etc.
- Useful for dependency analysis and optimization

### Statistics Collection
Events can collect performance and usage statistics:
- Processing times
- Resource usage
- Call counts
- Data access patterns

## Common Event Patterns

### Information Gathering
Events like **call.php** and **functions.php** gather metadata about what's being executed without modifying behavior.

### Data Capture
Events like **parse.php** and **eval/parse.php** capture intermediate data structures for debugging and analysis.

### Conditional Execution
Events respect configuration flags to minimize performance impact when tracing is disabled.

### Context Preservation
Events have access to the full execution context through global variables and function parameters.

## Integration with PAD Framework

The events system integrates deeply with PAD:

### Template Processing Pipeline
Events fire throughout the template lifecycle:
1. start → parse → walk → build → end
2. Variable processing: var_start → tag → var_end
3. Field access: fieldStart → field processing → fieldEnd
4. Levels/loops: levelStart/occurStart → data → occurEnd/levelEnd

### Expression Evaluation
Expression evaluation triggers multiple events:
1. eval/start → eval/parse → eval/go → eval/after → eval/end
2. On error: eval/error

### Function Execution
Function calls trigger tracking events:
1. callStart → call → callEnd
2. functions (cross-reference)
3. functionsFast (for optimized calls)

### Data Flow
Data operations trigger events:
1. get/put/store events for data access
2. content events for content manipulation
3. cache events for caching operations

### Conditional Logic
Control flow events enable template conditionals:
- true/false events for boolean evaluation
- else events for alternative branches
- optional events for optional content

## Performance Considerations

The event system is designed for minimal overhead when disabled:
- Multiple guard clauses prevent unnecessary execution
- Events are no-ops when tracing is disabled
- Fast-path optimization bypasses events when possible
- Conditional compilation through configuration flags

## Debugging and Development

The events system is invaluable for:
- **Debugging**: Trace execution flow and data transformations
- **Profiling**: Measure performance at each stage
- **Analysis**: Build cross-reference maps and dependency graphs
- **Testing**: Verify correct execution sequence
- **Optimization**: Identify bottlenecks and redundant operations

## Event Data Structure

Events typically trace:
- **Category**: The type of event (parse, call, level, etc.)
- **Type**: Subtype or detail (info, start, end, etc.)
- **Data**: Relevant data (variables, expressions, values, etc.)

This structured approach makes traces easier to analyze and filter.
