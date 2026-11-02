# time:zonecompare

Required fixed instant, plus `zones` list of IANA names or label=>zone map (maximum 50). Supply an ISO timestamp with offset to avoid local ambiguity. `format` supports Y-m-d H:i, Y-m-d H:i:s and H:i; offsets are always shown at the requested instant, including DST. Server rendered without JavaScript.

## Example

```pad
{time:zonecompare '2026-07-01T12:00:00Z'/}
```

Run the focused fixture with `php tests/subsystems/time-zonecompare.php`.
