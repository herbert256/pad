# PAD Sequence Subsystem

The sequence subsystem is a powerful mathematical sequence generation and manipulation engine. It allows generating various mathematical sequences, storing them, and applying transformations.

## Basic Sequence Generation

```
{fibonacci rows=10}{$fibonacci} {/fibonacci}      # Fibonacci numbers
{prime rows=15}{$prime} {/prime}                  # Prime numbers
{sequence '1..10', name='n'}{$n} {/sequence}      # Range 1-10
{random minimal=1, maximal=100, rows=5}           # Random numbers
{list '5;2;8;1;9'}{$list} {/list}                 # Custom list
```

## Character/Letter Ranges

```
{sequence 'A..Z', name='letter'}{$letter} {/sequence}   # Full alphabet
{sequence 'a..e', name='c'}{$c} {/sequence}             # a, b, c, d, e
{sequence '0..9', name='digit'}{$digit} {/sequence}     # Digits as strings
```

## OEIS Integration (Online Encyclopedia of Integer Sequences)

```
{sequence 10, oeis=81}{$sequence} {/sequence}           # OEIS sequence A000081
{sequence 15, oeis=257360}{$sequence} {/sequence}       # OEIS sequence A257360
```

## Loop with from/to Parameters

```
{sequence loop, from=11, to=30, name='i'}{$i} {/sequence}
```

## Sequence Types (80+)

**Mathematical sequences:**
- `fibonacci`, `lucas`, `pell`, `tribonacci`, `catalan`, `bell`, `perrin`
- `prime`, `composite`, `perfect`, `mersenne`

**Figurate numbers:**
- `triangular`, `square`, `cubic`, `pentagonal`, `hexagonal`, `heptagonal`, `octagonal`
- `tetrahedral`, `octahedral`, `biquadratic`

**Filters:**
- `even`, `odd`, `happy`, `lucky`, `harshad`, `palindrome`
- `semiprime`, `powerful`, `polite`, `kaprekar`

**Arithmetic operations:**
- `add`, `subtract`, `multiply`, `divide`, `modulo`, `power`/`exponentiation`
- `ceil`, `floor`, `round`, `negation`

**Logical operations:**
- `and`, `or`, `xor`, `not`, `nand`, `nor`, `xnor`

**Generation:**
- `range` (e.g., `'1..10'`), `list` (e.g., `'1;5;3;8'`), `loop`, `random`, `repeat`
- `oeis` - fetch from Online Encyclopedia of Integer Sequences

## Storing and Retrieving Sequences

**IMPORTANT:** Store names cannot be the same as action names (e.g., can't use `push='first'` because `first` is an action).

```
{sequence '1..10', push='mySeq'}           # Store sequence
{pull:mySeq}{$sequence} {/pull:mySeq}      # Retrieve and iterate

# Use pull: prefix to avoid naming conflicts with app tags
{sequence 5, push='nums'}
{pull:nums} {$sequence} {/pull:nums}
```

## Sequence Tags

The sequence tags (`resume`, `pull`, `keep`, `remove`, `flag`, `make`) operate on stored sequences:

```
{sequence 5, push='mySeq'}           # Create and store sequence
{resume add=10}                       # Transform: add 10 to each value
{resume reverse}                      # Transform: reverse order
{pull:mySeq} {$sequence} {/pull:mySeq}  # Iterate stored sequence
```

**Note:** `{resume}` applies transformations to stored sequences. Use `{continue}` for skipping iterations (like PHP's continue).

## Sequence Variable Access

Use named sequences instead of level-based `$-1` syntax:
```
{sequence 5, name='n'}
  {$n}                    # Correct - use named variable
{/sequence}

{sequence 5}
  {$-1}                   # Avoid - level-based access can be fragile
{/sequence}
```

## Sequence Actions (Transformations)

**Order manipulation:**
```
{pull:nums reverse}       # Reverse order
{pull:nums sort}          # Sort ascending
{pull:nums shuffle}       # Randomize order
```

**Selection:**
```
{pull:nums first}         # First element
{pull:nums first=3}       # First 3 elements
{pull:nums last=3}        # Last 3 elements
{pull:nums shift=2}       # Remove first 2
{pull:nums pop=2}         # Remove last 2
{pull:nums element=5}     # Get 5th element
{pull:nums slice='3|4'}   # From position 3, length 4
```

**Negative selection (invert):**
```
{pull:nums first=5, negative}   # All EXCEPT first 5
{pull:nums last=5, negative}    # All EXCEPT last 5
```

**Trim operations:**
```
{pull:nums trim, both=5}              # Trim 5 from each end
{pull:nums trim, left=5}              # Trim 5 from left
{pull:nums trim, right=5}             # Trim 5 from right
{pull:nums trim, left=10, right=5}    # Different amounts each side
```

**Eval parameter (apply expression to each element):**
```
{pull:nums eval='* 10 | - 1'}    # Multiply by 10, subtract 1
{pull:nums eval='15 + @'}        # Add 15 to each (@ = current value)
```

**Aggregation:**
```
{pull:nums sum}           # Sum of all elements
{pull:nums product}       # Product of all elements
{pull:nums average}       # Mean value
{pull:nums median}        # Median value
{pull:nums minimum}       # Smallest value
{pull:nums maximum}       # Largest value
{pull:nums count}         # Number of elements
{pull:nums distinct}      # Count of unique values
{pull:nums dedup}         # Remove duplicates
```

**Multi-sequence operations:**
```
{sequence '1..5', push='seqA'}
{sequence '3..8', push='seqB'}
{pull:seqA append='seqB'}        # Add seqB to end
{pull:seqA prepend='seqB'}       # Add seqB to start
{pull:seqA combine='seqB'}       # Concatenate preserving duplicates
{pull:seqA merge='seqB'}         # Merge, remove duplicates
{pull:seqA intersection='seqB'}  # Elements in both
{pull:seqA difference='seqB'}    # In seqA but not seqB
{pull:seqA onlyNow='seqB'}       # Only in current sequence
{pull:seqA onlyStore='seqB'}     # Only in stored sequence
```

## The `resume` Tag

Applies transformations to a stored sequence without pulling it:
```
{sequence 25, push='mySeq'}
{resume add=100}           # Add 100 to each value
{resume reverse}           # Reverse order
{resume subtract=5}        # Subtract 5 from each
{pull:mySeq}{$sequence} {/pull:mySeq}
```

## Sequence Plays (keep, remove, make, flag)

Filter or transform sequences based on sequence types:
```
{pull mySeq, keep, even}     # Keep only even values
{pull mySeq, remove, odd}    # Remove odd values
{pull mySeq, make, prime}    # Transform using prime
{pull mySeq, flag, even}     # Mark even entries
```

## Special Syntax Rules

**Some sequences need `sequence:` prefix:**
```
{sequence:repeat 42, rows=5}{$repeat}{/sequence:repeat}
{sequence:even rows=10}{$even}{/sequence:even}
```

**Chance sequence needs a numeric parameter:**
```
{chance 4, rows=15}    # 1-in-4 chance (not {chance rows=15})
```

## Named vs Unnamed Sequences

Always prefer named sequences for clarity:
```
{sequence 5, name='i'}
  {$i}                  # Clear, explicit
{/sequence}

{sequence '1..3', name='row'}
  {sequence '1..4', name='col'}
    ({$row},{$col})
  {/sequence}
{/sequence}
```

## Common Sequence Patterns

**Generate and transform:**
```
{sequence '1..10', push='nums'}
{resume add=5}
{resume reverse}
{pull:nums sort}{$sequence} {/pull:nums}
```

**Combine sequences:**
```
{sequence '1..5', push='seqA'}
{sequence '10..15', push='seqB'}
{pull:seqA append='seqB'}{$sequence} {/pull:seqA}
```

**Aggregate values:**
```
{sequence '1..100', push='nums'}
Sum: {pull:nums sum}{$sequence}{/pull:nums}
Avg: {pull:nums average}{$sequence}{/pull:nums}
```
