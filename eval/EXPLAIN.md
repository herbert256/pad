# Expression Evaluation System - Deep Dive

This document provides an in-depth exploration of PAD's expression evaluation system, which handles all expressions within `{...}` tags.

## Overview

The eval system transforms string expressions into computed values through a multi-stage pipeline:

```
Input: "$price * 1.21 | round(2)"
  ↓
[Parse] → Token array
  ↓
[After] → Resolve variables, identify types
  ↓
[Pipes] → Split into pipe stages
  ↓
[Result] → Evaluate each stage → Final value
```

## Entry Points

### `lib/eval/eval.php`
```php
function padEval($eval, $value='')    // Main entry, wrapped in try/catch
function padEvalBool($eval, $value='') // Returns boolean result
```

### `eval/eval.php`
The actual evaluation logic:
1. Fast path: If expression matches a function name in `functions/`, use `eval/fast.php`
2. Full path: Parse → After → Pipes → Result

### `eval/fast.php`
Optimization for simple function calls - directly includes the function file without full parsing.

---

## Stage 1: Parsing (`lib/eval/parse.php`)

### `padEvalParse(&$result, $eval)`

Tokenizes the input string into an array of `[value, type]` pairs.

### Token Types Produced

| Type | Description | Example Input |
|------|-------------|---------------|
| `VAL` | Literal value (string, number) | `'hello'`, `42`, `3.14` |
| `$` | Field/variable reference | `$name`, `$user.email` |
| `&` | Tag value reference | `&count`, `&tagname` |
| `#` | Option/parameter reference | `#limit`, `#offset` |
| `$$` | Current pipe value (`@` or `$$`) | `@`, `$$` |
| `%` | Printf format string | `%05d` |
| `hex` | Hexadecimal literal | `0xFF` |
| `OPR` | Operator | `+`, `-`, `*`, `LT`, `AND` |
| `other` | Unresolved identifier | `trim`, `myFunc` |
| `pipe` | Pipe separator | `\|` |
| `open`/`close` | Parentheses | `(`, `)` |
| `a-open`/`a-close` | Array brackets | `[`, `]` |

### Parsing Rules

**Strings:** Single (`'`) or double (`"`) quotes, with escape sequences (`\n`, `\r`, `\t`, `\\`, `\'`, `\"`)

**Numbers:** Integers, floats, scientific notation (`1.5e-3`), negative numbers

**Variables:** `$name`, `$name.subkey`, `$name@property`, supports wildcards (`*`), comparisons (`<`, `>`)

**Operators:** Single char (`+`, `-`, `*`, `/`, `%`, `!`, `.`) and double char (`**`)

---

## Stage 2: Post-Processing (`lib/eval/after.php`)

### `padEvalAfter(&$result)`

Resolves `other` tokens into their actual types:

1. **Type Resolution:** Checks if identifier is a known type via `padTypeFunction()`
   - If `type:name` syntax, extracts both
   - Marks as `TYPE` with type info in `[2]`

2. **Operator Aliases:** Converts comparison operators
   ```php
   '<'  → 'LT'    '<=' → 'LE'    '>'  → 'GT'
   '>=' → 'GE'    '='  → 'EQ'    '==' → 'EQ'
   '<>' → 'NE'
   ```

3. **Text Operators:** Recognizes `AND`, `OR`, `XOR`, `NOT`, `LT`, `LE`, `GT`, `GE`, `EQ`, `NE`

4. **Variable Resolution:**
   - `$` tokens → `padFieldValue()` → actual value
   - `&` tokens → `padTagValue()` → tag value
   - `#` tokens → `padOptValue()` → option value
   - `hex` tokens → `hex2bin()` → binary string
   - Remaining `other` → `padConstant()` → constant or literal

---

## Stage 3: Pipe Splitting (`lib/eval/pipes.php`)

### `padEvalPipes($result, &$pipes)`

Splits token array at `pipe` tokens into separate evaluation stages:

```php
// Input: $price * 1.21 | round(2)
// Output: $pipes[0] = [$price, *, 1.21]
//         $pipes[1] = [round, (, 2, )]
```

Each pipe stage receives the previous stage's result as `$$` or `@`.

---

## Stage 4: Result Evaluation (`lib/eval/result.php`)

### `padEvalResult($result, $value, $eval)`

Evaluates a single pipe stage through multiple passes:

```php
padEvalValue()   // Replace $$ with incoming value
padEvalArray()   // Process [...] array literals
padEvalOpnCls()  // Process (...) parentheses
padEvalOpr()     // Process operators by precedence
padEvalMulti()   // Final multi-value handling
```

Returns single `VAL` token or errors if result is ambiguous.

---

## Operator Evaluation (`lib/eval/operations.php`)

### `padEvalOpr(&$result, $myself, $start, $end)`

Processes operators in precedence order (defined in `lib/eval/const.php`):

```php
const padEval_precedence = [
  '!',                              // Highest
  '**', '*', '/', '%', '+', '-',    // Arithmetic
  '.',                              // Concatenation
  'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', // Comparison
  'AND', 'XOR', 'OR',               // Logical
  'NOT'                             // Lowest
];
```

### Action Selection (`eval/actions/`)

Based on operand positions:

| File | Condition | Example |
|------|-----------|---------|
| `single.php` | Unary operator + value | `!$flag` |
| `singleRight.php` | Unary + operator (chain) | `!!$flag` |
| `double.php` | Value + operator + value | `$a + $b` |
| `doubleLeft.php` | Operator + value (no left) | `+5` |
| `doubleRight.php` | Value + operator + operator | `$a + -$b` |
| `alone.php` | Two consecutive operators | Special handling |

---

## Operation Execution (`eval/go/`)

### `eval/go/go.php`
Dispatches to type-specific handler based on operand types:

| File | Left | Right | Use Case |
|------|------|-------|----------|
| `singleVar.php` | - | scalar | `!$flag` |
| `singleArr.php` | - | array | `!$array` |
| `doubleVarVar.php` | scalar | scalar | `$a + $b` |
| `doubleArrVar.php` | array | scalar | `$arr + 1` |
| `doubleVarArr.php` | scalar | array | `1 + $arr` |
| `doubleArrArr.php` | array | array | `$arr1 + $arr2` |

### `eval/go/doubleVarVar.php` - Core Operations

```php
// Comparison (return '1' or '')
'LT'  → $left <   $right
'LE'  → $left <=  $right
'EQ'  → $left ==  $right
'GE'  → $left >=  $right
'GT'  → $left >   $right
'NE'  → $left <>  $right

// Logical (return '1' or '')
'AND' → $left AND $right
'OR'  → $left OR  $right
'XOR' → $left XOR $right

// Concatenation
'.'   → $left . $right

// Arithmetic (auto-converts to int/float)
'+'   → $left + $right
'-'   → $left - $right
'*'   → $left * $right
'/'   → $left / $right
'%'   → $left % $right
```

### `eval/go/singleVar.php` - NOT Operation
```php
$now = ($right) ? '' : '1';  // Logical NOT
```

---

## Type/Function Calls (`eval/type/`)

### `eval/type/type.php`
When a `TYPE` token is found, executes the corresponding handler.

### `eval/type/single.php`
For types with handlers in `eval/single/`:
- `field.php` → `padFieldValue($name)`
- `data.php` → `$GLOBALS['padDataStore'][$name]`
- `content.php` → `$GLOBALS['padContentStore'][$name]`
- `pull.php` → `$GLOBALS['pqStore'][$name]`
- `level.php` → `padGetLevelArray($name)`
- `flag.php`, `array.php`, `constant.php`, `local.php`, `object.php`, `parm.php`, `property.php`, `tag.php`, `include.php`

### `eval/type/parms.php`
For types needing parameters (functions):
1. Collects parameters from following tokens
2. Gets incoming `$value` if present
3. Dispatches to `eval/parms/$kind.php`

### `eval/parms/` - Function Handlers

| File | Purpose |
|------|---------|
| `pad.php` | PAD built-in functions (`functions/*.php`) |
| `app.php` | Application functions (`APP2/_functions/`) |
| `php.php` | Native PHP functions via `call_user_func_array()` |
| `tag.php` | Tag as function |
| `sequence.php` | Sequence functions |
| `parm.php` | Parameter access |

---

## Special Constructs

### Parentheses (`lib/eval/openClose.php`)
`padEvalOpnCls()` finds matching `(...)` pairs and:
1. Removes the parentheses tokens
2. Recursively evaluates the inner expression
3. If preceded by a `TYPE`, marks it for function call with parameters

### Arrays (`lib/eval/array.php`)
`padEvalArray()` handles `[...]` array literals:
1. Finds matching brackets
2. Evaluates contents
3. Collects values into array

### Pipe Value (`lib/eval/value.php`)
`padEvalValue()` replaces `$$` tokens with the incoming pipe value:
```php
{$price | $$ * 1.21}  // $$ gets $price value
```

Also handles `%` format strings via `sprintf()`.

---

## Complete Example

Expression: `$price * 1.21 | round(2)`

### Stage 1: Parse
```
[100] = ['price', '$']
[200] = ['*', 'OPR']
[300] = ['1.21', 'VAL']
[400] = ['|', 'pipe']
[500] = ['round', 'other']
[600] = ['(', 'open']
[700] = ['2', 'VAL']
[800] = [')', 'close']
```

### Stage 2: After
```
[100] = [99.99, 'VAL']     // $price resolved to value
[200] = ['*', 'OPR']
[300] = [1.21, 'VAL']
[400] = ['|', 'pipe']
[500] = ['round', 'TYPE', 'pad', 0]  // Identified as PAD function
[600] = ['(', 'open']
[700] = [2, 'VAL']
[800] = [')', 'close']
```

### Stage 3: Pipes
```
Pipe 0: [100, 200, 300] → 99.99 * 1.21
Pipe 1: [500, 600, 700, 800] → round($$, 2)
```

### Stage 4: Result
```
Pipe 0: 99.99 * 1.21 = 120.9879
Pipe 1: round(120.9879, 2) = 120.99
```

Final result: `120.99`

---

## Tracing

When `$padInfo` is set, `padEvalTrace()` records each transformation step in `$_eval` global for debugging. Trace points include: `parse`, `after`, `value1`, `array1`, `opncls1`, `opr3`, `multi1`, `double2`, `check2`, `type2`, `go`, etc.

---

## File Summary

```
eval/
├── eval.php           # Main evaluation orchestrator
├── fast.php           # Fast path for simple functions
├── actions/           # Operator action handlers
│   ├── alone.php      # Consecutive operators
│   ├── double.php     # Binary: val op val
│   ├── doubleLeft.php # Unary prefix: op val
│   ├── doubleRight.php# Chain: val op op val
│   ├── single.php     # Unary: op val
│   └── singleRight.php# Unary chain: op op val
├── go/                # Operation execution
│   ├── go.php         # Dispatcher
│   ├── singleVar.php  # NOT on scalar
│   ├── singleArr.php  # NOT on array
│   ├── doubleVarVar.php # Scalar op scalar
│   ├── doubleArrVar.php # Array op scalar
│   ├── doubleVarArr.php # Scalar op array
│   └── doubleArrArr.php # Array op array
├── parms/             # Function call handlers
│   ├── app.php        # Application functions
│   ├── pad.php        # PAD functions
│   ├── php.php        # PHP native functions
│   ├── parm.php       # Parameter access
│   ├── sequence.php   # Sequence functions
│   └── tag.php        # Tag as function
├── single/            # Simple type value getters
│   ├── array.php, constant.php, content.php, data.php
│   ├── field.php, flag.php, include.php, level.php
│   ├── local.php, object.php, parm.php, property.php
│   ├── pull.php, tag.php
│   └── ...
└── type/              # Type evaluation
    ├── type.php       # Type dispatcher
    ├── single.php     # Simple type handler
    └── parms.php      # Function type handler

lib/eval/
├── eval.php      # padEval(), padEvalBool(), padEvalTrace()
├── parse.php     # padEvalParse() - tokenizer
├── const.php     # Operator precedence, operator lists
├── after.php     # padEvalAfter() - post-parse resolution
├── pipes.php     # padEvalPipes() - pipe splitting
├── result.php    # padEvalResult() - stage evaluation
├── value.php     # padEvalValue() - $$ replacement
├── array.php     # padEvalArray() - [...] handling
├── openClose.php # padEvalOpnCls() - (...) handling
├── operations.php# padEvalOpr() - operator processing
├── double.php    # padEvalDouble() - consecutive operator fix
├── types.php     # padEvalType() - TYPE token handling
├── check.php     # Validation utilities
├── multi.php     # Multi-value handling
└── nextKey.php   # Key navigation utilities
```
