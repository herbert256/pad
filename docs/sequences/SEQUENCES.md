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

The A-number is written without its leading zeroes. The terms come from
`pad/sequence/types/oeis/oeis.sqlite`, one row per A-number, read a sequence at a time - so
a request that asks for one costs a lookup rather than the whole table. An A-number the
table does not hold produces no terms. `oeis/build.php` regenerates the table from the
`stripped` bulk download published by oeis.org.

## Loop with from/to Parameters

```
{sequence loop, from=11, to=30, name='i'}{$i} {/sequence}
```

What `from` and `to` count depends on how the type produces its terms - values for the ones
that test candidates, positions for the ones that compute the nth term. `rows` always counts
terms and `stop` always names a value. See
[PAD.md](PAD.md#what-from-and-to-count) for which types are which.

## Naming a Sequence

A name that is not a sequence type, a stored sequence or an action is reported as an error.
It used to be answered with a plain 1, 2, 3, ... counter, which looked enough like a
sequence to be mistaken for one, so a typo went unnoticed:

```
{sequence prime, rows=5}     # the primes
{sequence prmie, rows=5}     # error: 'prmie' is not a sequence type, a store or an action
```

## Sequence Types (80+)

### Mathematical Sequences

- `fibonacci` - Fibonacci numbers (0, 1, 1, 2, 3, 5, 8, 13, ...)
- `lucas` - Lucas numbers (1, 3, 4, 7, 11, 18, 29, ...)
- `pell` - Pell numbers (1, 2, 5, 12, 29, 70, ...)
- `tribonacci` - Tribonacci numbers (0, 0, 1, 1, 2, 4, 7, ...)
- `catalan` - Catalan numbers (1, 2, 5, 14, 42, 132, ...)
- `bell` - Bell numbers (1, 2, 5, 15, 52, 203, ...)
- `perrin` - Perrin numbers (3, 0, 2, 3, 2, 5, ...)

Where a sequence is conventionally listed with a leading term for n = 0, PAD starts at
n = 1 and does not produce it: `lucas` begins at 1 rather than 2, and `pell`, `catalan`
and `bell` omit their leading 1. `fibonacci`, `tribonacci`, `perrin` and `recaman` do
include their n = 0 term. Every value produced is a genuine member either way - only where
the listing starts differs.

### Prime-Related

- `prime` - Prime numbers (2, 3, 5, 7, 11, 13, ...)
- `composite` - Composite numbers (4, 6, 8, 9, 10, ...)
- `perfect` - Perfect numbers (6, 28, 496, ...)
- `mersenne` - Mersenne primes, the 2^p - 1 that are themselves prime (3, 7, 31, 127, 8191, ...) - not the Mersenne numbers, which include 1, 15, 63 and the rest
- `emirp` - Emirp primes (primes that are different primes when reversed)
- `strong` - Strong numbers / factorions, equal to the sum of the factorials of their own digits (1, 2, 145, 40585) - not the strong primes
- `semiprime` - Semiprimes (products of exactly two primes)

### Figurate Numbers

- `triangular` - Triangular numbers (1, 3, 6, 10, 15, ...)
- `square` - Square numbers (1, 4, 9, 16, 25, ...)
- `cubic` - Cubic numbers (1, 8, 27, 64, 125, ...)
- `pentagonal` - Pentagonal numbers (1, 5, 12, 22, 35, ...)
- `hexagonal` - Hexagonal numbers (1, 6, 15, 28, 45, ...)
- `heptagonal` - Heptagonal numbers (1, 7, 18, 34, 55, ...)
- `octagonal` - Octagonal numbers (1, 8, 21, 40, 65, ...)
- `decagonal` - Decagonal numbers (1, 10, 27, 52, 85, ...)
- `tetrahedral` - Tetrahedral numbers (1, 4, 10, 20, 35, ...)
- `octahedral` - Octahedral numbers (1, 6, 19, 44, 85, ...)
- `biquadratic` - Biquadratic numbers (1, 16, 81, 256, ...)
- `gnomonic` - Gnomonic numbers (1, 3, 5, 7, 9, ...)
- `pronic` - Pronic/oblong numbers (n*(n+1))
- `heptadecagonal` - Heptadecagonal numbers (1, 17, 48, 94, 155, ...)
- `enneadecagonal` - Enneadecagonal numbers (1, 19, 54, 106, 175, ...)
- `icosihenagonal` - Icosihenagonal numbers (1, 21, 60, 118, 195, ...)

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
- `antiprime` - Antiprime (highly composite) numbers (1, 2, 4, 6, 12, 24, 36, ...)

### Other Sequences

- `golomb` - Golomb sequence (1, 2, 2, 3, 3, 4, 4, 4, ...)
- `gould` - Gould sequence, odd entries in row n-1 of Pascal's triangle (1, 2, 2, 4, 2, 4, 4, 8, ...)
- `kolakoski` - Kolakoski sequence (1, 2, 2, 1, 1, 2, 1, 2, ...)
- `kynea` - Kynea numbers (7, 23, 79, 287, 1087, ...)
- `moserdebruijn` - Moser-de Bruijn sequence, sums of distinct powers of four (1, 4, 5, 16, 17, 20, 21, 64, ...)
- `newmanConway` - Newman-Conway sequence (1, 1, 2, 2, 3, 4, 4, 4, ...)
- `recaman` - Recaman sequence (0, 1, 3, 6, 2, 7, 13, 20, ...)
- `sylvester` - Sylvester sequence (2, 3, 7, 43, 1807, ...)
- `cullen` - Cullen numbers, n*2^n+1 (3, 9, 25, 65, 161, ...)
- `xpadovan` - Padovan sequence (1, 1, 1, 2, 2, 3, 4, 5, ...)
- `caterer` - Lazy caterer's sequence (2, 4, 7, 11, 16, 22, ...)

### Arithmetic Operations

Each takes a number as its parameter and applies it to the loop value, so they are most
often used as plays over another sequence - `{make multiply=3}` - rather than generated on
their own. A parameter that is not a number is reported rather than passed on, `divide` and
`multiple` will not take a zero, and `chance` needs 1 or more.

- `add=N` - Addition (with N = 3: 4, 5, 6, 7, ...)
- `subtract=N` - Subtraction
- `multiply=N` - Multiplication (with N = 3: 3, 6, 9, 12, ...)
- `divide=N` - Division
- `modulo=N` - Modulo, of a fractional parameter too (with N = 3: 1, 2, 0, 1, 2, 0, ...)
- `power=N` - N raised to the loop value (with N = 3: 3, 9, 27, 81, ...)
- `exponentiation=N` - the loop value raised to N (with N = 3: 1, 8, 27, 64, ...)
- `ceil=N` - Rounded up to a multiple of N (with N = 3: 3, 3, 3, 6, 6, 6, ...)
- `floor=N` - Rounded down to a multiple of N
- `round=N` - Rounded to the nearest multiple of N
- `multiple=N` - The multiples of N (with N = 3: 3, 6, 9, 12, ...)
- `step=N` - Counts in steps of N (with N = 3: 1, 4, 7, 10, ...)
- `negation` - Negation (-1, -2, -3, -4, ...)

### Bitwise Operations

These work on the bits of the loop value and the parameter, not on true and false - `and=3`
gives 1, 2, 3, 0, 1, 2, 3, 0, which is n AND 3 rather than a logical answer. The four that
complement their result run negative.

- `and=N` - Bitwise AND (with N = 3: 1, 2, 3, 0, ...)
- `or=N` - Bitwise OR (with N = 3: 3, 3, 3, 7, ...)
- `xor=N` - Bitwise XOR (with N = 3: 2, 1, 0, 7, ...)
- `not` - Bitwise NOT (-2, -3, -4, -5, ...)
- `nand=N` - Bitwise NAND (with N = 3: -2, -3, -4, -1, ...)
- `nor=N` - Bitwise NOR (with N = 3: -4, -4, -4, -8, ...)
- `xnor=N` - Bitwise XNOR (with N = 3: -3, -2, -1, -8, ...)

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
