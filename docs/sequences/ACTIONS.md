# PAD Sequence Actions

This document describes the actions and transformations available for PAD sequences.

An action runs over a sequence once it has been generated. There are 29 of them, listed in
the summary table at the end. Three other things are written the same way and are easy to
mistake for actions: **plays** (`add=5`, `keep`, `remove`) apply a sequence type to each
value as it is generated, and **options** (`rows`, `from`, `to`, `increment`) shape the
generation itself. They are covered in [SEQUENCES.md](SEQUENCES.md) and
[PAD.md](PAD.md).

## Order Manipulation

```
{pull:nums reverse}       # Reverse order
{pull:nums sort}          # Sort ascending
{pull:nums shuffle}       # Randomize order
{pull:nums randomize}     # Pick at random - see below, not the same as shuffle
```

`shuffle` reorders every value. `randomize` *picks*: bare it draws as many as there are, so
it looks like a shuffle, but `randomize=3` draws three. Three options change how it draws:

- `orderly` - the drawn values keep the order they had, instead of coming out shuffled
- `duplicates` - a value may be drawn more than once
- `atLeastOnce` - every value is drawn at least once before any is drawn twice, which
  implies `duplicates`. Asking for fewer than there are cannot satisfy that, and the count
  gives way: `randomize=2, atLeastOnce` over five values returns all five

`sort`, `dedup` and `reverse` take an optional store name, and then work on the union of the
two: `{pull:a sort='b'}` sorts a and b together rather than sorting a by a field named b.

## Selection

```
{pull:nums first}         # First element
{pull:nums first=3}       # First 3 elements
{pull:nums last=3}        # Last 3 elements
{pull:nums element=5}     # Get 5th element, counting from 1
{pull:nums slice='3|4}    # From offset 3, length 4 - counting from 0
{pull:nums minimum}       # Smallest value
{pull:nums minimum=3}     # The 3 smallest, back in their original order
{pull:nums maximum=3}     # The 3 largest
{pull:nums shift=2}       # See "Destructive selection" below
{pull:nums pop=2}         # See "Destructive selection" below
```

`element` counts from 1, as `from` and `to` do. `slice` and `splice` count from 0, because
they are PHP's array functions underneath.

`left=N` and `right=N` are **not** selectors on their own - they do nothing unless `trim` is
also given, where they say how much to take off each end. See Trim Operations.

A position the sequence does not reach - `element=10` of three values - gives nothing rather
than an error.

## Destructive selection

`shift` and `pop` take values *out of the stored sequence*. What the tag shows is the part
removed, and what stays in the store is the remainder:

```
{sequence '1..10', push='nums'}
{pull:nums shift=2}{$sequence} {/pull:nums}   # shows 1 2
{pull:nums}{$sequence} {/pull:nums}           # the store now holds 3 4 5 6 7 8 9 10
```

## Negative Selection (Invert)

```
{pull:nums first=5, negative}   # All EXCEPT first 5
{pull:nums last=5, negative}    # All EXCEPT last 5
```

`negative` inverts what an **action** picked, and is applied after each action in turn. It
does nothing to a play: `keep='prime', negative` is not the complement of `keep='prime'` -
`remove='prime'` is.

## Trim Operations

`trim` *removes* values from the ends. `both`, `left` and `right` say how many, and mean
nothing without it.

```
{pull:nums trim, both=5}              # Trim 5 from each end
{pull:nums trim, left=5}              # Trim 5 from left
{pull:nums trim, right=5}             # Trim 5 from right
{pull:nums trim, left=10, right=5}    # Different amounts each side
{pull:nums trim=2}                    # 2 from each end - a value on trim itself fills both
```

A count on `trim` itself fills `both`, and `left` and `right` are then taken off on top of
it: `trim=2, left=3` removes five from the left and two from the right. A count of 0 removes
nothing.

## Eval Parameter

Apply an expression to each element:

```
{pull:nums eval='* 10 | - 1'}    # Multiply by 10, subtract 1
{pull:nums eval='15 + @'}        # Add 15 to each (@ = current value)
```

## Aggregation

```
{pull:nums sum}           # Sum of all elements
{pull:nums product}       # Product of all elements
{pull:nums average}       # Mean value
{pull:nums median}        # Median: values sorted, middle one, or the mean of the two middles
{pull:nums count}         # Number of elements
{pull:nums distinct}      # Count of unique values
{pull:nums dedup}         # Remove duplicates
```

Over an empty sequence, `count`, `sum`, `distinct` and `product` answer 0, 0, 0 and 1, while
`average`, `median` and `element` have no answer to give and leave it empty.

`unique` is not an action. It is a generation option that drops a repeated value as the
sequence is built, so it runs before any action does.

## Arithmetic Operations (plays, not actions)

These are sequence types applied to each value as it is generated, so they run *before* the
actions do. A parameter that is not a number is reported; `divide` will not take a zero.

```
{pull:nums add=5}             # Add 5 to each element
{pull:nums subtract=3}        # Subtract 3 from each
{pull:nums multiply=2}        # Multiply each by 2
{pull:nums divide=4}          # Divide each by 4
{pull:nums modulo=3}          # Modulo 3 of each
{pull:nums power=2}           # Square each element
{pull:nums exponentiation=3}  # Cube each element
{pull:nums ceil}              # Ceiling of each
{pull:nums floor}             # Floor of each
{pull:nums round}             # Round each
{pull:nums negation}          # Negate each
```

`increment` is not an action either - it is the option that sets the step a sequence counts
in, and has no effect written after a pull.

## Bitwise Operations (plays, not actions)

These work on the bits of each value, not on true and false:

```
{pull:nums and=12}    # Bitwise AND with 12
{pull:nums or=5}      # Bitwise OR
{pull:nums not}       # Bitwise NOT
{pull:nums nand=12}   # Bitwise NAND - runs negative
{pull:nums nor=5}     # Bitwise NOR - runs negative
{pull:nums xor=3}     # Bitwise XOR
{pull:nums xnor=3}    # Bitwise XNOR - runs negative
```

## Multi-Sequence Operations

With `seqA` holding 1 to 6 and `seqB` holding 4 to 9:

```
{sequence '1..6', push='seqA'}
{sequence '4..9', push='seqB'}
{pull:seqA append='seqB'}        # 1 2 3 4 5 6 4 5 6 7 8 9  - seqB on the end
{pull:seqA prepend='seqB'}       # 4 5 6 7 8 9 1 2 3 4 5 6  - seqB in front
{pull:seqA combine='seqB'}       # 1 2 3 4 4 5 5 6 6 7 8 9  - in order, duplicates kept
{pull:seqA merge='seqB'}         # 1 2 3 4 5 6 7 8 9        - in order, duplicates dropped
{pull:seqA intersection='seqB'}  # 4 5 6                    - in both
{pull:seqA difference='seqB'}    # 1 2 3 7 8 9              - in one but not both
{pull:seqA onlyNow='seqB'}       # 1 2 3                    - in seqA only
{pull:seqA onlyStore='seqB'}     # 7 8 9                    - in seqB only
```

`difference` is the symmetric difference: the values only in the sequence, followed by the
values only in the store. For the one-sided operation use `onlyNow`.

Every one of these needs a store to work with. Named without one, or named with a store that
was never pushed, they leave the sequence as it is.

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

## Common Patterns

### Generate and Transform

```
{sequence '1..10', push='nums'}
{resume add=5}
{resume reverse}
{pull:nums sort}{$sequence} {/pull:nums}
```

### Combine Sequences

```
{sequence '1..5', push='seqA'}
{sequence '10..15', push='seqB'}
{pull:seqA append='seqB'}{$sequence} {/pull:seqA}
```

### Aggregate Values

```
{sequence '1..100', push='nums'}
Sum: {pull:nums sum}{$sequence}{/pull:nums}
Avg: {pull:nums average}{$sequence}{/pull:nums}
```

## Action Summary Table

The 29 actions, and nothing else - `from`, `to`, `rows`, `increment` and `unique` are
generation options, and the arithmetic and bitwise entries above are plays.

| Action | Description |
|--------|-------------|
| `reverse` | Reverse order. With a store name, reverses the union |
| `sort` | Sort ascending. With a store name, sorts the union |
| `shuffle` | Reorder at random |
| `randomize` / `randomize=N` | Draw N at random, all of them when bare |
| `first` / `first=N` | Keep the first N, 1 when bare |
| `last` / `last=N` | Keep the last N |
| `element=N` | The Nth value, counting from 1; nothing if there is no Nth |
| `slice='pos\|len'` | From offset pos, counting from 0, for len values |
| `splice='pos\|len'` or `'pos\|len\|seq'` | Remove len values at pos, optionally putting a store in their place |
| `shift=N` | Take the first N out of the store and show them |
| `pop=N` | Take the last N out of the store and show them |
| `trim` | Remove from the ends, by `both` / `left` / `right` |
| `negative` | Invert whatever the action just selected |
| `sum` | Sum of the values, 0 when empty |
| `product` | Product of the values, 1 when empty |
| `average` | Mean; nothing when empty |
| `median` | Values sorted, the middle one or the mean of the two middles |
| `minimum` / `minimum=N` | The smallest, or the N smallest in their original order |
| `maximum` / `maximum=N` | The largest, or the N largest |
| `count` | How many values, 0 when empty |
| `distinct` | How many different values |
| `dedup` | Remove duplicates. With a store name, dedups the union |
| `append='seq'` | Add a store's values to the end |
| `prepend='seq'` | Add a store's values to the front |
| `merge='seq'` | Merge in order, dropping duplicates |
| `combine='seq'` | Merge in order, keeping duplicates |
| `intersection='seq'` | Values in both |
| `difference='seq'` | Values in one or the other but not both |
| `onlyNow='seq'` | Values in the sequence but not the store |
| `onlyStore='seq'` | Values in the store but not the sequence |

## Type Prefixes for Sequences

| Prefix | Purpose | Example |
|--------|---------|---------|
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `action:` | Sequence action | `{action:reverse}` |

## See Also

- [SEQUENCES.md](SEQUENCES.md) - Sequence types and generation
- [README.md](README.md) - Sequence documentation overview
