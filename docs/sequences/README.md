# PAD Sequence Documentation

The PAD sequence subsystem is a powerful mathematical sequence generation and manipulation engine. It provides over 80 sequence types, sequence storage and retrieval, and a rich set of transformation operations.

## Quick Example

```
{fibonacci rows=10}{$fibonacci} {/fibonacci}
```

Output: `1 1 2 3 5 8 13 21 34 55`

## Documents

| File | Description |
|------|-------------|
| [SEQUENCES.md](SEQUENCES.md) | Sequence types - all 80+ available sequences and how to generate them |
| [ACTIONS.md](ACTIONS.md) | Sequence actions - transformations, aggregations, and operations |
| [EXPLAIN.md](EXPLAIN.md) | Technical internals - how the sequence subsystem works |
| [PAD.md](PAD.md) | Framework integration - sequence implementation details |

## Key Features

- **80+ Sequence Types:** Fibonacci, prime, triangular, Lucas, Catalan, and many more
- **Storage & Retrieval:** Store sequences with `push`, retrieve with `pull`
- **Transformations:** `reverse`, `sort`, `shuffle`, `dedup`
- **Selection:** `first`, `last`, `slice`, `shift`, `pop`
- **Aggregations:** `sum`, `average`, `minimum`, `maximum`, `count`
- **Multi-sequence:** `append`, `merge`, `intersection`, `difference`
- **OEIS Integration:** Fetch any sequence from the Online Encyclopedia of Integer Sequences

## Getting Started

Generate a simple range:
```
{sequence '1..10', name='n'}{$n} {/sequence}
```

Store and transform:
```
{sequence '1..10', push='nums'}
{resume reverse}
{pull:nums}{$sequence} {/pull:nums}
```

Aggregate values:
```
{sequence '1..100', push='nums'}
Sum: {pull:nums sum}{$sequence}{/pull:nums}
```

## Options for Sequence Tags

| Option | Description |
|--------|-------------|
| `rows` | Limit number of elements |
| `name` | Variable name for current value |
| `push` | Store sequence with this name |
| `minimal` | Minimum value (for random) |
| `maximal` | Maximum value (for random) |
| `increment` | Step increment |
| `unique` | Ensure unique values |

## Notes

- Sequences are lazily evaluated where possible
- Use `push` to store sequences for later transformation
- Use `{resume}` to apply transformations to the last pushed sequence
- Use `{pull:name}` to iterate over a stored sequence
- The `@` symbol represents the current value in `eval` expressions

## See Also

- [../APP.md](../APP.md) - Application development guide
- [../reference/TAGS.md](../reference/TAGS.md) - All template tags reference
