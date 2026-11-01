<?php

  // The helpers of a page's PHP (lib/formulas.php): padSheet computes named cells or rows of
  // values and answers them in the same shape, a whole number an int and an error its code;
  // padFormula computes one formula, its = optional, beside cells or none; RAND gives the
  // same numbers for the same seed.

  $named = padSheet ( [ 'A1' => 10, 'A2' => '=A1*3', 'B1' => '=A2/0', 'B2' => 'text', 'C1' => '=C2+1', 'C2' => '=C1+1' ] );
  $rows  = padSheet ( [ [ 2, 3, '=A1*B1' ], [ '=SUM(A1:C1)', '0.5', '=B2*2' ] ] );

  $results = [
    'named'      => json_encode ( $named ),
    'rows'       => json_encode ( $rows ),
    'sum'        => var_export ( padFormula ( '=SUM(1,2)' ), TRUE ),
    'no ='       => var_export ( padFormula ( 'SUM(1,2)*2' ), TRUE ),
    'beside'     => var_export ( padFormula ( '=B1*2', [ 'B1' => 21 ] ), TRUE ),
    'rows of'    => var_export ( padFormula ( '=AVERAGE(A1:A3)', [ [ 1 ], [ 2 ], [ 4 ] ] ), TRUE ),
    'text'       => var_export ( padFormula ( '=UPPER("pad")' ), TRUE ),
    'logical'    => var_export ( padFormula ( '=1<2' ), TRUE ),
    'fraction'   => var_export ( padFormula ( '=1/4' ), TRUE ),
    'error'      => var_export ( padFormula ( '=1/0' ), TRUE ),
    'unreadable' => var_export ( padFormula ( '=SUM(1,' ), TRUE ),
    'unknown'    => var_export ( padFormula ( '=NOPE(1)' ), TRUE ),
    'empty'      => var_export ( padFormula ( '=A1', [ 'B1' => 1 ] ), TRUE ),
    'same seed'  => var_export ( padFormula ( '=RAND()', [], 7 ) === padFormula ( '=RAND()', [], 7 ), TRUE ),
    'other seed' => var_export ( padFormula ( '=RAND()', [], 7 ) === padFormula ( '=RAND()', [], 8 ), TRUE ),
    'in range'   => var_export ( padFormula ( '=AND(RAND()>=0,RAND()<1)', [], 3 ), TRUE )
  ];

  $results = implode ( ' | ', array_map ( fn ( $label, $value ) => "$label: $value", array_keys ( $results ), $results ) );

?>
