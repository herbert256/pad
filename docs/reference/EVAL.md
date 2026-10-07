# PAD Evaluation Subsystem

This document explains in detail how the PAD expression evaluation subsystem works.

## Overview

The eval subsystem is responsible for parsing and evaluating expressions in PAD templates. When you write `{echo $variable + 5 | trim}` in a PAD template, this subsystem handles the parsing, variable resolution, operator execution, and pipe processing.

## Entry Point

The main entry point is `eval.php`:

```php
// Fast path: if expression is a plain name of a built-in pipe function, use optimized path
if ( preg_match ( '/^[A-Za-z][A-Za-z0-9_]*$/', $eval ) and file_exists ( PAD . "functions/$eval.php" ) )
  return include PAD . 'eval/fast.php';

// Full evaluation pipeline
$result = padEvalParsed ( $eval );                 // Step 1: Validate and parse into tokens (kept per request)
if ( ! padEvalCheckPipes ( $result, $eval, $pipe ) )  // the pipe functions named must exist
  return '';
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
| `&` | Property of the current tag | `&current` |
| `#` | Option/parameter reference | `#option` |
| `$$` | The `@` placeholder - the piped value | `@`, `$$` |
| `prop` | Property reference | `first@items`, `current@` |
| `%` | A whole expression that is a printf format | `%05.2f` |
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
- Two-character operators: `**`, `<=`, `>=`, `==`, `<>`, `!=`

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
| `<>` or `!=` | `NE` |

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

Operators bind in these groups, strongest first. The operators of one group bind equally
and are applied left to right - `10 - 2 + 3` is 11, `12 / 2 * 3` is 18 - except `**`,
which binds right to left: `2 ** 3 ** 2` is `2 ** 9`, 512. A sign before a value and `!`
bind weaker than `**`, as in PHP - `-$b ** 2` and `-3 ** 2` are -9 - and a sign or `!` that
opens the right operand of `**` belongs to that operand: `2 ** -$b` is `2 ** (-$b)`:

```php
const padEval_groups = [
  [ '**' ],                         // Power
  [ '!', 'NEG', 'POS' ],            // NOT and the signs (unary)
  [ '*', '/', '%' ],                // Multiplication
  [ '+', '-' ],                     // Addition
  [ '.' ],                          // String concatenation
  [ 'LT', 'LE', 'GT', 'GE' ],       // Ordering
  [ 'EQ', 'NE' ],                   // Equality
  [ 'NOT' ],                        // NOT (word form) - not $a eq $b denies the comparison
  [ 'AND' ],
  [ 'XOR' ],
  [ 'OR' ],
  [ '??' ],                         // empty-coalescing
];
```

Weaker than all of them is the inline ternary `cond ? then : else`, resolved by
`padEvalTernary()` (`lib/eval/ternary.php`) before any operator group: the first `?` of a
range splits it at its own `:` (a nested `?` takes the next one), the condition is reduced
alone, and only the chosen branch is reduced after it. Nothing before the `?` means the
piped value is the condition: `{$n | ? 'some' : 'none'}`.

`a ?? b` answers `a` unless it is empty - `''`, NULL, an empty list, a missing field - and
then `b`; a `$field` standing before `??` may be missing under the strict check.

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
if ( $opr == 'NE' )  $now = ($left != $right) ? 1 : '';

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
| `property` | `single/property.php` | `padTagValue($name, 1)` |
| `parm` | `single/parm.php` | `padOptValue($name, 1)` |
| `array` | `single/array.php` | `padArrayValue($name, TRUE)` |
| `constant` | `single/constant.php` | `constant($name)` |
| `level` | `single/level.php` | `padGetLevelArray($name)` |
| `bool` | `single/bool.php` | `$padBoolStore[$name]`, FALSE when not set |
| `flag` | `single/flag.php` | `$padBoolStore[$name]` - an error when not set |
| `pull` | `single/pull.php` | The stored sequence `$pqStore[$name]` |
| `object` | `single/object.php` | An application global as an array (`padToArray`) |
| `local` | `single/local.php` | A `_data/` file's data |
| `include` | `single/include.php` | An `_include/` snippet, rendered |

### Parameterized Types (`parms/`)

Types that accept parameters:

| Type | File | Description |
|------|------|-------------|
| `tag` | `parms/tag.php` | Tag as function: `padTagAsFunction($name, $value, $parm)` |
| `pad` | `parms/pad.php` | A built-in pipe function, `functions/$name.php`, after its parameter count is checked |
| `php` | `parms/php.php` | A PHP function `$padPhpFunctions` allows |
| `app` | `parms/app.php` | An application's `_functions/` file |
| `function` | `parms/function.php` | Whichever function kind the name is |
| `action` | `parms/action.php` | A sequence action |
| `script` | `parms/script.php` | A `_scripts/` script |
| `sequence` | `parms/sequence.php` | Sequence generator |

## Fast Path (`fast.php`)

For simple expressions that are just function names (e.g., `trim`), the fast path bypasses full parsing:

```php
$kind  = 'pad';
$name  = $eval;
$count = 0;
$parm  = [];

if ( $padInfo )
  include PAD . 'events/functionsFast.php';  // Tracing

return include PAD . 'eval/parms/pad.php';   // The function, through the parameter-count check
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
│   ├── action.php        # Sequence action
│   ├── app.php           # Application function (_functions/)
│   ├── function.php      # Any function kind
│   ├── pad.php           # Built-in pipe function
│   ├── php.php           # PHP function
│   ├── script.php        # Script from _scripts/
│   ├── sequence.php      # Sequence generator
│   └── tag.php           # Tag as function
│
├── single/               # Simple type handlers
│   ├── array.php         # Array access
│   ├── bool.php          # Bool store
│   ├── constant.php      # PHP constant
│   ├── content.php       # Content store
│   ├── data.php          # Data store
│   ├── field.php         # Field/variable
│   ├── flag.php          # Bool store, strict
│   ├── include.php       # Include content
│   ├── level.php         # Array field of an enclosing row
│   ├── local.php         # A _data/ file
│   ├── object.php        # Application global as array
│   ├── parm.php          # Parameter value
│   ├── property.php      # Property value
│   └── pull.php          # Stored sequence
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
├── reduce.php      # An array reduced to the one value an operator needs
├── result.php      # Result computation
├── ternary.php     # The inline ternary cond ? a : b
├── types.php       # Type utilities
├── validate.php    # The check before parsing - brackets, strings, functions, operands
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

The comparison and logical operators answer `1` for true and `''` for false. A value is
tested for truth as PHP tests it:
- **False**: `''`, `'0'`, `0`, NULL, FALSE and an empty array
- **True**: everything else - an array when it has elements

This allows natural use in string contexts while maintaining logical operations.

## Error Handling

Under `$padCheckSyntax` (the default) errors are reported via `padError()`. Before anything
runs, `lib/eval/validate.php` reads the expression and names the fault and its position -
`Expression error: ...`:
- a `(` or `[` that is never closed, a string never closed - a `)` or `]` that closes nothing
  in a tag's parameters is named before that, by the parameter split: "Closing ) without an
  opening ("
- a pipe function that does not exist
- a comparison or logical operator with nothing on its left or its right - an arithmetic one
  is not named: `{echo 5 *}` answers 0, `{if 5 + eq 1}` holds
- a pipe operator written without its space - `| +1` for `| + 1`

While it runs:
- "there is no field named '$x'" - a missing field (`lib/eval/after.php`)
- "'a' is not a number for +", "a division by zero in /" - arithmetic
- "the ? of an inline ternary has no :", "a branch of an inline ternary is empty" (`lib/eval/ternary.php`)
- "No result back", "More than one result back", "Result is not a value" - an expression that reduced to no single value
- "Unsupported \\ char" - Invalid escape sequence
- "Escape \\ char only allowed inside a string"

With the check off nothing is reported, and the evaluator answers what it can: a value that is
no number counts as 0 (`'a' + 1` is 1), a missing field is empty (`$nope + 1` is 1), an unknown
escape loses its backslash (`'a\qb'` is `aqb`), a ternary without its `:` keeps what it has
(`1 ? 'a'` is `1a`), and an expression that cannot be reduced - an unclosed bracket, a
division by zero - answers `''`.
