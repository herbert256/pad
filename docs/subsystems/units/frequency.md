# units: frequency

`padConvert(value, from, to)` converts this family to canonical `Hz` using static factors. Symbols and aliases are case sensitive. Units: `Hz` = 1 Hz, `kHz` = 1000 Hz, `MHz` = 1000000.0 Hz, `GHz` = 1000000000.0 Hz, `rpm` = 0.016666666666666666 Hz. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.

Runnable example: `apps/tags/examples/unit/frequency.pad`. Independent fixture: `php tests/subsystems/expansion-units-frequency.php`.
