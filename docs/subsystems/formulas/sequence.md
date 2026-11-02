# formulas: sequence

SEQUENCE(rows,[columns=1],[start=1],[step=1]) fills row-major. Dimensions must be integers; nonpositive dimensions return #CALC!, more than 10000 cells returns #NUM!, arithmetic overflow returns #NUM!. Negative and zero steps are allowed. All results use the existing dynamic-array spill ownership, dependency/cycle detection and 10000-cell limit.

Runnable example: `apps/tags/examples/formula/sequence.pad`. Independent fixture: `php tests/subsystems/expansion-formulas-sequence.php`.
