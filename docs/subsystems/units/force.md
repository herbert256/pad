# units: force

`padConvert(value, from, to)` converts this family to canonical `N` using static factors. Symbols and aliases are case sensitive. Units: `N` = 1 N, `kN` = 1000 N, `dyn` = 1e-05 N, `lbf` = 4.4482216152605 N, `kgf` = 9.80665 N. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.

Runnable example: `apps/tags/examples/unit/force.pad`. Independent fixture: `php tests/subsystems/expansion-units-force.php`.
