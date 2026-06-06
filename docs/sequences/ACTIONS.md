# PAD Sequence Actions

This document describes the actions and transformations available for PAD sequences.

## Order Manipulation

```
{pull:nums reverse}       # Reverse order
{pull:nums sort}          # Sort ascending
{pull:nums shuffle}       # Randomize order
{pull:nums randomize}     # Alias for shuffle
```

## Selection

```
{pull:nums first}         # First element
{pull:nums first=3}       # First 3 elements
{pull:nums last=3}        # Last 3 elements
{pull:nums left=5}        # First 5 elements
{pull:nums right=5}       # Last 5 elements
{pull:nums from=3}        # From position 3
{pull:nums to=7}          # To position 7
{pull:nums shift=2}       # Remove first 2
{pull:nums pop=2}         # Remove last 2
{pull:nums element=5}     # Get 5th element
{pull:nums slice='3|4'}   # From position 3, length 4
{pull:nums rows=10}       # Limit to 10 rows
```

## Negative Selection (Invert)

```
{pull:nums first=5, negative}   # All EXCEPT first 5
{pull:nums last=5, negative}    # All EXCEPT last 5
```

## Trim Operations

```
{pull:nums trim, both=5}              # Trim 5 from each end
{pull:nums trim, left=5}              # Trim 5 from left
{pull:nums trim, right=5}             # Trim 5 from right
{pull:nums trim, left=10, right=5}    # Different amounts each side
```

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
{pull:nums median}        # Median value
{pull:nums minimum}       # Smallest value
{pull:nums maximum}       # Largest value
{pull:nums count}         # Number of elements
{pull:nums distinct}      # Count of unique values
{pull:nums dedup}         # Remove duplicates
{pull:nums unique}        # Alias for dedup
```

## Arithmetic Operations

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
{pull:nums increment}         # Add 1 to each
{pull:nums increment=5}       # Add 5 to each
```

## Logical Operations

```
{pull:nums and='seqB'}    # Logical AND
{pull:nums or='seqB'}     # Logical OR
{pull:nums not}           # Logical NOT
{pull:nums nand='seqB'}   # Logical NAND
{pull:nums nor='seqB'}    # Logical NOR
{pull:nums xor='seqB'}    # Logical XOR
{pull:nums xnor='seqB'}   # Logical XNOR
```

## One/Two Value Results

```
{pull:nums one}           # Return 1 if non-empty
{pull:nums two}           # Return 2 if has 2+ elements
```

## Multi-Sequence Operations

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

| Action | Description |
|--------|-------------|
| `reverse` | Reverse order |
| `sort` | Sort ascending |
| `shuffle` / `randomize` | Randomize order |
| `first` / `first=N` | First element(s) |
| `last` / `last=N` | Last element(s) |
| `left=N` | First N elements |
| `right=N` | Last N elements |
| `from=N` | From position N |
| `to=N` | To position N |
| `rows=N` | Limit to N rows |
| `shift=N` | Remove first N |
| `pop=N` | Remove last N |
| `element=N` | Get Nth element |
| `slice='pos\|len'` | Slice from position |
| `negative` | Invert selection |
| `trim` | Trim from ends |
| `eval='expr'` | Apply expression |
| `sum` | Sum all elements |
| `product` | Product of all |
| `average` | Mean value |
| `median` | Median value |
| `minimum` | Smallest value |
| `maximum` | Largest value |
| `count` | Number of elements |
| `distinct` | Unique count |
| `dedup` / `unique` | Remove duplicates |
| `one` | Return 1 if non-empty |
| `two` | Return 2 if 2+ elements |
| `add=N` | Add N to each element |
| `subtract=N` | Subtract N from each |
| `multiply=N` | Multiply each by N |
| `divide=N` | Divide each by N |
| `modulo=N` | Modulo N of each |
| `power=N` | Raise each to power N |
| `increment` / `increment=N` | Add 1 or N to each |
| `ceil` | Ceiling of each |
| `floor` | Floor of each |
| `round` | Round each |
| `negation` | Negate each |
| `and='seq'` | Logical AND |
| `or='seq'` | Logical OR |
| `not` | Logical NOT |
| `append='seq'` | Add sequence to end |
| `prepend='seq'` | Add sequence to start |
| `merge='seq'` | Merge sequences |
| `combine='seq'` | Combine sequences |
| `intersection='seq'` | Common elements |
| `difference='seq'` | Different elements |
| `onlyNow='seq'` | Only in current |
| `onlyStore='seq'` | Only in stored |
| `splice='pos\|len'` | Splice operation |

## Type Prefixes for Sequences

| Prefix | Purpose | Example |
|--------|---------|---------|
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `action:` | Sequence action | `{action:reverse}` |

## See Also

- [SEQUENCES.md](SEQUENCES.md) - Sequence types and generation
- [README.md](README.md) - Sequence documentation overview
