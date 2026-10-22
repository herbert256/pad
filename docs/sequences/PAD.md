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

122 mathematical and number theory sequences, including:

**Arithmetic Sequences**
- **add**, **multiply**, **divide**, **subtract**, **modulo** - Basic operations
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
- **factorial**, **partition**, **derangement**, **motzkin** - Counting sequences
- **jacobsthal**, **leonardo**, **narayana** - Linear recurrences

**Polygonal Numbers**
- **triangular**, **square**, **pentagonal**, **hexagonal**, **heptagonal**, **octagonal**
- **decagonal**, **heptadecagonal**, **enneadecagonal**, **icosihenagonal**
- **gnomonic**, **pronic**
- **star**, **centeredSquare**, **centeredHexagonal** - Centered numbers

**3D Figurate Numbers**
- **tetrahedral**, **octahedral**, **caterer**
- **pentatope** - The fourth dimension

**Other Sequences**
- **recaman**, **sylvester**, **newmanConway** - Recursive sequences
- **vanEck**, **stern**, **thueMorse**, **collatz** - Self-referring and binary sequences
- **mersenne**, **cullen**, **kynea** - Named after mathematicians
- **woodall**, **thabit**, **fermat**, **repunit**, **primorial** - More named forms
- **perfect**, **powerful**, **polite**, **harshad**, **kaprekar** - Special properties
- **totient**, **divisors**, **sigma** - Divisor functions
- **abundant**, **deficient**, **squarefree**, **smith**, **sphenic**, **carmichael**, **amicable** - Properties of the factors
- **hamming**, **perfectPower**, **twinPrime**, **sophieGermain**, **safePrime** - Smooth numbers, powers, prime pairs
- **digitSum**, **digitalRoot**, **reversal**, **narcissistic**, **automorphic**, **evil**, **odious** - Digits and bits
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
- **shift.php** - Take the first N out of the store
- **dedup.php** - Remove duplicates; **distinct.php** - count the different values

**Combination**
- **append.php**, **prepend.php** - Add elements
- **combine.php** - Merge sequences

**Ordering**
- **order/order.php**, **order/keys.php** - Put the survivors of minimum and maximum back in the order they had, by key
- **negative/keys.php**, **negative/values.php** - Negative ordering

**Structure**
- **assoc.php** - Create associative arrays
- **set.php** - Set up an action called from an expression

### Options (options/types/)

Configure sequence generation:

**Range Control**
- **from.php**, **to.php** - Define range boundaries
- **minimal.php**, **maximal.php** - Value constraints
- **increment.php** - Step size

**Limit Control**
- **rows.php** - Maximum number of elements
- **stop.php** - Stop condition
- **skip.php** - Skip the first N candidates offered
- **sole.php** - Single value mode

**Behavior**
- **atLeastOnce.php** - Ensure minimum execution
- **try.php** - Attempt count
- **operation.php** - Operation mode
- **single.php** - Single element mode

**Ordering**
- **orderly.php** - Ordered processing
- **negative.php** - Invert an action's selection
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
- **both.php** - How many trim takes off each end
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
- **from/to**: Define range boundaries - see "What from and to count" below
- **increment**: Step size between values, 1 or more - a range (`'0..1'`) also counts in a step below 1
- **rows**: Limit number of results
- **page**: One page of the terms, as on a data tag - `rows` is the page's size, 10 when not given, a page below 1 or no number is page 1, and `{pager 'sequence'}` - or the name of the type, the store or the tag, `{pager 'prime'}`, `{pager 'keep'}`, or the `name=` given - writes the links to the other pages. The pager of a range, a list or a store counts all its terms, made only up to the page shown; of any other run - a type, or a list with a play, `unique`, `min`/`max`, `stop`, `try` or `randomly` - it knows the pages made, up to the one after the page shown, so it has a next page while there are more. In the order written, as among a data tag's options: an action before `page` acts on every term and the page is cut from its result - `{sequence '5;3;9;1;7;2', sort, page=1, rows=3}` is 1 2 3 - one after it acts on the page. A type without an end of its own - no `to` or `stop` - has no every term: it is cut to its page first, wherever `page` is written - `{sequence 'prime', reverse, page=2, rows=5}` is 29 23 19 17 13 - unless every action before `page` keeps a front of the run (`first`, `shift`, `element`, `dedup`, `intersection`, `onlyNow`, `prepend`): `{sequence 'prime', first=20, page=5, rows=5}` is past the twenty, no terms
- **stop**: Maximum value constraint
- **skip**: Skip the first N candidates offered, not the first N kept
- **try**: Attempt limits for validation
- **unique**: Remove duplicate values
- **negative**: Invert an action's selection - `first=5, negative` gives all but the first five. It applies to actions only; a play is inverted by writing `remove` where you would write `keep`
- **randomly**: Random selection mode

### What from and to count

`from` and `to` bound the loop the sequence is generated by, and what that loop counts
depends on how the type produces its terms:

- **Types that test candidates** decide whether each number in turn belongs to the
  sequence, so the loop counts **values** and `to` is an upper bound on the terms
  themselves. `{sequence prime, to=20}` gives the primes up to 20, and
  `{sequence triangular, to=20}` gives `1 3 6 10 15`.
- **Types that compute the nth term** from a formula or from earlier terms are handed the
  position, so the loop counts **terms** and `to` is how many are produced.
  `{sequence square, to=20}` gives twenty squares, ending at 400, and
  `{sequence fibonacci, to=20}` gives twenty Fibonacci numbers.
- **Types with a ready-made list** - `perfect`, `mersenne`, `kolakoski`, and any stored
  sequence read back with `pull` - are indexed by position too, so `to` is a position in
  that list.

`rows` counts produced terms whichever kind is in play, so it is the parameter to reach
for when a fixed number of terms is what is wanted, and `stop` always names a value.
Which kind a type is follows from the files in its directory: a `bool.php` predicate tests
values, while `function.php`, `order.php` and `fixed.php` are given a position, as most
`loop.php` files are - the exceptions test or step through the values themselves: `prime`
and `emirp` test the loop value, `multiple` and `step` step through the values by their
parameter, so `from` and `to` bound their terms. A type that is generated by position and
also has a `bool.php` - golomb, gould, square and others - keeps generating by position;
the predicate is what `keep`, `remove` and `flag` ask.

`build=` picks the other kind for one run, and two of the builds serve every type:

- `build='bool'` walks the values and keeps the ones that belong, so `from` and `to` name
  values: `{sequence fibonacci, build='bool', to=100}` gives `1 2 3 5 8 13 21 34 55 89`, each
  value once. A type without a `bool.php` is asked through its membership check.
- `build='function'` gives the term at each position, so `from` and `to` count terms:
  `{sequence happy, build='function', from=5, to=8}` gives the 5th to 8th happy numbers,
  `19 23 28 31`. A type that tests values has its nth value found by walking them, kept for
  the rest of the request; one that steps through them by itself - `step`, `even`, `odd`,
  `multiple` - has it worked out from its first terms, at any position:
  `{sequence step=3, build='function', from=2, to=4}` gives `4 7 10`.

### Storage and Reuse
- Store sequences in named stores (push, name)
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

Sequences are invoked through PAD template tags. The type is named directly - there is no
`type=` attribute - and options follow it as a comma-separated list:

```
{sequence fibonacci, from=1, to=10}{$sequence} {/sequence}
{sequence prime, rows=20, sum}{$sequence}{/sequence}
{sequence range='1..100', increment=5, average}{$sequence}{/sequence}
{sequence random, minimal=1, maximal=100, rows=10, unique}{$sequence} {/sequence}
```

Parameters control generation, actions transform results, and outputs can be stored for later use.

## Example Workflows

1. **Generate Fibonacci numbers** - `{sequence fibonacci, from=1, to=10}` gives ten of them,
   0 1 1 2 3 5 8 13 21 34
2. **Sum the first 20 primes** - `{sequence prime, rows=20, sum}` gives 639
3. **Random unique numbers** - `{sequence random, minimal=1, maximal=100, rows=10, unique}`
4. **Even numbers descending** - `{sequence even, to=10, reverse}` gives 20 18 16 ... 2.
   Descending order comes from the `reverse` action; `negative` inverts an action's
   selection and does not reorder anything
5. **Average of a range** - `{sequence range='1..100', average}` gives 50.5

This module demonstrates PAD's extensibility, providing a complete mathematical sequence engine within the template system.
