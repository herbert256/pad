# formulas: transpose

TRANSPOSE(array) swaps rows and columns. Scalars produce a 1×1 matrix. Cell errors retain their positions after transposition. All results use the existing dynamic-array spill ownership, dependency/cycle detection and 10000-cell limit.

Runnable example: `apps/tags/examples/formula/transpose.pad`. Independent fixture: `php tests/subsystems/expansion-formulas-transpose.php`.
