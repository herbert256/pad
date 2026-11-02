# fake:locale

`supported=` is an explicit nonempty array of 1–200 BCP 47-shaped identifiers; defaults to `["en-GB","nl-NL"]`. It validates identifier syntax, not a language registry. Selection uses PAD's seeded generator and never changes PHP's random state.

## Example

```pad
{fake:locale seed=42/}
```

Run the focused fixture with `php tests/subsystems/fake-locale.php`.
