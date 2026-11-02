# Unit conversion

`padConvert(100, 'cm', 'm')` returns `1.0`. `padConvert(32, 'F', 'C')` returns approximately zero. `padUnits()` returns all families; `padUnits('length')` returns its canonical symbol and `units` map, whose entries contain `scale`, `offset`, and `aliases`.

Families are length, area, volume, mass, temperature and digital. Symbols and aliases are case sensitive: `B` is bytes and `b` is bits; `kB` is 1000 bytes and `KiB` is 1024. Gallons must be `USgal` or `UKgal`; ambiguous `gal`, pounds as force and inferred compound dimensions are unsupported. International foot, inch, mile and avoirdupois pound are used. Conversion is numeric, separate from locale formatting. A canonical value is `value * scale + offset`, so temperature is affine. Temperatures below absolute zero are rejected, as are non-finite input/output and incompatible dimensions. Failures call `padError` and return NULL if the configured error handler continues.

Example: `$litres = padConvert(2, 'USgal', 'L');` gives 7.570823568. Register these as helper-only kinds (`unit => units`), not template tags.

Run `php tests/subsystems/units.php` for known-value, every-unit round-trip and invalid-input checks.
