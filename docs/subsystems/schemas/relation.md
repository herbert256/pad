# schemas: relation

Checks already-shaped named rowsets. Schema primary is a list of `{table,fields}`; foreign entries are `{table,fields,references:{table,fields},null:"error"|"skip"}`. Composite scalar keys preserve PHP types, including integer vs string and boolean vs integer; no coercion. Primary keys cannot be null; foreign skip ignores a row with any null key component, while missing fields always fail. Returns the first deterministic issue with quoted table/row path. ≤100 tables, 10000 total rows, 100 declarations, 16 key fields and 1000000 key-field checks, 8 MiB aggregate key bytes, 4096 bytes per string key component and 256 bytes per table/field name. Row shape remains the rowset kind’s responsibility.

Runnable example: `apps/tags/examples/schema/relation.pad`. Independent fixture: `php tests/subsystems/expansion-schemas-relation.php`.
