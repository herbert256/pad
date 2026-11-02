# formats: ods

Bounded ODF 1.3 scalar-cell package import/export. sheet is a name or zero-based index on import, a name on export. header=first (default) maps the first row to unique nonempty names; none yields column1… records. Scalars preserve numbers, booleans, null and UTF-8 strings. Date/duration cells import as ISO strings. Formula import defaults reject; explicit formulas=cached trusts supplied cached values, text returns the formula source. No formulas execute; export always writes formula-looking strings as text. Merged cells and unsupported rich content are rejected. ZIP ≤8 MiB, ≤256 entries, ≤32 MiB total expanded metadata, content XML ≤8 MiB, depth≤64, XML elements≤200000, rows≤10000, columns≤256, cells≤100000, scalar text≤65536 bytes, aggregate expanded text≤8 MiB. Repeated rows/cells consume expanded limits. No extraction or external entities. Deterministic writer stores mimetype first and uncompressed.

References: [OASIS packages](https://docs.oasis-open.org/office/OpenDocument/v1.3/OpenDocument-v1.3-part2-packages.html), [OASIS scalar schema](https://docs.oasis-open.org/office/OpenDocument/v1.3/OpenDocument-v1.3-part3-schema.html).

Runnable example: `apps/tags/examples/format/ods.pad`. Independent fixture: `php tests/subsystems/expansion-formats-ods.php`.
