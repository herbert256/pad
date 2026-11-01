<?php

  // PERCENTILE.INC, PERCENTILE.EXC, QUARTILE.INC, QUARTILE.EXC and the older names PERCENTILE
  // and QUARTILE (types/percentile*.php, quartile*.php, padFormulaPercentile in
  // _lib/collect.php) on the data of Microsoft's pages for them, with the answers those pages
  // give - PERCENTILE.EXC's data and answers those of the table its page shows as a picture,
  // which Python's statistics.quantiles (method exclusive) gives too; then the edges: no
  // number, a k out of reach or a place before the first number or past the last #NUM!, the
  // places 1 and n of the exclusive form reached exactly, a k that is no number #VALUE!, the
  // text, logical values and empty cells of a range left out, an error in it the answer, a
  // quart cut toward 0, an empty cell as k 0. Then on the sample sheet (_lib/formulas.php).

  $pctInc   = [ [ 'Data' ], [ 1 ], [ 3 ], [ 2 ], [ 4 ] ];
  $pctQuart = [ [ 'Data' ], [ 1 ], [ 2 ], [ 4 ], [ 7 ], [ 8 ], [ 9 ], [ 10 ], [ 12 ] ];
  $pctExcQ  = [ [ 'Data' ], [ 6 ], [ 7 ], [ 15 ], [ 36 ], [ 39 ], [ 40 ], [ 41 ], [ 42 ], [ 43 ], [ 47 ], [ 49 ] ];
  $pctExc   = [ [ 'Data' ], [ 1 ], [ 2 ], [ 3 ], [ 6 ], [ 6 ], [ 6 ], [ 7 ], [ 8 ], [ 9 ] ];
  $pctOdd   = [ [ 4, 'x', TRUE ], [ 1, NULL, '7' ], [ 3, '#DIV/0!', 2 ], [ NULL, NULL, NULL ] ];

  $pctCases = [
    [ '=PERCENTILE.INC(A2:A5,0.3)', $pctInc ],
    [ '=QUARTILE.INC(A2:A9,1)', $pctQuart ],
    [ '=QUARTILE.EXC(A2:A12,1)', $pctExcQ ],
    [ '=QUARTILE.EXC(A2:A12,3)', $pctExcQ ],
    [ '=PERCENTILE.EXC(A2:A10,0.25)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,0)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,0.01)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,2)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,0.1)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,0.9)', $pctExc ],
    [ '=PERCENTILE.EXC(A2:A10,0.95)', $pctExc ],
    [ '=PERCENTILE.INC(A2:A10,0)', $pctExc ],
    [ '=PERCENTILE.INC(A2:A10,1)', $pctExc ],
    [ '=PERCENTILE.INC(A2:A10,-0.1)', $pctExc ],
    [ '=PERCENTILE.INC(A2:A10,"half")', $pctExc ],
    [ '=PERCENTILE.INC(A2:A10,"0.5")', $pctExc ],
    [ '=PERCENTILE(A2:A5,0.3)', $pctInc ],
    [ '=QUARTILE(A2:A9,1)', $pctQuart ],
    [ '=QUARTILE.INC(A2:A9,0)', $pctQuart ],
    [ '=QUARTILE.INC(A2:A9,4)', $pctQuart ],
    [ '=QUARTILE.INC(A2:A9,1.9)', $pctQuart ],
    [ '=QUARTILE.INC(A2:A9,-0.5)', $pctQuart ],
    [ '=QUARTILE.INC(A2:A9,-1)', $pctQuart ],
    [ '=QUARTILE.EXC(A2:A12,3.9)', $pctExcQ ],
    [ '=QUARTILE.EXC(A2:A12,0)', $pctExcQ ],
    [ '=QUARTILE.EXC(A2:A3,1)', $pctExcQ ],
    [ '=QUARTILE.EXC(A2:A4,2)', $pctExcQ ],
    [ '=PERCENTILE.INC(A1:C2,0.5)', $pctOdd ],
    [ '=PERCENTILE.INC(A1:C3,0.5)', $pctOdd ],
    [ '=PERCENTILE.INC(B4:C4,0.5)', $pctOdd ],
    [ '=PERCENTILE.EXC(5,0.5)', $pctOdd ],
    [ '=PERCENTILE.INC("x",0.5)', $pctOdd ],
    [ '=PERCENTILE.INC(A1:A3,A4)', $pctOdd ],
    [ '=PERCENTILE.EXC(A1:A3,A4)', $pctOdd ],
    [ '=PERCENTILE.INC(A1:A3,#N/A)', $pctOdd ]
  ];

  $pctOut = [];

  foreach ( $pctCases as [ $pctFormula, $pctCells ] )
    $pctOut [] = "$pctFormula: " . var_export ( padFormula ( $pctFormula, $pctCells ), TRUE );

  $cases = implode ( ' | ', $pctOut ) . ' | ' . formulasRun ( [
    '=PERCENTILE.INC(B2:B7,0.3)',
    '=PERCENTILE.INC(B2:B7,0.7)',
    '=PERCENTILE.EXC(B2:B7,0.25)',
    '=QUARTILE.INC(B2:B7,1)',
    '=QUARTILE.INC(C2:C7,2)',
    '=QUARTILE.EXC(B2:B7,3)',
    '=QUARTILE.INC(A2:A7,1)',
    '=PERCENTILE.INC(B:B,0.5)'
  ] );

?>
