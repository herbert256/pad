<?php

  // minifs - MINIFS(min_range, criteria_range, criteria, ...) is the smallest number among the
  // cells of min_range where every criteria_range meets its criteria - pairs of a range and a
  // criterion (see COUNTIF), each range the size and shape of min_range, else #VALUE!. Without
  // any number there it is 0.
  //
  //   =MINIFS(C2:C7, A2:A7, "Apples")                   1.1
  //   =MINIFS(B2:B7, C2:C7, ">2", B2:B7, ">0")          8
  //   =MINIFS(B2:B7, A2:A7, "Kiwis")                    0

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
      return $numbers ? (float) min ( $numbers ) : 0.0;
    }
  ];

?>
