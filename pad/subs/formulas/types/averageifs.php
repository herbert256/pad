<?php

  // averageifs - AVERAGEIFS(average_range, criteria_range, criteria, ...) is the mean of the cells
  // of average_range where every criteria_range meets its criteria - pairs of a range and a
  // criterion (see COUNTIF), each range the size and shape of average_range, else #VALUE!. Only
  // the numbers among those cells count; without any it is #DIV/0!.
  //
  //   =AVERAGEIFS(B2:B7, A2:A7, "Apples")               12.5
  //   =AVERAGEIFS(C2:C7, B2:B7, ">5", B2:B7, "<20")     2.1
  //   =AVERAGEIFS(B2:B7, A2:A7, "Kiwis")                #DIV/0!

  return [
    'group' => 'Statistics',
    'args'  => [ 3, 255 ],
    'pairs' => 1,
    'takes' => 'a range to average and pairs of a range and its criteria',
    'call'  => function ( $args, $env ) {
      $values = padFormulaIfsValues ( $args, $env );
      if ( padFormulaIsError ( $values ) )
        return $values;
      $numbers = array_filter ( $values, fn ( $value ) => is_float ( $value ) or is_int ( $value ) );
      return $numbers ? array_sum ( $numbers ) / count ( $numbers ) : padFormulaError ( '#DIV/0!' );
    }
  ];

?>
