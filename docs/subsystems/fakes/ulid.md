# Fake: ulid

`padFake('ulid', ['time' => 1700000000000])` returns a canonical ULID from explicit Unix milliseconds plus 80 bits from PAD's seeded fake stream. The timestamp is required, integral and 48-bit. `padFakeSeed(7)` repeats the output; no clock dependency. These identifiers are test fixtures, not cryptographically secure or guaranteed unique/monotonic.

Template equivalent: `{fake:ulid/}`, with the same named options. Errors call `padError` and return NULL if execution continues. Run `php tests/subsystems/fake-ulid.php`.
