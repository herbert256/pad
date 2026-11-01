<?php

  // SUMPRODUCT (types/sumproduct.php) walks its ranges in step by their rows and columns -
  // Microsoft's example of two ranges of 3 rows and 2 columns is 156, a range of 2 by 2 and
  // one of 4 by 1 differ in shape and are #VALUE! - counts a cell that holds no number as 0,
  // the text "2" too, answers the first error of a cell, and takes a value typed into the
  // call as a range of one. An operator or a function on every cell of a range - Excel's
  // array evaluation - is #VALUE!, never the number of the one cell an IFERROR or ABS takes
  // of the range; the strict check of {spreadsheet} reports it (the errors suite).

  $sumproductCells = [ 'A1' => 2, 'A2' => 3, 'B1' => 10, 'B2' => 20, 'B3' => 30 ];
  $sumproductText  = [ 'A1' => "'2", 'A2' => 3, 'B1' => 10, 'B2' => 20 ];
  $sumproductDoc   = [ [ 3, 4, 2, 7 ], [ 8, 6, 6, 7 ], [ 1, 9, 5, 3 ] ];
  $sumproductOdd   = [ 'A1' => TRUE, 'A2' => 'x', 'A4' => 5, 'B1' => 10, 'B2' => 20, 'B3' => 30, 'B4' => 2 ];
  $sumproductError = [ 'A1' => 2, 'A2' => '#N/A', 'B1' => '#DIV/0!', 'B2' => 20 ];

  $sumproductCases = [
    '[2,3] x [10,20]'                 => [ '=SUMPRODUCT(A1:A2,B1:B2)', $sumproductCells ],
    '[text 2,3] x [10,20]'            => [ '=SUMPRODUCT(A1:A2,B1:B2)', $sumproductText ],
    '2 rows x 3 rows'                 => [ '=SUMPRODUCT(A1:A2,B1:B3)', $sumproductCells ],
    'one range'                       => [ '=SUMPRODUCT(B1:B3)', $sumproductCells ],
    'three ranges'                    => [ '=SUMPRODUCT(A1:A2,B1:B2,B2:B3)', $sumproductCells ],
    '3x2 x 3x2'                       => [ '=SUMPRODUCT(A1:B3,C1:D3)', $sumproductDoc ],
    '2x2 x 4x1'                       => [ '=SUMPRODUCT(A1:B2,C1:C4)', $sumproductDoc ],
    'a row x a row'                   => [ '=SUMPRODUCT(A1:D1,A2:D2)', $sumproductDoc ],
    'a row x a column'                => [ '=SUMPRODUCT(A1:C1,A1:A3)', $sumproductDoc ],
    'logical, text, empty count 0'    => [ '=SUMPRODUCT(A1:A4,B1:B4)', $sumproductOdd ],
    'past the sheet'                  => [ '=SUMPRODUCT(B1:B9,B1:B9)', $sumproductCells ],
    'an error of a cell'              => [ '=SUMPRODUCT(A1:A2,B1:B2)', $sumproductError ],
    'the first error, left to right'  => [ '=SUMPRODUCT(B1:B2,A1:A2)', $sumproductError ],
    'sizes before cell errors'        => [ '=SUMPRODUCT(A1:A2,B1:B3)', $sumproductError ],
    'typed numbers'                   => [ '=SUMPRODUCT(3,4)', [] ],
    'typed text'                      => [ '=SUMPRODUCT("3",4)', [] ],
    'typed logical'                   => [ '=SUMPRODUCT(TRUE,4)', [] ],
    'a range and a typed number'      => [ '=SUMPRODUCT(A1:A2,2)', $sumproductCells ],
    'a typed error'                   => [ '=SUMPRODUCT(A1:A2,1/0)', $sumproductCells ],
    'an operator on ranges'           => [ '=SUMPRODUCT((A1:A2=2)*B1:B2)', $sumproductCells ],
    'a minus on a range'              => [ '=SUMPRODUCT(--(A1:A2>2))', $sumproductCells ],
    'IFERROR on a range'              => [ '=SUMPRODUCT(IFERROR(A1:A2,0))', $sumproductCells ],
    'ABS on a range'                  => [ '=SUMPRODUCT(ABS(A1:A2))', $sumproductCells ],
    'a range INDEX answers'           => [ '=SUMPRODUCT(INDEX(A1:B2,0,1),B1:B2)', $sumproductCells ],
    'SUM of a range in it'            => [ '=SUMPRODUCT(SUM(A1:A2),2)', $sumproductCells ]
  ];

  $sumproductOut = [];

  foreach ( $sumproductCases as $sumproductLabel => [ $sumproductFormula, $sumproductWith ] )
    $sumproductOut [] = "$sumproductLabel: " . var_export ( padFormula ( $sumproductFormula, $sumproductWith ), TRUE );

  // In a cell of a sheet the one cell of a range in the formula's own row is what an operator
  // takes: SUMPRODUCT(ABS(A1:A2)) in C1 was ABS(A1), 2.

  $sumproductSheet = padSheet ( [ 'A1' => -2, 'A2' => 3, 'C1' => '=SUMPRODUCT(ABS(A1:A2))', 'C2' => '=SUMPRODUCT(A1:A2*1)' ] );

  $sumproductOut [] = 'in C1: ' . var_export ( $sumproductSheet ['C1'], TRUE ) . ', in C2: ' . var_export ( $sumproductSheet ['C2'], TRUE );

  $cases = formulasRun ( [
    '=SUMPRODUCT(B2:B7,C2:C7)',
    '=SUMPRODUCT(B2:B7,C2:C7,B2:B7)',
    '=SUMPRODUCT(A2:A7,B2:B7)',
    '=SUMPRODUCT(B2:C7,B2:C7)',
    '=SUMPRODUCT(B2:B7,C2:C6)',
    '=SUMPRODUCT(B2:B7,C2:D7)',
    '=SUMPRODUCT(B:B,C:C)',
    '=sumproduct(B2:B3,C2:C3)'
  ] ) . ' | ' . implode ( ' | ', $sumproductOut );

?>
