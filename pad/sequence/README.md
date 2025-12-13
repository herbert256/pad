# Sequence/Iteration Support

This module provides powerful sequence generation capabilities for the PAD framework, supporting mathematical sequences, number theory sequences, random generation, and array operations.

## Overview

The sequence module (internally prefixed with `pq`) enables templates to generate and manipulate numeric sequences like Fibonacci, primes, ranges, and many specialized mathematical sequences. It provides a comprehensive system for building sequences with customizable parameters (from/to, increment, limits), applying transformations (sort, reverse, dedup), and extracting results (sum, average, count).

## Directory Structure

### Core Components

- **inits/** - Initialization and parameter handling
  - Parse sequence parameters (from, to, increment, rows, etc.)
  - Find and validate sequence definitions
  - Set up loop limits and constraints

- **build/** - Sequence construction
  - `given.php` - Process named sequences
  - `parm.php` - Parameter extraction
  - `store.php` - Store built sequences
  - `vars.php` - Variable management
  - `randomly/` - Random sequence building

- **exits/** - Result extraction and output
  - `data.php` - Return sequence data
  - `return.php` - Main return logic
  - `store/` - Store results to variables
  - `extra/` - Additional processing (chain, actions, etc.)

- **plays/** - Sequence execution
  - `parm.php` - Play parameters
  - `play/` - Different play modes (bool, function, order)

### Sequence Types (types/)

Over 70 mathematical and number theory sequences including:

**Arithmetic Sequences**
- **add**, **multiply**, **divide**, **substract**, **modulo** - Basic operations
- **exponentiation**, **power** - Exponential sequences
- **repeat**, **step** - Simple patterns

**Number Properties**
- **even**, **odd** - Parity-based sequences
- **prime**, **antiprime**, **emirp**, **semiprime** - Prime-related
- **composite** - Composite numbers
- **square**, **cubic**, **biquadratic** - Power sequences
- **palindrome** - Palindromic numbers
- **happy**, **lucky** - Special number properties

**Famous Sequences**
- **fibonacci**, **lucas**, **tribonacci** - Fibonacci family
- **catalan** - Catalan numbers
- **bell** - Bell numbers
- **pell** - Pell numbers
- **perrin** - Perrin sequence
- **golomb**, **kolakoski** - Self-describing sequences

**Polygonal Numbers**
- **triangular**, **square**, **pentagonal**, **hexagonal**, **heptagonal**, **octagonal**
- **decagonal**, **heptadecagonal**, **enneadecagonal**, **icosihenagonal**
- **gnomonic**, **pronic**

**3D Figurate Numbers**
- **tetrahedral**, **octahedral**, **caterer**

**Other Sequences**
- **recaman**, **sylvester**, **newman-conway** - Recursive sequences
- **mersenne**, **cullen**, **kynea** - Named after mathematicians
- **perfect**, **powerful**, **polite**, **harshad**, **kaprekar** - Special properties
- **oeis** - Reference OEIS sequences

**Utilities**
- **range**, **list**, **loop** - Basic iteration
- **random**, **chance** - Random number generation
- **identity**, **eval** - Special functions

**Logical Operations**
- **and**, **or**, **xor**, **nand**, **nor**, **xnor**, **not**, **negation** - Boolean logic
- **ceil**, **floor**, **round** - Rounding operations

### Actions (actions/)

Transform and analyze sequences:

**Aggregation**
- **sum.php** - Sum all values
- **average.php** - Calculate mean
- **product.php** - Multiply all values
- **count.php** - Count elements
- **median.php** - Find median

**Selection**
- **first.php** - Get first element
- **last.php** - Get last element
- **trim.php** - Remove elements
- **splice.php** - Extract/insert elements

**Transformation**
- **sort.php** - Sort ascending
- **reverse.php** - Reverse order
- **shuffle.php**, **randomize.php** - Random reordering
- **shift.php** - Rotate elements
- **dedup.php**, **distinct.php** - Remove duplicates

**Combination**
- **append.php**, **prepend.php** - Add elements
- **combine.php** - Merge sequences

**Ordering**
- **order/keys.php**, **order/values.php** - Order by keys or values
- **negative/keys.php**, **negative/values.php** - Negative ordering

**Structure**
- **assoc.php** - Create associative arrays
- **set.php** - Set operations

### Options (options/types/)

Configure sequence generation:

**Range Control**
- **from.php**, **to.php** - Define range boundaries
- **min.php**, **max.php** - Value constraints
- **increment.php** - Step size

**Limit Control**
- **rows.php** - Maximum number of elements
- **stop.php** - Stop condition
- **skip.php** - Skip initial elements
- **sole.php** - Single value mode

**Behavior**
- **atLeastOnce.php** - Ensure minimum execution
- **try.php** - Attempt count
- **operation.php** - Operation mode
- **single.php** - Single element mode

**Ordering**
- **orderly.php** - Ordered processing
- **negative.php** - Reverse ordering
- **left.php**, **right.php** - Direction control

**Duplicates**
- **unique.php** - Remove duplicates
- **duplicates.php** - Handle duplicates
- **keep.php** - Keep specific elements
- **remove.php** - Remove specific elements

**Actions & Storage**
- **action.php** - Apply action
- **build.php** - Build mode
- **make.php** - Creation mode
- **store.php** - Store result
- **toData.php** - Output to data variable
- **name.php** - Named sequence
- **pull.php**, **push.php** - Stack operations

**Special**
- **both.php** - Both keys and values
- **count.php** - Count mode
- **randomly.php** - Random mode

## Key Features

### Flexible Sequence Generation
- Generate any mathematical sequence by type name
- Customize range (from/to), increment, and limits
- Support for both ordered and random generation
- Named sequences for reuse

### Powerful Transformations
- Chain multiple actions (sort, reverse, dedup, etc.)
- Aggregate operations (sum, average, count)
- Array manipulation (append, prepend, combine)
- Extract subsets (first, last, splice)

### Advanced Parameters
- **from/to**: Define range boundaries
- **increment**: Step size between values
- **rows**: Limit number of results
- **stop**: Maximum value constraint
- **skip**: Skip initial values
- **try**: Attempt limits for validation
- **unique**: Remove duplicate values
- **negative**: Reverse ordering
- **randomly**: Random selection mode

### Storage and Reuse
- Store sequences in named variables (build)
- Save results to data store (toData)
- Pull/push stack operations
- Reference built sequences

## Integration with PAD Framework

The sequence module extends PAD's template capabilities:

1. **Template Tags**: Sequences accessible via template tags with parameters
2. **Data Generation**: Generate data arrays for iteration
3. **Calculations**: Perform mathematical operations in templates
4. **Random Data**: Generate random datasets for testing/demos
5. **Number Theory**: Access mathematical sequences for specialized applications

## Usage Pattern

Sequences are invoked through PAD template tags:
```
{sequence type="fibonacci" from="1" to="10"}
{sequence type="prime" rows="20" action="sum"}
{sequence type="range" from="1" to="100" increment="5" action="average"}
{sequence type="random" from="1" to="100" rows="10" unique="true"}
```

Parameters control generation, actions transform results, and outputs can be stored for later use.

## Example Workflows

1. **Generate Fibonacci sequence**: Type=fibonacci, from=1, to=10
2. **Sum first 20 primes**: Type=prime, rows=20, action=sum
3. **Random unique numbers**: Type=random, from=1, to=100, rows=10, unique=true
4. **Even numbers descending**: Type=even, from=2, to=20, negative=true
5. **Average of range**: Type=range, from=1, to=100, action=average

This module demonstrates PAD's extensibility, providing a complete mathematical sequence engine within the template system.
