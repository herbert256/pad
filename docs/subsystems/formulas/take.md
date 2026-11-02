# formulas: take

TAKE(array,rows,[columns]) selects up to the requested count. Negative counts operate from the end; oversized counts clamp to the dimension. Empty output returns #CALC!. DROP zero leaves that dimension unchanged; TAKE zero is empty. All results use the existing dynamic-array spill ownership, dependency/cycle detection and 10000-cell limit.

Runnable example: `apps/tags/examples/formula/take.pad`. Independent fixture: `php tests/subsystems/expansion-formulas-take.php`.
