<?php

  // SIGN, QUOTIENT and RANDBETWEEN (types/sign.php, quotient.php, randbetween.php) on the
  // examples of Microsoft's pages for them, with the answers those pages give - RANDBETWEEN's
  // a number between its ends, which is held here: 600 draws of 1 to 6 and of -1 to 1 all
  // whole numbers between the ends, every one of them drawn, and the same 600 for the same
  // seed; then the edges: SIGN of an empty cell 0, of a text #VALUE!; QUOTIENT cut toward 0 on
  // the quotient's 15 digits, 0 never -0, a denominator of 0 #DIV/0!, a quotient past a float
  // #NUM!; RANDBETWEEN's bottom rounded up and top down, a bottom then above the top #NUM!.
  // Then on the sample sheet (_lib/formulas.php).

  $mathCases = [
    '=SIGN(10)', '=SIGN(4-4)', '=SIGN(-0.00001)',
    '=QUOTIENT(5, 2)', '=QUOTIENT(4.5, 3.1)', '=QUOTIENT(-10, 3)',
    '=SIGN(A1)', '=SIGN("x")', '=SIGN(1E-300)', '=SIGN(#N/A)',
    '=QUOTIENT(0.3,0.1)', '=QUOTIENT(-0.5,2)', '=QUOTIENT(7,-2)', '=QUOTIENT(1,0)', '=QUOTIENT(1E300,1E-300)', '=QUOTIENT("6","3")',
    '=RANDBETWEEN(7,7)', '=RANDBETWEEN(1.5,2.5)', '=RANDBETWEEN(-1.5,-0.5)', '=RANDBETWEEN(1.2,1.8)', '=RANDBETWEEN(2,1)', '=RANDBETWEEN("a",1)'
  ];

  $mathOut = [];

  foreach ( $mathCases as $mathFormula )
    $mathOut [] = "$mathFormula: " . var_export ( padFormula ( $mathFormula ), TRUE );

  $mathRows = [];

  for ( $mathRow = 0; $mathRow < 600; $mathRow++ )
    $mathRows [] = [ '=RANDBETWEEN(1,6)', '=RANDBETWEEN(-1,1)', '=RANDBETWEEN(1,100)' ];

  $mathDraws = padSheet ( $mathRows, 7 );
  $mathSeen  = [ [], [], [] ];
  $mathOdd   = 0;

  foreach ( $mathDraws as $mathDraw )
    foreach ( [ [ 1, 6 ], [ -1, 1 ], [ 1, 100 ] ] as $mathAt => [ $mathLow, $mathHigh ] ) {
      if ( ! is_int ( $mathDraw [$mathAt] ) or $mathDraw [$mathAt] < $mathLow or $mathDraw [$mathAt] > $mathHigh )
        $mathOdd++;
      $mathSeen [$mathAt] [ $mathDraw [$mathAt] ] = TRUE;
    }

  $mathOut [] = '600 draws: ' . $mathOdd . ' outside their ends, ' . count ( $mathSeen [0] ) . ' of 1 to 6 drawn, '
              . count ( $mathSeen [1] ) . ' of -1 to 1 drawn, the same again: '
              . ( padSheet ( $mathRows, 7 ) === $mathDraws ? 'yes' : 'no' );

  $cases = implode ( ' | ', $mathOut ) . ' | ' . formulasRun ( [
    '=SIGN(E2)',
    '=SIGN(B6)',
    '=QUOTIENT(B4,B3)',
    '=QUOTIENT(E7,B2)',
    '=RANDBETWEEN(B6,B6)'
  ] );

?>
