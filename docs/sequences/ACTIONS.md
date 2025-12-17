# PAD Sequence Actions

This document describes the actions and transformations available for PAD sequences.

## Order Manipulation

```
{pull:nums reverse}       # Reverse order
{pull:nums sort}          # Sort ascending
{pull:nums shuffle}       # Randomize order
```

## Selection

```
{pull:nums first}         # First element
{pull:nums first=3}       # First 3 elements
{pull:nums last=3}        # Last 3 elements
{pull:nums shift=2}       # Remove first 2
{pull:nums pop=2}         # Remove last 2
{pull:nums element=5}     # Get 5th element
{pull:nums slice='3|4'}   # From position 3, length 4
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
| `shuffle` | Randomize order |
| `first` / `first=N` | First element(s) |
| `last` / `last=N` | Last element(s) |
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
| `dedup` | Remove duplicates |
| `append='seq'` | Add sequence to end |
| `prepend='seq'` | Add sequence to start |
| `merge='seq'` | Merge sequences |
| `intersection='seq'` | Common elements |
| `difference='seq'` | Different elements |

## See Also

- [SEQUENCES.md](SEQUENCES.md) - Sequence types and generation
- [README.md](README.md) - Sequence documentation overview
