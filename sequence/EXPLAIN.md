# Sequence System - Deep Dive

This document provides an in-depth exploration of PAD's sequence generation system, which creates mathematical and algorithmic number sequences.

## Overview

The sequence system generates arrays of numbers based on mathematical rules, filters, and transformations:

```
Input: {prime rows=10}
  ↓
[Init] → Set parameters (from, to, rows, etc.)
  ↓
[Find] → Determine sequence type and build method
  ↓
[Build] → Generate numbers via loop/fixed/order
  ↓
[Plays] → Apply filters (keep/remove/flag)
  ↓
[Actions] → Transform results (sort, reverse, sum)
  ↓
[Exit] → Store result, return array
```

## Entry Points

### `sequence/sequence.php`
The main orchestrator that includes all processing stages:
```php
include PQ . 'inits/direct.php';    // Initialize globals
include PQ . 'inits/clear.php';     // Clear state
include PQ . 'inits/vars.php';      // Set variables
include PQ . 'actions/set.php';     // Configure actions
include PQ . 'plays/inits.php';     // Initialize plays
include PQ . 'actions/inits.php';   // Parse action options
include PQ . 'inits/limits.php';    // Set iteration limits
include PQ . 'build/build.php';     // Generate sequence
include PQ . 'exits/actions.php';   // Apply actions
include PQ . 'exits/done.php';      // Cleanup
include PQ . 'exits/info.php';      // Debug info

return array_values($pqResult);     // Return sequence
```

---

## Stage 1: Initialization (`inits/`)

### `inits/parms.php` - Parameter Extraction
Extracts sequence parameters from tag options:

```php
$pqFrom = $pqParms['from']      ?? 1           ;  // Starting value
$pqTo   = $pqParms['to']        ?? PHP_INT_MAX ;  // Ending value
$pqSole = $pqParms['sole']      ?? 0           ;  // Single value
$pqMin  = $pqParms['minimal']   ?? PHP_INT_MIN ;  // Minimum result
$pqMax  = $pqParms['maximal']   ?? PHP_INT_MAX ;  // Maximum result
$pqInc  = $pqParms['increment'] ?? 1           ;  // Step size
$pqRows = $pqParms['rows']      ?? 0           ;  // Max result count
$pqTry  = $pqParms['try']       ?? 0           ;  // Max iterations
$pqStop = $pqParms['stop']      ?? PHP_INT_MAX ;  // Stop at value
$pqSkip = $pqParms['skip']      ?? 0           ;  // Skip first N
```

### `inits/set.php` - Play Mode Selection
Determines the evaluation mode based on tag/type:

| $pqPlay | Purpose |
|---------|---------|
| `make` | Default - generate values |
| `keep` | Keep only matching values |
| `remove` | Remove matching values |
| `flag` | Return 1/0 for match/no-match |

### `inits/limits.php` - Iteration Limits
Sets default iteration limits:
```php
if (!$pqTry)  $pqTry  = $padSeqDefaultTries;  // Max attempts
if (!$pqRows) $pqRows = $padSeqDefaultRows;   // Max results
```

---

## Stage 2: Build Methods (`build/`)

### `build/build.php` - Dispatcher
Includes the appropriate build type handler:
```php
include PQ . "build/types/$pqBuild.php";
```

### Build Types (`build/types/`)

| Type | File | Description |
|------|------|-------------|
| `loop` | `loop.php` | Iterate from `$pqFrom` to `$pqTo` |
| `order` | `order.php` | Build ordered sequence (Fibonacci, etc.) |
| `fixed` | `fixed.php` | Use pre-computed array from type |
| `build` | `build.php` | Build array via type's `build.php` |
| `bool` | `bool.php` | Boolean check function |
| `function` | `function.php` | Direct function call |
| `check` | `check.php` | Check inclusion criteria |
| `make` | `make.php` | Make values via type |
| `pull` | `pull.php` | Pull from stored sequence |
| `given` | `given.php` | Use given input |

### `build/types/type/loop.php` - Core Loop
The main iteration loop:
```php
$pqGo = $pqFrom;

while ($pqGo <= $pqTo) {
    $pqLoop = $pqGo;

    if (!include PQ . 'build/one.php')
        break;

    $pqGo = $pqGo + $pqInc;
}
```

### `build/one.php` - Single Value Processing
Processes each iteration:
```php
$pqTries++;

// Check iteration limit
if ($pqTries > $pqTry) return FALSE;

// Handle random parameters
if ($pqRandomParm) $pqParm = include PQ . 'build/parm.php';
if ($pqRandomly)   $pqLoop = include PQ . 'build/randomly/randomly.php';

// Generate value based on build type
    if (pqStore($pqBuild))     $pq = $pqLoop;
elseif ($pqBuild == 'bool')    $pq = ('pqBool'.ucfirst($pqSeq))($pqLoop, $pqParm);
elseif ($pqBuild == 'function') $pq = ('pq'.ucfirst($pqSeq))($pqLoop);
elseif ($pqBuild == 'loop')    $pq = include PT . "$pqSeq/loop.php";
elseif ($pqBuild == 'make')    $pq = include PT . "$pqSeq/make.php";
elseif ($pqBuild == 'order')   $pq = include PT . "$pqSeq/order.php";

// Apply plays (filters)
if (count($pqPlays)) include PQ . 'plays/plays.php';

// Validate result
if (is_numeric($pq) && $pq < $pqMin) return TRUE;  // Skip
if (is_numeric($pq) && $pq > $pqMax) return TRUE;  // Skip
if ($pqUnique && in_array($pq, $pqResult)) return TRUE;  // Skip duplicate

// Add to result
$pqResult[] = $pq;

// Check termination
if (is_numeric($pq) && $pq >= $pqStop) return FALSE;
if ($pqRows && count($pqResult) >= $pqRows) return FALSE;

return TRUE;
```

---

## Stage 3: Sequence Types (`types/`)

Each sequence type has its own subdirectory with specific files:

### Type Directory Structure
```
types/{sequence}/
├── loop.php      # Loop-based value generation
├── order.php     # Ordered sequence (uses previous values)
├── build.php     # Build fixed array
├── fixed.php     # Return pre-computed array
├── go.php        # Core calculation
├── bool.php      # Boolean check function
├── make.php      # Make value from input
├── generated.php # Pre-computed constant array
└── flags/        # Flag subdirectory for keep/remove/flag
    ├── keep.php
    ├── remove.php
    └── flag.php
```

### Example: Fibonacci (`types/fibonacci/`)

**go.php** - Core calculation:
```php
$padFibonacci = $pqLoop - 1;
return $pqOrder[$padFibonacci-1] + $pqOrder[$padFibonacci-2];
```

**generated.php** - Pre-computed values:
```php
const PADfibonacci = [0,1,1,2,3,5,8,13,21,34,55,89,144,233,...];
```

### Example: Prime (`types/prime/`)

**loop.php** - Check if prime, or get next prime:
```php
include_once PT . "prime/bool.php";
return pqBoolPrime($pqLoop);
```

**bool.php** - Boolean primality test:
```php
function pqBoolPrime($n, $p=0) {
    if (!function_exists('gmp_prob_prime'))
        return TRUE;
    if (gmp_prob_prime($n))
        return TRUE;
    else
        return FALSE;
}
```

### Example: Add (`types/add/`)

**loop.php** - Simple addition:
```php
return $pqLoop + $pqParm;
```

### Example: Range (`types/range/`)

**build.php** - Build range array:
```php
return padGetRange($pqParm, $pqInc);
```

### Sequence Categories

**Basic:**
- `range` - Number range (1..10)
- `list` - Explicit list
- `repeat` - Repeat value
- `identity` - Pass-through

**Arithmetic:**
- `add`, `substract`, `multiply`, `divide`, `modulo`
- `exponentiation`, `ceil`, `floor`, `round`

**Number Theory:**
- `prime`, `composite`, `semiprime`
- `fibonacci`, `lucas`, `tribonacci`
- `mersenne`, `perfect`, `powerful`

**Figurate Numbers:**
- `triangular`, `square`, `pentagonal`, `hexagonal`
- `heptagonal`, `octagonal`, `decagonal`
- `tetrahedral`, `octahedral`

**Special:**
- `catalan`, `bell`, `pell`, `perrin`
- `golomb`, `happy`, `harshad`, `kaprekar`
- `kolakoski`, `recaman`, `sylvester`

**Logical:**
- `and`, `or`, `xor`, `not`, `nand`, `nor`, `xnor`

**Other:**
- `even`, `odd`, `multiple`, `power`
- `palindrome`, `emirp`, `lucky`, `polite`

---

## Stage 4: Plays (Filters) (`plays/`)

Plays filter or transform values during generation.

### `plays/plays.php` - Play Execution
```php
foreach ($pqPlays as $pqTmp) {
    extract($pqTmp);

    $pqLoop = $pq;
    $pqParm = include PQ . 'plays/parm.php';
    $pq     = include PQ . "plays/play/$pqBuild.php";

    // Apply play mode
    if ($pqPlay == 'make' && $pq === TRUE) $pq = $pqLoop;

    elseif ($pqPlay == 'keep' && $pq === TRUE)   $pq = $pqLoop;
    elseif ($pqPlay == 'keep' && $pq <> $pqLoop) $pq = FALSE;

    elseif ($pqPlay == 'remove' && $pq === TRUE)   $pq = FALSE;
    elseif ($pqPlay == 'remove' && $pq === FALSE)  $pq = $pqLoop;
    elseif ($pqPlay == 'remove' && $pq == $pqLoop) $pq = FALSE;

    elseif ($pqPlay == 'flag' && $pq === TRUE)   $pq = 1;
    elseif ($pqPlay == 'flag' && $pq === FALSE)  $pq = 0;
    elseif ($pqPlay == 'flag' && $pq == $pqLoop) $pq = 1;
    elseif ($pqPlay == 'flag' && $pq <> $pqLoop) $pq = 0;
}
```

### Play Modes

| Mode | TRUE result | FALSE result | Match result |
|------|-------------|--------------|--------------|
| `make` | Use $pqLoop | Skip | Use value |
| `keep` | Use $pqLoop | Skip | Use $pqLoop |
| `remove` | Skip | Use $pqLoop | Skip |
| `flag` | Return 1 | Return 0 | Return 1/0 |

### Play Types (`plays/play/`)

- `bool.php` - Boolean function check
- `function.php` - Function call
- `order.php` - Ordered sequence check

---

## Stage 5: Actions (Transformations) (`actions/`)

Actions transform the final result array.

### `actions/actions.php` - Action Execution
```php
foreach ($pqActions as $pqAction => $pqActionParm) {
    $pqActionStart = $pqResult;
    $pqActionList  = padExplode($pqActionParm, '|');
    $pqActionCnt   = (ctype_digit($pqActionParm)) ? $pqActionParm : 1;

    include PQ . "actions/types/$pqAction.php";

    $pqActionsHit[$pqAction] = array_values($pqResult);
}
```

### Action Types (`actions/types/`)

| Action | Description | Example |
|--------|-------------|---------|
| `sort` | Sort values | `asort($pqResult)` |
| `reverse` | Reverse order | `array_reverse($pqResult)` |
| `shuffle` | Random order | `shuffle($pqResult)` |
| `randomize` | Randomize array | Random reorder |
| `first` | Keep first N | `array_slice($pqResult, 0, N)` |
| `last` | Keep last N | Keep tail |
| `sum` | Sum all values | `array_sum($pqResult)` |
| `product` | Multiply all | Product of values |
| `average` | Average value | Sum / count |
| `median` | Median value | Middle value |
| `count` | Count elements | `count($pqResult)` |
| `distinct` | Unique values | Remove duplicates |
| `dedup` | Deduplicate | Remove consecutive duplicates |
| `trim` | Trim array | Remove from ends |
| `shift` | Shift array | Remove first |
| `splice` | Splice array | Array splice |
| `append` | Append value | Add to end |
| `prepend` | Prepend value | Add to start |
| `combine` | Combine arrays | Merge sequences |

---

## Options Reference (`options/types/`)

| Option | File | Purpose |
|--------|------|---------|
| `from` | `from.php` | Starting value |
| `to` | `to.php` | Ending value |
| `sole` | `sole.php` | Single value (sets from=to) |
| `rows` | `rows.php` | Maximum result count |
| `count` | `count.php` | Alias for rows |
| `increment` | `increment.php` | Step size |
| `skip` | `skip.php` | Skip first N |
| `stop` | `stop.php` | Stop at value |
| `min` | `min.php` | Minimum result value |
| `max` | `max.php` | Maximum result value |
| `unique` | `unique.php` | Only unique values |
| `try` | `try.php` | Maximum iterations |
| `build` | `build.php` | Build method override |
| `name` | `name.php` | Named sequence |
| `pull` | `pull.php` | Pull from stored sequence |
| `push` | `push.php` | Push to stored sequence |
| `store` | `store.php` | Store result |
| `toData` | `toData.php` | Store to data variable |
| `action` | `action.php` | Apply action |
| `negative` | `negative.php` | Negate results |
| `randomly` | `ramdomly.php` | Random selection |
| `keep` | `keep.php` | Keep filter |
| `remove` | `remove.php` | Remove filter |
| `single` | `single.php` | Single result |

---

## Complete Example

Expression: `{prime rows=5 keep=odd action=reverse}`

### Stage 1: Initialize
```
$pqFrom = 1
$pqTo   = PHP_INT_MAX
$pqRows = 5
$pqPlay = 'keep'
```

### Stage 2: Find
```
$pqSeq   = 'prime'
$pqBuild = 'bool'
$pqPlays = [['pqSeq' => 'odd', 'pqPlay' => 'keep']]
$pqActions = ['reverse' => '']
```

### Stage 3: Build Loop
```
Loop 1: $pqLoop=2, pqBoolPrime(2)=TRUE, pqBoolOdd(2)=FALSE → Skip
Loop 2: $pqLoop=3, pqBoolPrime(3)=TRUE, pqBoolOdd(3)=TRUE → Add [3]
Loop 3: $pqLoop=4, pqBoolPrime(4)=FALSE → Skip
Loop 4: $pqLoop=5, pqBoolPrime(5)=TRUE, pqBoolOdd(5)=TRUE → Add [3,5]
Loop 5: $pqLoop=6, pqBoolPrime(6)=FALSE → Skip
Loop 6: $pqLoop=7, pqBoolPrime(7)=TRUE, pqBoolOdd(7)=TRUE → Add [3,5,7]
...
Result after build: [3, 5, 7, 11, 13]
```

### Stage 4: Apply Actions
```
Action 'reverse': array_reverse([3, 5, 7, 11, 13])
Result: [13, 11, 7, 5, 3]
```

### Final Result
```php
[13, 11, 7, 5, 3]
```

---

## Randomness Support

### Random Parameters
Parameters can use `..` syntax for random ranges:
```
{prime from=1..100 rows=5}     // Random start 1-100
{add(1..10) rows=5}            // Random parameter 1-10
{range increment=1...5 rows=10} // Random increment 1-5 (triple dot)
```

### `build/randomly/` - Random Handling
- `init.php` - Initialize random state
- `randomly.php` - Random value selection
- `build/inits.php` - Random build initialization

---

## Storage System

### Sequence Store (`$pqStore`)
Sequences can be stored and retrieved:
```
{prime rows=10 push=myPrimes}  // Store as 'myPrimes'
{continue pull=myPrimes}       // Retrieve 'myPrimes'
```

### `exits/store/set.php` - Store Result
```php
if ($pqStoreUpdated)
    $pqStore[$padLastPush] = array_values($pqStore[$pqPull]);
else
    $pqStore[$padLastPush] = array_values($pqResult);
```

---

## File Summary

```
sequence/
├── sequence/
│   └── sequence.php       # Main orchestrator
├── inits/
│   ├── direct.php         # Initialize globals
│   ├── clear.php          # Clear state
│   ├── vars.php           # Variable setup
│   ├── parms.php          # Parse parameters
│   ├── set.php            # Set play mode
│   ├── limits.php         # Set iteration limits
│   ├── name.php           # Named sequence handling
│   ├── tag.php            # Tag-based initialization
│   ├── check/             # Validation
│   └── find/              # Sequence/type detection
├── build/
│   ├── build.php          # Build dispatcher
│   ├── one.php            # Single value processor
│   ├── vars.php           # Build variables
│   ├── given.php          # Given input handling
│   ├── parm.php           # Parameter handling
│   ├── store.php          # Store access
│   ├── include.php        # Type inclusion
│   ├── types/             # Build type handlers
│   │   ├── loop.php       # Loop build
│   │   ├── order.php      # Ordered build
│   │   ├── fixed.php      # Fixed array build
│   │   ├── build.php      # Array build
│   │   ├── bool.php       # Boolean build
│   │   ├── function.php   # Function build
│   │   ├── check.php      # Check build
│   │   ├── make.php       # Make build
│   │   ├── pull.php       # Pull build
│   │   ├── given.php      # Given build
│   │   └── type/          # Core type loops
│   └── randomly/          # Random handling
├── plays/
│   ├── inits.php          # Initialize plays
│   ├── plays.php          # Execute plays
│   ├── add.php            # Add play
│   ├── parm.php           # Play parameters
│   └── play/              # Play type handlers
│       ├── bool.php       # Boolean play
│       ├── function.php   # Function play
│       └── order.php      # Order play
├── actions/
│   ├── inits.php          # Parse actions
│   ├── actions.php        # Execute actions
│   ├── set.php            # Set up actions
│   ├── assoc.php          # Associative handling
│   ├── function.php       # Function action
│   ├── merge.php          # Merge handling
│   ├── negative/          # Negation
│   ├── order/             # Ordering
│   └── types/             # Action implementations
│       ├── sort.php, reverse.php, shuffle.php
│       ├── first.php, last.php, trim.php
│       ├── sum.php, product.php, average.php
│       ├── count.php, median.php
│       ├── distinct.php, dedup.php
│       └── append.php, prepend.php, combine.php
├── exits/
│   ├── actions.php        # Apply final actions
│   ├── done.php           # Cleanup
│   ├── info.php           # Debug info
│   ├── data.php           # Data handling
│   ├── return.php         # Return handling
│   ├── extra/             # Extra processing
│   ├── info/              # Info output
│   ├── return/            # Return handling
│   └── store/             # Store handling
├── options/
│   └── types/             # Option handlers (from, to, rows, etc.)
├── start/
│   └── ...                # Start handlers
└── types/                 # 80+ sequence implementations
    ├── add/, and/, antiprime/, bell/, ...
    ├── fibonacci/, prime/, ...
    └── {each has loop.php, bool.php, flags/, etc.}
```

---

## Tracing

When `$padInfo` is set, sequence processing records debug information via `exits/info.php`, including:
- Sequences used
- Build methods applied
- Actions executed
- Options processed
- Plays applied
