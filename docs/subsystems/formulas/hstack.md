# formulas: hstack

HSTACK(array,...) combines 1..255 scalar or matrix arguments horizontally. Short dimensions are padded with #N/A cell errors. Input errors are preserved; output dimensions are checked before allocation. All results use the existing dynamic-array spill ownership, dependency/cycle detection and 10000-cell limit.

Runnable example: `apps/tags/examples/formula/hstack.pad`. Independent fixture: `php tests/subsystems/expansion-formulas-hstack.php`.
