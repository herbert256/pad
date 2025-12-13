# Expression Parser and Evaluator

The eval module is PAD's expression parser and evaluator, responsible for parsing and executing PAD's custom expression language embedded within templates and data definitions.

## Overview

This module implements a complete expression evaluation system that:
- Parses PAD expressions into executable components
- Evaluates expressions with support for variables, fields, arrays, and operators
- Provides fast-path optimization for common function calls
- Supports both single-value and parameter-based operations
- Integrates with PAD's data model (fields, tags, properties, etc.)

## Directory Structure

### Main Files
- **eval.php** - Main evaluation entry point with parsing and pipe processing
- **fast.php** - Fast-path evaluation for pre-defined functions

### Actions (`actions/` subdirectory)
Expression action handlers that process parsed expressions:
- **alone.php** - Handles standalone expressions
- **single.php** - Handles single operand operations
- **singleRight.php** - Handles right-side single operations
- **double.php** - Handles binary operations (two operands)
- **doubleLeft.php** - Handles left-side of binary operations
- **doubleRight.php** - Handles right-side of binary operations

### Go/Execute (`go/` subdirectory)
Execution handlers for different value type combinations:
- **go.php** - Main execution dispatcher
- **singleVar.php** - Single variable/value operations
- **singleArr.php** - Single array operations
- **doubleVarVar.php** - Binary operations with two variables
- **doubleArrVar.php** - Binary operations with array and variable
- **doubleVarArr.php** - Binary operations with variable and array
- **doubleArrArr.php** - Binary operations with two arrays

### Parameters (`parms/` subdirectory)
Parameter handling for function-like expressions:
- **parm.php** - Individual parameter handling
- **pad.php** - PAD-specific parameter evaluation
- **php.php** - PHP function parameter handling
- **app.php** - Application parameter handling
- **tag.php** - Tag parameter handling
- **sequence.php** - Sequence parameter handling

### Single Values (`single/` subdirectory)
Single value type resolvers:
- **field.php** - Field value retrieval
- **tag.php** - Tag value retrieval
- **property.php** - Property value retrieval
- **parm.php** - Parameter value retrieval
- **data.php** - Data value retrieval
- **content.php** - Content value retrieval
- **array.php** - Array literal handling
- **constant.php** - Constant value retrieval
- **flag.php** - Flag value retrieval
- **level.php** - Level value retrieval
- **local.php** - Local variable handling
- **object.php** - Object value retrieval
- **pull.php** - Pull data retrieval
- **include.php** - Include file handling

### Type Handlers (`type/` subdirectory)
Type resolution and dispatch:
- **type.php** - Main type dispatcher
- **single.php** - Single value type handler
- **parms.php** - Parameter-based type handler

## Key Features

### Expression Parsing
- Parses PAD expressions into structured arrays
- Identifies operators, operands, and function calls
- Supports nested expressions and operator precedence
- Handles pipes for chaining operations

### Fast Path Optimization
When a function exists in `/functions/` directory:
- Bypasses full parsing
- Directly includes and executes the function
- Significantly faster for common operations

### Operator Support
Binary operators (from doubleVarVar.php):
- Comparison: `<` (LT), `<=` (LE), `==` (EQ), `>=` (GE), `>` (GT), `<>` (NE)
- Logical: AND, OR, XOR
- Arithmetic: `+`, `-`, `*`, `/`, `%`
- String: `.` (concatenation)
- Automatic type coercion (int/float)

Unary operators:
- Negation: `!` (NOT operation)

### Data Access
Expressions can access:
- **Fields**: Database/data fields
- **Tags**: Template tags and their values
- **Properties**: Object properties
- **Parameters**: Function parameters
- **Arrays**: Array literals and indexed access
- **Content**: Template content
- **Data**: Global data values
- **Flags**: Boolean flags
- **Levels**: Hierarchical level data

## Evaluation Flow

### Standard Evaluation (eval.php)
1. Check if fast-path function exists
2. Parse expression into structured result array
3. Apply post-parsing transformations (padEvalAfter)
4. Split into pipe segments
5. Evaluate each pipe segment sequentially
6. Return final value

### Fast Evaluation (fast.php)
1. Include pre-defined function file
2. Execute function directly
3. Track function call in info system if enabled
4. Return result immediately

### Type Resolution (type/type.php)
1. Identify value kind (field, tag, property, etc.)
2. Check for single-value handler
3. Execute single-value or parameter-based evaluation
4. Mark result as VALUE in result array
5. Continue operator processing

### Operation Execution (go/go.php)
1. Determine operation type (single or double)
2. Check operand types (variable or array)
3. Dispatch to appropriate handler
4. Execute operation
5. Update result array
6. Process remaining operators

## Integration with PAD Framework

The eval module is central to PAD's template processing:
- **Template Expressions**: Evaluates `{expression}` syntax in templates
- **Conditional Logic**: Supports if/else/true/false event handling
- **Data Binding**: Connects template expressions to data sources
- **Function Calls**: Executes PAD, PHP, and application functions
- **Event System**: Integrates with events for trace/debug information
- **Pipe Operations**: Supports data transformation pipelines

## Performance Optimization

The module includes several optimization strategies:
- **Fast Path**: Pre-compiled functions skip parsing
- **Type Dispatch**: Direct routing to type-specific handlers
- **Array Caching**: `$GLOBALS['_eval']` and `$GLOBALS['_eval_last']` cache
- **Minimal Parsing**: Only parses when necessary
- **Inline Includes**: Direct file inclusion for minimal overhead

## Tracing and Debugging

When `$GLOBALS['padInfo']` is enabled:
- Traces parse results
- Tracks type resolution
- Logs operator processing
- Records function calls
- Captures evaluation errors
