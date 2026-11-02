# fake:coordinate

Returns one typed record `{latitude, longitude}` in WGS84 degrees. `box=[south,west,north,east]` must be ordered and finite; antimeridian-crossing boxes must be split by the caller. `method=surface` (default) samples longitude and sine(latitude) uniformly; `degrees` samples the coordinate rectangle uniformly. Eight decimal places; bounds are preserved. The seeded sequence is repeatable.

## Example

```pad
{fake:coordinate seed=42}{$coordinate.latitude}, {$coordinate.longitude}{/fake:coordinate}
```

Run the focused fixture with `php tests/subsystems/fake-coordinate.php`.
