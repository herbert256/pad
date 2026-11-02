<?php

  // maxifs - MAXIFS(max_range, criteria_range, criteria, ...) is the largest number among the
  // cells of max_range where every criteria_range meets its criteria - pairs of a range and a
  // criterion (see COUNTIF), each range the size and shape of max_range, else #VALUE!. Without
  // any number there it is 0.
  //
  //   =MAXIFS(B2:B7, A2:A7, "Apples")                   15
  //   =MAXIFS(D2:D7, A2:A7, "Apples")                   2024-04-01
  //   =MAXIFS(B2:B7, A2:A7, "Kiwis")                    0

  return [
    'group' => 'Statistics',
    'args'  => [ 3, 255 ],
    'pairs' => 1,
    'takes' => 'a range and pairs of a range and its criteria',
    'call'  => function ( $args, $env ) {
      $values = padFormulaIfsValues ( $args, $env );
      if ( padFormulaIsError ( $values ) )
        return $values;
      $numbers = array_filter ( $values, fn ( $value ) => is_float ( $value ) or is_int ( $value ) );
      return $numbers ? (float) max ( $numbers ) : 0.0;
    }
  ];

?>
