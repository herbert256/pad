# PAD Sequence Subsystem

This document explains in detail how the PAD sequence generation subsystem works.

## Overview

The sequence subsystem generates mathematical sequences like prime numbers, Fibonacci numbers, triangular numbers, and many more. It supports 79 different sequence types, filtering, transformations, and various output actions.

## Entry Point

The main entry point is `sequence/sequence.php`:

```php
include PQ . 'inits/direct.php';     // Set entry mode and globals
include PQ . 'inits/clear.php';      // Clear previous state
include PQ . 'inits/vars.php';       // Initialize variables
include PQ . 'actions/set.php';      // Set up actions
include PQ . 'plays/inits.php';      // Initialize plays (filters)
include PQ . 'actions/inits.php';    // Initialize actions
include PQ . 'inits/limits.php';     // Set iteration limits
include PQ . 'build/build.php';      // Generate the sequence
include PQ . 'exits/actions.php';    // Execute post-actions
include PQ . 'exits/done.php';       // Mark options as done
include PQ . 'exits/info.php';       // Generate debug info

return array_values ( $pqResult );   // Return the sequence
```

## Constants

The subsystem uses two path constants:
- `PQ` - Path to the sequence subsystem root (`pad/sequence/`)
- `PT` - Path to sequence types (`pad/sequence/types/`)

## Parameters

Parameters control how sequences are generated (`inits/parms.php`):

| Parameter | Default | Description |
|-----------|---------|-------------|
| `from` | 1 | Starting value for loop |
| `to` | PHP_INT_MAX | Ending value for loop |
| `sole` | 0 | Generate single value (sets from=to) |
| `minimal` | PHP_INT_MIN | Minimum acceptable value |
| `maximal` | PHP_INT_MAX | Maximum acceptable value |
| `increment` | 1 | Step between iterations |
| `rows` | varies | Maximum number of results |
| `try` | varies | Maximum iterations to attempt |
| `stop` | PHP_INT_MAX | Stop when value reaches this |
| `skip` | 0 | Skip first N valid results |
| `randomly` | '' | Enable random selection |
| `unique` | '' | Only unique values |
| `name` | '' | Name for storing sequence |
| `build` | '' | Override build type |
| `toData` | '' | Store to data |
| `negative` | 0 | Apply negative handling |
| `pull` | '' | Pull from stored sequence |
| `push` | '' | Push to stored sequence |

## Build Types

The subsystem supports different build strategies (`build/types/`):

### 1. Loop Build (`build/types/loop.php`)

Iterates from `$pqFrom` to `$pqTo`, calling the sequence's `loop.php` file for each value:

```php
$pqGo = $pqFrom;
while ( $pqGo <= $pqTo ) {
  $pqLoop = $pqGo;
  if ( ! include PQ . 'build/one.php' )
    break;
  $pqGo = $pqGo + $pqInc;
}
```

### 2. Fixed Build (`build/types/fixed.php`)

Uses a pre-computed array from the sequence's `fixed.php` file:

```php
$pqFixed = include PT . "$pqSeq/fixed.php";
foreach ( $pqFixed as $pqKey => $pqLoop ) {
  // Filter by from/to range
  if ( $pqKey < $pqFrom - 1 ) continue;
  if ( $pqKey > $pqTo - 1 ) break;
  include PQ . 'build/one.php';
}
```

### 3. Bool Build (`build/types/bool.php`)

Tests each number with a boolean function (e.g., `pqBoolPrime()`):

```php
$pq = ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
```

### 4. Function Build (`build/types/function.php`)

Calls a custom function:

```php
$pq = ( 'pq' . ucfirst($pqSeq) ) ( $pqLoop );
```

### 5. Make Build (`build/types/make.php`)

Generates the next value from current loop value (e.g., even numbers):

```php
$pq = include PT . "$pqSeq/make.php";
```

### 6. Order Build (`build/types/order.php`)

Generates values that depend on previous values (e.g., Fibonacci):

```php
$pq = include PT . "$pqSeq/order.php";
```

### 7. Check Build (`build/types/check.php`)

Validates values against criteria.

### 8. Pull Build (`build/types/pull.php`)

Retrieves a previously stored sequence.

## Value Generation (`build/one.php`)

For each iteration:

```php
$pqTries++;
if ( $pqTries > $pqTry ) return FALSE;  // Max attempts reached

// Generate value based on build type
if     ( pqStore($pqBuild) )    $pq = $pqLoop;
elseif ( $pqBuild == 'bool' )   $pq = pqBoolXxx($pqLoop, $pqParm);
elseif ( $pqBuild == 'function') $pq = pqXxx($pqLoop);
elseif ( $pqBuild == 'check' )  $pq = include PQ . "build/check.php";
elseif ( $pqBuild == 'loop' )   $pq = include PT . "$pqSeq/loop.php";
elseif ( $pqBuild == 'make' )   $pq = include PT . "$pqSeq/make.php";
elseif ( $pqBuild == 'order' )  $pq = include PT . "$pqSeq/order.php";

// Apply filters (plays)
if ( count($pqPlays) ) {
  include PQ . 'plays/plays.php';
  if ( $pq === FALSE ) return TRUE;  // Filtered out, continue
}

// Apply constraints
if ( $pq < $pqMin ) return TRUE;          // Below minimum
if ( $pq > $pqMax ) return TRUE;          // Above maximum
if ( $pqUnique && in_array($pq, $pqResult) ) return TRUE;  // Duplicate

// Add to result
$pqResult[] = $pq;

// Check stop conditions
if ( $pq >= $pqStop ) return FALSE;
if ( count($pqResult) >= $pqRows ) return FALSE;

return TRUE;  // Continue iteration
```

## Sequence Types

The 79 sequence types are organized in `types/` with this structure:

```
types/
├── prime/
│   ├── bool.php       # pqBoolPrime() - tests if n is prime
│   ├── function.php   # pqPrime() - generates primes
│   ├── generated.php  # PADprime constant with pre-computed values
│   ├── loop.php       # Returns next prime >= n
│   ├── make.php       # Finds next prime from n
│   └── flags/         # Type-specific flags
├── fibonacci/
│   ├── go.php         # Computes Fibonacci(n) from previous values
│   ├── generated.php  # PADfibonacci constant
│   └── flags/
├── triangular/
│   ├── bool.php       # Tests if n is triangular
│   ├── generated.php  # PADtriangular constant
│   └── flags/
...
```

### Type File Purposes

| File | Purpose |
|------|---------|
| `bool.php` | Boolean test function `pqBoolXxx($n)` |
| `function.php` | Generator function `pqXxx($n)` |
| `generated.php` | Pre-computed constant array `PADxxx` |
| `loop.php` | Value generator for loop build |
| `make.php` | Value generator for make build |
| `order.php` | Order-dependent generator (uses previous values) |
| `go.php` | Alternative generator |
| `init.php` | Type initialization |
| `flags/` | Type-specific configuration flags |

### Sequence Categories

**Arithmetic Operations:**
- `add` - Addition: n + parm
- `subtract` - Subtraction: n - parm
- `multiply` - Multiplication: n * parm
- `divide` - Division: n / parm
- `modulo` - Modulus: n % parm
- `exponentiation` - Power: n ^ parm
- `negation` - Negation: -n

**Logical Operations:**
- `and`, `or`, `xor`, `nand`, `nor`, `xnor`, `not`

**Number Properties:**
- `even` - Even numbers
- `odd` - Odd numbers
- `prime` - Prime numbers
- `composite` - Composite numbers
- `semiprime` - Products of exactly two primes
- `perfect` - Perfect numbers (sum of divisors = n)
- `palindrome` - Palindromic numbers
- `happy` - Happy numbers
- `harshad` - Harshad/Niven numbers
- `kaprekar` - Kaprekar numbers
- `emirp` - Primes that are different primes reversed
- `strong` - Strong primes
- `lucky` - Lucky numbers
- `polite` - Polite numbers
- `powerful` - Powerful numbers
- `antiprime` - Highly composite numbers

**Figurate Numbers:**
- `triangular` - 1, 3, 6, 10, 15...
- `square` - 1, 4, 9, 16, 25...
- `pentagonal` - 1, 5, 12, 22, 35...
- `hexagonal` - 1, 6, 15, 28, 45...
- `heptagonal`, `octagonal`, `decagonal`...
- `gnomonic` - 1, 3, 5, 7, 9...

**3D Figurate Numbers:**
- `tetrahedral` - Tetrahedral numbers
- `octahedral` - Octahedral numbers
- `cubic` - Cube numbers

**Famous Sequences:**
- `fibonacci` - 0, 1, 1, 2, 3, 5, 8...
- `lucas` - 2, 1, 3, 4, 7, 11...
- `tribonacci` - Tribonacci sequence
- `pell` - Pell numbers
- `catalan` - Catalan numbers
- `bell` - Bell numbers
- `perrin` - Perrin numbers
- `xpadovan` - Padovan sequence
- `sylvester` - Sylvester's sequence
- `recaman` - Recamán's sequence
- `golomb` - Golomb sequence
- `kolakoski` - Kolakoski sequence
- `gould` - Gould's sequence
- `newmanConway` - Newman-Conway sequence
- `moserdebruijn` - Moser-de Bruijn sequence

**Special:**
- `mersenne` - Mersenne numbers (2^n - 1)
- `cullen` - Cullen numbers
- `kynea` - Kynea numbers
- `caterer` - Lazy caterer's sequence
- `pronic` - Pronic numbers (n*(n+1))
- `biquadratic` - Fourth powers

**Utility:**
- `range` - Simple range: from..to
- `list` - Explicit list of values
- `loop` - Loop counter
- `step` - Step sequence
- `repeat` - Repeat value
- `random` - Random numbers
- `chance` - Random with probability
- `identity` - Identity (returns input)
- `eval` - Evaluate expression
- `oeis` - OEIS sequence lookup

**Rounding:**
- `ceil` - Ceiling
- `floor` - Floor
- `round` - Round
- `power` - Power of base
- `multiple` - Multiple of value

## Plays (Filters)

Plays filter or transform values before adding to results (`plays/plays.php`):

```php
foreach ( $pqPlays as $pqTmp ) {
  extract ( $pqTmp );
  $pqLoop = $pq;
  $pq = include PQ . "plays/play/$pqBuild.php";

  // Play modes:
  if ( $pqPlay == 'make' )   // Transform value
  if ( $pqPlay == 'keep' )   // Keep only matching
  if ( $pqPlay == 'remove' ) // Remove matching
  if ( $pqPlay == 'flag' )   // Return 1/0 flag
}
```

**Play Modes:**

| Mode | Returns TRUE | Returns FALSE | Returns other |
|------|--------------|---------------|---------------|
| `make` | Use loop value | Skip | Use returned value |
| `keep` | Keep value | Skip | Keep if matches loop |
| `remove` | Skip | Keep | Skip if matches loop |
| `flag` | Return 1 | Return 0 | 1 if matches, 0 otherwise |

## Actions

Actions transform the final result array (`actions/types/`):

### Aggregation
- `sum` - Sum of all values
- `product` - Product of all values
- `average` - Average value
- `median` - Median value
- `minimum` - Minimum value
- `maximum` - Maximum value
- `count` - Count of values

### Selection
- `first` - First N values
- `last` - Last N values
- `element` - Specific element
- `slice` - Slice of array
- `splice` - Splice array
- `pop` - Remove last
- `shift` - Remove first

### Modification
- `append` - Add to end
- `prepend` - Add to start
- `merge` - Merge arrays
- `combine` - Combine arrays

### Ordering
- `sort` - Sort ascending
- `reverse` - Reverse order
- `shuffle` - Random order
- `randomize` - Randomize

### Filtering
- `dedup` - Remove duplicates
- `distinct` - Distinct values only
- `trim` - Trim values
- `onlyNow` - Only current values
- `onlyStore` - Only stored values

### Set Operations
- `intersection` - Common values
- `difference` - Different values

## Random Support

The subsystem supports randomization at multiple levels:

### Random Parameters

Parameters can include ranges with `..`:
```
from=1..100      # Random from 1-100
increment=1..5   # Random increment 1-5
rows=5..10       # Random 5-10 rows
```

### Random Selection

With `randomly` option, values are selected randomly from the valid range instead of sequentially.

### Random Increment

With `increment=1...5` (three dots), the increment changes randomly each iteration.

## Pre-computed Values

Many sequences include pre-computed constants in `generated.php`:

```php
const PADfibonacci = [0,1,1,2,3,5,8,13,21,34,55,89,...];
const PADtriangular = [1,3,6,10,15,21,28,36,45,55,...];
const PADprime = [2,3,5,7,11,13,17,19,23,29,...];
```

These enable fast lookups for common sequences.

## Directory Structure

```
sequence/
├── sequence/
│   └── sequence.php     # Main entry point
│
├── inits/               # Initialization
│   ├── clear.php        # Clear state
│   ├── direct.php       # Direct entry setup
│   ├── limits.php       # Set iteration limits
│   ├── parms.php        # Parse parameters
│   ├── set.php          # Setup sequence type
│   └── vars.php         # Initialize variables
│
├── build/               # Sequence generation
│   ├── build.php        # Main build dispatcher
│   ├── check.php        # Check build type
│   ├── given.php        # Handle given build name
│   ├── include.php      # Include type files
│   ├── one.php          # Generate one value
│   ├── parm.php         # Parameter handling
│   ├── randomly/        # Random support
│   └── types/           # Build type handlers
│       ├── bool.php
│       ├── build.php
│       ├── check.php
│       ├── fixed.php
│       ├── function.php
│       ├── given.php
│       ├── loop.php
│       ├── make.php
│       ├── order.php
│       ├── pull.php
│       └── type/        # Type-specific handlers
│
├── plays/               # Filter system
│   ├── add.php          # Add play filter
│   ├── init.php         # Initialize plays
│   ├── inits.php        # Setup plays
│   ├── parm.php         # Play parameters
│   ├── plays.php        # Execute plays
│   └── play/            # Play type handlers
│
├── actions/             # Result transformations
│   ├── actions.php      # Execute actions
│   ├── assoc.php        # Associative handling
│   ├── function.php     # Function actions
│   ├── inits.php        # Setup actions
│   ├── merge.php        # Merge handling
│   ├── set.php          # Set actions
│   ├── double/          # Double-value actions
│   ├── merge/           # Merge actions
│   ├── negative/        # Negative handling
│   ├── order/           # Order actions
│   ├── parm/            # Parameterized actions
│   ├── single/          # Single-value actions
│   └── types/           # Action implementations
│
├── exits/               # Exit processing
│   ├── actions.php      # Final actions
│   ├── data.php         # Data storage
│   ├── done.php         # Mark done
│   ├── exit.php         # Exit handler
│   ├── exits.php        # Exit processing
│   ├── info.php         # Debug info
│   ├── return.php       # Return handling
│   ├── start.php        # Start processing
│   ├── extra/           # Extra processing
│   ├── info/            # Info generation
│   ├── return/          # Return handlers
│   └── store/           # Store handlers
│
├── options/             # Option handlers
│   └── types/           # Option types
│
└── types/               # 79 sequence types
    ├── add/
    ├── and/
    ├── antiprime/
    ├── bell/
    ... (79 types total)
```

## Example Usage

### Simple Range
```
{sequence '1..10'}
→ [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
```

### First 10 Primes
```
{sequence prime, rows=10}
→ [2, 3, 5, 7, 11, 13, 17, 19, 23, 29]
```

### Fibonacci up to 100
```
{sequence fibonacci, stop=100}
→ [0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89]
```

### Even Numbers with Sum
```
{sequence even, from=2, to=20, sum}
→ [110]
```

### Primes Filtered by Palindrome
```
{sequence prime, rows=20, keep, palindrome}
→ [2, 3, 5, 7, 11, 101, 131, 151, 181, 191...]
```

### Random Selection
```
{sequence prime, rows=5, randomly}
→ [17, 3, 41, 7, 23] (random primes)
```

## Boolean Functions

Each sequence type can have a boolean test function:

```php
function pqBoolPrime($n, $p=0) {
  if (gmp_prob_prime($n))
    return TRUE;
  else
    return FALSE;
}

function pqBoolEven($n, $p=0) {
  if ($n & 1)
    return FALSE;
  else
    return TRUE;
}

function pqBoolTriangular($num) {
  // Uses quadratic formula to check if n*(n+1)/2 = num
  $c = (-2 * $num);
  $d = 1 - (4 * $c);
  if ($d < 0) return false;
  $root = (-1 + sqrt($d)) / 2;
  return ($root > 0 && floor($root) == $root);
}
```

## State Variables

Key state variables during generation:

| Variable | Purpose |
|----------|---------|
| `$pqResult` | Result array |
| `$pqLoop` | Current loop value |
| `$pqTries` | Number of attempts |
| `$pqSeq` | Current sequence type |
| `$pqBuild` | Current build type |
| `$pqParm` | Current parameter |
| `$pqOrder` | Order array (for dependent sequences) |
| `$pqPlays` | Active plays (filters) |
| `$pqActions` | Active actions |
| `$pqFixed` | Fixed array (for fixed builds) |
