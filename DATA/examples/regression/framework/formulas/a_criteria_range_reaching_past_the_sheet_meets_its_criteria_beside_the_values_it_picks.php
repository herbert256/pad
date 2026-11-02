<?php

  // SUMIFS, AVERAGEIFS, MINIFS, MAXIFS, SUMIF and AVERAGEIF (padFormulaHits) walk the places
  // as far as the range they add up reaches into the sheet, not only as far as a criteria
  // range does: B3:B5 beside A2:A4 on a sheet of 4 rows ends in B5, past the sheet and empty,
  // which meets "<>b" and "" - so A4 = 100 counts, as Excel counts it (Microsoft's MINIFS
  // example shifts its criteria range by a row the same way) and as COUNTIFS counted the
  // place already. A row shifted past the sheet's last column the same. Before, the walk
  // stopped at B4: MAXIFS 1, AVERAGEIFS 1, SUMIFS 1, MINIFS 0.

  $pastCells = [ [ 'Weight', 'Grade' ], [ 10, 'a' ], [ 1, 'b' ], [ 100, 'a' ] ];
  $pastWide  = [ [ 1, 2 ], [ 'x', 'y' ] ];

  $pastCases = [
    [ '=MAXIFS(A2:A4,B3:B5,"<>b")', $pastCells ],
    [ '=MINIFS(A2:A4,B3:B5,"")', $pastCells ],
    [ '=AVERAGEIFS(A2:A4,B3:B5,"<>b")', $pastCells ],
    [ '=SUMIFS(A2:A4,B3:B5,"<>b")', $pastCells ],
    [ '=SUMIF(B3:B5,"<>b",A2:A4)', $pastCells ],
    [ '=AVERAGEIF(B3:B5,"",A2:A4)', $pastCells ],
    [ '=COUNTIFS(B3:B5,"<>b")', $pastCells ],
    [ '=MAXIFS(A2:A4,B3:B5,"a")', $pastCells ],
    [ '=SUMIFS(A3:A5,B2:B4,"a")', $pastCells ],
    [ '=SUMIFS(A1:B1,B2:C2,"<>y")', $pastWide ],
    [ '=SUMIFS(A1:B1,B2:C2,"y")', $pastWide ]
  ];

  $pastOut = [];

  foreach ( $pastCases as [ $pastFormula, $pastWith ] )
    $pastOut [] = "$pastFormula: " . var_export ( padFormula ( $pastFormula, $pastWith ), TRUE );

  $cases = implode ( ' | ', $pastOut );

?>
