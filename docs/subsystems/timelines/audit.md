# timeline:audit

Records: timestamp, actor, action, target and optional scalar detail. Timestamp must be an explicit ISO instant with offset (or DateTime); timezone defaults UTC. Stable ascending order preserves input order at tied instants. Maximum 2000 records. Escaped details use native disclosure. This displays application-supplied history without collecting logs or asserting integrity.

## Example

```pad
{timeline:audit data='auditRecords', timezone='Europe/Amsterdam'/}
```

Run the focused fixture with `php tests/subsystems/timeline-audit.php`.
