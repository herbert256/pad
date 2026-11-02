<?php

  // averageif - AVERAGEIF(range, criteria, [average_range]) is the mean of the cells of
  // average_range - else of range - where range meets the criteria (see COUNTIF); without any
  // #DIV/0!.
  //
  //   =AVERAGEIF(A2:A7, "Apples", B2:B7)   12.5
  //   =AVERAGEIF(B2:B7, ">100")            #DIV/0!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $range = $args [0];
      if ( ! padFormulaIsRange ( $range ) )
        return padFormulaError ( '#VALUE!' );
      $over = ( isset ( $args [2] ) and padFormulaIsRange ( $args [2] ) ) ? $args [2] : $range;
      $hits = padFormulaHits ( [ [ $range, $args [1] ] ], $env, $over );
      if ( padFormulaIsError ( $hits ) )
        return $hits;
      $total = 0.0;
      $count = 0;
      foreach ( $hits [0] as [ $row, $column ] ) {
        $value = padFormulaAt ( $over, $row, $column, $env );
        if ( padFormulaIsError ( $value ) )
          return $value;
        if ( is_float ( $value ) or is_int ( $value ) ) {
          $total += $value;
          $count++;
        }
      }
      return $count ? $total / $count : padFormulaError ( '#DIV/0!' );
    }
  ];

?>
