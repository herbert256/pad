<?php

  // STDEV.P and VAR.P (types/stdev.p.php, var.p.php) divide by the count where STDEV and VAR
  // divide by the count less one, and STDEV.S and VAR.S are STDEV and VAR under their names of
  // Excel 2010, STDEVP and VARP the population ones under their older names. On the table of
  // Microsoft's pages for the four, with the answers those pages give (26.05455814,
  // 27.46391572, 678.84, 754.27); then the edges: one number 0 for the population and #DIV/0!
  // for the sample, none #DIV/0!, typed into the call TRUE counts 1 and a text that reads as a
  // number that number, another text #VALUE!, a range's text, logical values and empty cells
  // left out, an error in it the answer. Then on the sample sheet (_lib/formulas.php).

  $statStrength = [ [ 'Strength' ], [ 1345 ], [ 1301 ], [ 1368 ], [ 1322 ], [ 1310 ], [ 1370 ], [ 1318 ], [ 1350 ], [ 1303 ], [ 1299 ] ];
  $statOdd      = [ [ 4, 'x', TRUE ], [ 8, NULL, '#N/A' ] ];

  $statCases = [
    [ '=STDEV.P(A2:A11)', $statStrength ],
    [ '=STDEV.S(A2:A11)', $statStrength ],
    [ '=VAR.P(A2:A11)', $statStrength ],
    [ '=VAR.S(A2:A11)', $statStrength ],
    [ '=ROUND(STDEV.P(A2:A11),8)', $statStrength ],
    [ '=ROUND(STDEV.S(A2:A11),8)', $statStrength ],
    [ '=ROUND(VAR.S(A2:A11),2)', $statStrength ],
    [ '=STDEVP(A2:A11)=STDEV.P(A2:A11)', $statStrength ],
    [ '=VARP(A2:A11)=VAR.P(A2:A11)', $statStrength ],
    [ '=STDEV(A2:A11)=STDEV.S(A2:A11)', $statStrength ],
    [ '=VAR(A2:A11)=VAR.S(A2:A11)', $statStrength ],
    [ '=STDEV.P(7)', [] ],
    [ '=VAR.P(7)', [] ],
    [ '=STDEV.S(7)', [] ],
    [ '=VAR.S(7)', [] ],
    [ '=STDEV.P(B1:B2)', $statOdd ],
    [ '=VAR.P(A1:B2)', $statOdd ],
    [ '=VAR.P(A1:A2,TRUE,"3")', $statOdd ],
    [ '=VAR.P(A1:A2,"x")', $statOdd ],
    [ '=STDEV.P(A1:C2)', $statOdd ],
    [ '=VARP(A1:A2,#DIV/0!)', $statOdd ]
  ];

  $statOut = [];

  foreach ( $statCases as [ $statFormula, $statCells ] )
    $statOut [] = "$statFormula: " . var_export ( padFormula ( $statFormula, $statCells ), TRUE );

  $cases = implode ( ' | ', $statOut ) . ' | ' . formulasRun ( [
    '=STDEV.P(B2:B7)',
    '=VAR.P(B2:B7)',
    '=STDEV.S(B2:B7)',
    '=VAR.S(C2:C7)',
    '=STDEVP(2,4,4,4,5,5,7,9)',
    '=VARP(A2:A7)'
  ] );

?>
