# PAD Evaluation Subsystem

This document explains in detail how the PAD expression evaluation subsystem works.

## Overview

The eval subsystem is responsible for parsing and evaluating expressions in PAD templates. When you write `{$variable + 5 | trim}` in a PAD template, this subsystem handles the parsing, variable resolution, operator execution, and pipe processing.

## Entry Point

The main entry point is `eval.php`:

```php
// Fast path: if expression is a simple function name, use optimized path
if ( file_exists ( PAD . "functions/$eval.php" ) )
  return include PAD . 'eval/fast.php';

// Full evaluation pipeline
padEvalParse ( $result, $eval );   // Step 1: Parse into tokens
padEvalAfter ( $result );          // Step 2: Resolve types and operators
padEvalPipes ( $result, $pipes );  // Step 3: Split on pipe operators

foreach ( $pipes as $one )
  $value = padEvalResult ( $one, $value, $eval );  // Step 4: Evaluate each pipe segment

return $value;
```

## Evaluation Pipeline

### Step 1: Parsing (`lib/eval/parse.php`)

The `padEvalParse()` function tokenizes the expression into an array of tokens. Each token is an array with:
- `[0]` = The value/name
- `[1]` = The type (VAL, OPR, $, &, #, TYPE, pipe, open, close, etc.)
- `[2]` = Additional type info (for TYPE tokens)
- `[3]` = Parameter end position (for function calls)

**Token Types:**

| Type | Meaning | Example |
|------|---------|---------|
| `VAL` | Literal value | `'hello'`, `123`, `3.14` |
| `OPR` | Operator | `+`, `-`, `*`, `LT`, `AND` |
| `$` | Variable reference | `$name` |
| `&` | Tag reference | `&tagname` |
| `#` | Option/parameter reference | `#option` |
| `$$` | Property reference | `@property` or `$$prop` |
| `TYPE` | Typed value accessor | `field:name`, `data:key` |
| `pipe` | Pipe separator | `\|` |
| `open` | Open parenthesis | `(` |
| `close` | Close parenthesis | `)` |
| `a-open` | Array open bracket | `[` |
| `a-close` | Array close bracket | `]` |
| `hex` | Hexadecimal value | `0xFF` |
| `other` | Unrecognized (becomes constant or type) | `TRUE`, `fieldname` |

**Parsing Features:**

- String literals with single (`'`) or double (`"`) quotes
- Escape sequences: `\n`, `\r`, `\t`, `\\`, `\'`, `\"`
- Hexadecimal numbers: `0xFF`
- Scientific notation: `1.5E-10`
- Negative numbers: `-42`
- Two-character operators: `**`, `<=`, `>=`, `==`, `<>`

### Step 2: Type Resolution (`lib/eval/after.php`)

The `padEvalAfter()` function processes the parsed tokens:

1. **Resolve typed references** - Tokens like `field:name` become TYPE tokens
2. **Resolve operators** - Text operators (`LT`, `AND`) and alternates (`<` → `LT`)
3. **Resolve variables** - `$var` tokens get their values from `padFieldValue()`
4. **Resolve tags** - `&tag` tokens get values from `padTagValue()`
5. **Resolve options** - `#opt` tokens get values from `padOptValue()`
6. **Resolve hex** - `0xFF` converts to binary

**Operator Alternates:**

| Symbol | Becomes |
|--------|---------|
| `<` | `LT` |
| `<=` | `LE` |
| `>` | `GT` |
| `>=` | `GE` |
| `=` or `==` | `EQ` |
| `<>` | `NE` |

### Step 3: Pipe Splitting (`lib/eval/pipes.php`)

The `padEvalPipes()` function splits the token stream at pipe (`|`) operators:

```
$name | trim | upper
```

Becomes three segments that are evaluated left-to-right, with each result passed to the next.

### Step 4: Expression Evaluation (`lib/eval/result.php`)

The `padEvalResult()` function evaluates each pipe segment:

```php
padEvalValue  ( $result, $value );  // Inject pipe input value
padEvalArray  ( $result, $value );  // Handle array access [index]
padEvalOpnCls ( $result, $value );  // Handle parentheses
padEvalOpr    ( $result, $value );  // Execute operators
padEvalMulti  ( $result );          // Handle multiple expressions
```

## Operator Execution

### Operator Precedence (`lib/eval/const.php`)

Operators are evaluated in this order (highest to lowest):

```php
const padEval_precedence = [
  '!',                              // NOT (unary)
  '**', '*', '/', '%', '+', '-',    // Arithmetic
  '.',                              // String concatenation
  'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', // Comparison
  'AND', 'XOR', 'OR',               // Logical
  'NOT',                            // NOT (word form)
];
```

### Operator Processing (`lib/eval/operations.php`)

The `padEvalOpr()` function:

1. Scans tokens for operators in precedence order
2. Finds operands (left and/or right values)
3. Dispatches to appropriate action handler

**Action Types:**

| Action | File | Description |
|--------|------|-------------|
| `single` | `actions/single.php` | Unary operator with right operand |
| `singleRight` | `actions/singleRight.php` | Unary with operator to the right |
| `double` | `actions/double.php` | Binary operator with both operands |
| `doubleLeft` | `actions/doubleLeft.php` | Binary with missing left operand |
| `doubleRight` | `actions/doubleRight.php` | Binary with missing right operand |
| `alone` | `actions/alone.php` | Operator with implicit operands |

### Operator Execution (`go/go.php`)

Based on operand types, dispatches to:

| Left | Right | Handler |
|------|-------|---------|
| scalar | scalar | `go/doubleVarVar.php` |
| array | scalar | `go/doubleArrVar.php` |
| scalar | array | `go/doubleVarArr.php` |
| array | array | `go/doubleArrArr.php` |
| - | scalar | `go/singleVar.php` |
| - | array | `go/singleArr.php` |

### Binary Operations (`go/doubleVarVar.php`)

```php
// Comparison operators - return '1' or ''
if ( $opr == 'LT' )  $now = ($left <  $right) ? 1 : '';
if ( $opr == 'LE' )  $now = ($left <= $right) ? 1 : '';
if ( $opr == 'EQ' )  $now = ($left == $right) ? 1 : '';
if ( $opr == 'GE' )  $now = ($left >= $right) ? 1 : '';
if ( $opr == 'GT' )  $now = ($left >  $right) ? 1 : '';
if ( $opr == 'NE' )  $now = ($left <> $right) ? 1 : '';

// Logical operators
if ( $opr == 'AND' ) $now = ($left AND $right) ? 1 : '';
if ( $opr == 'OR' )  $now = ($left OR  $right) ? 1 : '';
if ( $opr == 'XOR' ) $now = ($left XOR $right) ? 1 : '';

// String concatenation
if ( $opr == '.' )   $now = $left . $right;

// Arithmetic (with automatic int/float detection)
if ( $opr == '+' )   $now = $left + $right;
if ( $opr == '-' )   $now = $left - $right;
if ( $opr == '*' )   $now = $left * $right;
if ( $opr == '/' )   $now = $left / $right;
if ( $opr == '%' )   $now = $left % $right;
```

### Unary Operations (`go/singleVar.php`)

```php
// NOT operator - inverts truthiness
$now = ( $right ) ? '' : '1';
```

## Type Handlers

### Type Resolution (`type/type.php`)

When a TYPE token is encountered:

```php
$kind = $result[$k][2];  // e.g., 'field', 'data', 'property'
$name = $result[$k][0];  // e.g., 'username'

if ( file_exists ( PAD . "eval/single/$kind.php" ) )
  $value = include PAD . 'eval/type/single.php';  // Simple type
else
  $value = include PAD . 'eval/type/parms.php';   // Type with parameters
```

### Simple Types (`single/`)

| Type | File | Returns |
|------|------|---------|
| `field` | `single/field.php` | `padFieldValue($name)` |
| `data` | `single/data.php` | `$GLOBALS['padDataStore'][$name]` |
| `content` | `single/content.php` | `$GLOBALS['padContentStore'][$name]` |
| `property` | `single/property.php` | `padTagValue($name)` |
| `tag` | `single/tag.php` | `padOptValue($name, 1)` |
| `array` | `single/array.php` | `padArrayValue($name, TRUE)` |
| `constant` | `single/constant.php` | `constant($name)` |
| `level` | `single/level.php` | `padGetLevelArray($name)` |
| `flag` | `single/flag.php` | Flag value |
| `pull` | `single/pull.php` | Pulled value |
| `object` | `single/object.php` | Object property |
| `local` | `single/local.php` | Local variable |
| `include` | `single/include.php` | Included content |

### Parameterized Types (`parms/`)

Types that accept parameters:

| Type | File | Description |
|------|------|-------------|
| `tag` | `parms/tag.php` | Tag as function: `padTagAsFunction($name, $value, $parm)` |
| `parm` | `parms/parm.php` | Parameter access with type |
| `pad` | `parms/pad.php` | PAD include |
| `php` | `parms/php.php` | PHP include |
| `app` | `parms/app.php` | Application include |
| `sequence` | `parms/sequence.php` | Sequence generator |

## Fast Path (`fast.php`)

For simple expressions that are just function names (e.g., `trim`), the fast path bypasses full parsing:

```php
if ( $GLOBALS['padInfo'] )
  include PAD . 'events/functionsFast.php';  // Tracing

return include PAD . "functions/$eval.php";  // Direct function call
```

## Directory Structure

```
eval/
├── eval.php              # Main entry point
├── fast.php              # Fast path for simple functions
│
├── actions/              # Operator action handlers
│   ├── alone.php         # Operator with implicit operands
│   ├── double.php        # Binary operator
│   ├── doubleLeft.php    # Binary with missing left
│   ├── doubleRight.php   # Binary with missing right
│   ├── single.php        # Unary operator
│   └── singleRight.php   # Unary with right operator
│
├── go/                   # Operator execution
│   ├── go.php            # Dispatch based on operand types
│   ├── doubleVarVar.php  # scalar OP scalar
│   ├── doubleArrVar.php  # array OP scalar
│   ├── doubleVarArr.php  # scalar OP array
│   ├── doubleArrArr.php  # array OP array
│   ├── singleVar.php     # OP scalar
│   └── singleArr.php     # OP array
│
├── parms/                # Parameterized type handlers
│   ├── app.php           # Application include
│   ├── pad.php           # PAD include
│   ├── parm.php          # Parameter access
│   ├── php.php           # PHP include
│   ├── sequence.php      # Sequence generator
│   └── tag.php           # Tag as function
│
├── single/               # Simple type handlers
│   ├── array.php         # Array access
│   ├── constant.php      # PHP constant
│   ├── content.php       # Content store
│   ├── data.php          # Data store
│   ├── field.php         # Field/variable
│   ├── flag.php          # Flag value
│   ├── include.php       # Include content
│   ├── level.php         # Level variable
│   ├── local.php         # Local variable
│   ├── object.php        # Object property
│   ├── parm.php          # Parameter value
│   ├── property.php      # Property value
│   ├── pull.php          # Pulled value
│   └── tag.php           # Tag value
│
└── type/                 # Type dispatch
    ├── parms.php         # Parameterized type handler
    ├── single.php        # Simple type handler
    └── type.php          # Type dispatch entry
```

## Supporting Library (`lib/eval/`)

```
lib/eval/
├── after.php       # Post-parse type resolution
├── array.php       # Array access handling
├── check.php       # Validation checks
├── const.php       # Operator constants and precedence
├── double.php      # Double operand handling
├── eval.php        # Evaluation utilities
├── multi.php       # Multi-expression handling
├── nextKey.php     # Next key finder
├── openClose.php   # Parentheses handling
├── operations.php  # Main operator processing
├── parse.php       # Expression parser
├── pipes.php       # Pipe splitting
├── result.php      # Result computation
├── types.php       # Type utilities
└── value.php       # Value handling
```

## Example Evaluation

Expression: `$price * 1.1 | round`

1. **Parse**: `[$price, $] [*, OPR] [1.1, VAL] [|, pipe] [round, other]`

2. **After**: `[123.45, VAL] [*, OPR] [1.1, VAL] [|, pipe] [round, TYPE]`

3. **Pipes**: Split into `[123.45 * 1.1]` and `[round]`

4. **Evaluate first segment**:
   - Find `*` operator
   - Get left=123.45, right=1.1
   - Execute: 123.45 * 1.1 = 135.795
   - Result: `[135.795, VAL]`

5. **Evaluate second segment**:
   - Input value: 135.795
   - round TYPE token
   - Call round function
   - Result: 136

6. **Final result**: `136`

## Boolean Handling

PAD uses string-based booleans:
- **True**: `'1'` or any non-empty string
- **False**: `''` (empty string)

This allows natural use in string contexts while maintaining logical operations.

## Error Handling

Errors are reported via `padError()`:
- "No result back" - Expression produced no value
- "More than one result back" - Expression incomplete
- "Result is not a value" - Unexpected token type
- "Unsupported \\ char" - Invalid escape sequence
- "Escape \\ char only allowed inside a string"
