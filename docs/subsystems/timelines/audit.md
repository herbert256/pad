# timeline:audit

Records: timestamp, actor, action, target and optional scalar detail. Timestamp must be an explicit ISO instant with offset (or DateTime); timezone defaults UTC. Stable ascending order preserves input order at tied instants. Maximum 2000 records. Escaped details use native disclosure. This displays application-supplied history without collecting logs or asserting integrity.

## Example

The paired PHP page supplies the application data:

```php
<?php
$auditRecords=[['timestamp'=>'2026-11-02T09:00:00Z','actor'=>'Ada','action'=>'updated','target'=>'Report','detail'=>'Changed title'],['timestamp'=>'2026-11-02T09:00:00Z','actor'=>'Bo','action'=>'reviewed','target'=>'Report']];
```

The PAD template renders it:

```pad
{timeline:audit data='auditRecords', timezone='Europe/Amsterdam'/}
```

Run the focused fixture with `php tests/subsystems/timeline-audit.php`.
