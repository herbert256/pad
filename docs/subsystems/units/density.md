# units: density

`padConvert(value, from, to)` converts this family to canonical `kg/m3` using static factors. Symbols and aliases are case sensitive. Units: `kg/m3` = 1 kg/m3, `g/cm3` = 1000 kg/m3, `g/L` = 1 kg/m3, `kg/L` = 1000 kg/m3, `lb/ft3` = 16.01846337396014 kg/m3. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.

Runnable example: `apps/tags/examples/unit/density.pad`. Independent fixture: `php tests/subsystems/expansion-units-density.php`.
