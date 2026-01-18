# PAD Sequence Types

This document describes the sequence types available in PAD and how to generate them.

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

### Mathematical Sequences

- `fibonacci` - Fibonacci numbers (1, 1, 2, 3, 5, 8, 13, ...)
- `lucas` - Lucas numbers (2, 1, 3, 4, 7, 11, 18, ...)
- `pell` - Pell numbers (0, 1, 2, 5, 12, 29, ...)
- `tribonacci` - Tribonacci numbers (0, 0, 1, 1, 2, 4, 7, ...)
- `catalan` - Catalan numbers (1, 1, 2, 5, 14, 42, ...)
- `bell` - Bell numbers (1, 1, 2, 5, 15, 52, ...)
- `perrin` - Perrin numbers (3, 0, 2, 3, 2, 5, ...)

### Prime-Related

- `prime` - Prime numbers (2, 3, 5, 7, 11, 13, ...)
- `composite` - Composite numbers (4, 6, 8, 9, 10, ...)
- `perfect` - Perfect numbers (6, 28, 496, ...)
- `mersenne` - Mersenne numbers (3, 7, 31, 127, ...)
- `emirp` - Emirp primes (primes that are different primes when reversed)
- `strong` - Strong primes
- `semiprime` - Semiprimes (products of exactly two primes)

### Figurate Numbers

- `triangular` - Triangular numbers (1, 3, 6, 10, 15, ...)
- `square` - Square numbers (1, 4, 9, 16, 25, ...)
- `cubic` - Cubic numbers (1, 8, 27, 64, 125, ...)
- `pentagonal` - Pentagonal numbers (1, 5, 12, 22, 35, ...)
- `hexagonal` - Hexagonal numbers (1, 6, 15, 28, 45, ...)
- `heptagonal` - Heptagonal numbers (1, 7, 18, 34, 55, ...)
- `octagonal` - Octagonal numbers (1, 8, 21, 40, 65, ...)
- `decagonal` - Decagonal numbers
- `tetrahedral` - Tetrahedral numbers (1, 4, 10, 20, 35, ...)
- `octahedral` - Octahedral numbers (1, 6, 19, 44, 85, ...)
- `biquadratic` - Biquadratic numbers (1, 16, 81, 256, ...)
- `gnomonic` - Gnomonic numbers (1, 3, 5, 7, 9, ...)
- `pronic` - Pronic/oblong numbers (n*(n+1))
- `heptadecagonal` - Heptadecagonal numbers
- `enneadecagonal` - Enneadecagonal numbers
- `icosihenagonal` - Icosihenagonal numbers

### Filters

- `even` - Even numbers (2, 4, 6, 8, 10, ...)
- `odd` - Odd numbers (1, 3, 5, 7, 9, ...)
- `happy` - Happy numbers (1, 7, 10, 13, 19, ...)
- `lucky` - Lucky numbers (1, 3, 7, 9, 13, ...)
- `harshad` - Harshad/Niven numbers (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, ...)
- `palindrome` - Palindromic numbers (1, 2, ..., 9, 11, 22, ...)
- `powerful` - Powerful numbers (1, 4, 8, 9, 16, ...)
- `polite` - Polite numbers (3, 5, 6, 7, 9, ...)
- `kaprekar` - Kaprekar numbers (1, 9, 45, 55, ...)
- `antiprime` - Antiprime (highly composite) numbers

### Other Sequences

- `golomb` - Golomb sequence
- `gould` - Gould sequence
- `kolakoski` - Kolakoski sequence
- `kynea` - Kynea numbers
- `moserdebruijn` - Moser-de Bruijn sequence
- `newmanConway` - Newman-Conway sequence
- `recaman` - Recaman sequence
- `sylvester` - Sylvester sequence
- `cullen` - Cullen numbers
- `xpadovan` - Padovan sequence
- `caterer` - Lazy caterer's sequence

### Arithmetic Operations

- `add` - Addition
- `subtract` - Subtraction
- `multiply` - Multiplication
- `divide` - Division
- `modulo` - Modulo operation
- `power` / `exponentiation` - Power/exponentiation
- `ceil` - Ceiling function
- `floor` - Floor function
- `round` - Rounding
- `negation` - Negation

### Logical Operations

- `and` - Logical AND
- `or` - Logical OR
- `xor` - Logical XOR
- `not` - Logical NOT
- `nand` - Logical NAND
- `nor` - Logical NOR
- `xnor` - Logical XNOR

### Generation

- `range` - Range (e.g., `'1..10'`)
- `list` - Custom list (e.g., `'1;5;3;8'`)
- `loop` - Loop iteration
- `random` - Random numbers
- `repeat` - Repeat a value
- `oeis` - Fetch from Online Encyclopedia of Integer Sequences
- `identity` - Identity sequence (returns input)
- `step` - Step sequence
- `chance` - Chance/probability sequence

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

## See Also

- [ACTIONS.md](ACTIONS.md) - Sequence transformations and operations
- [README.md](README.md) - Sequence documentation overview
