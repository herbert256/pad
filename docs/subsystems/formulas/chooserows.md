# formulas: chooserows

CHOOSEROWS(array,index,...) uses one-based indices; negatives count from the end. Zero, noninteger and out-of-range indices return #VALUE!. Repeated indices duplicate the selected row. All results use the existing dynamic-array spill ownership, dependency/cycle detection and 10000-cell limit.

Runnable example: `apps/tags/examples/formula/chooserows.pad`. Independent fixture: `php tests/subsystems/expansion-formulas-chooserows.php`.
