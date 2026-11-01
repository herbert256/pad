<?php

  // sumif - SUMIF(range, criteria, [sum_range]) adds up the cells of sum_range - else of range
  // itself - where range meets the criteria: a value, or a text such as ">5", "<>Apples", "A*"
  // (see COUNTIF). sum_range is read from its first cell at the size of range, as Excel reads it.
  //
  //   =SUMIF(A2:A7, "Apples", B2:B7)   25
  //   =SUMIF(B2:B7, ">10")             35

  return [
    'group' => 'Math',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $range = $args [0];
      if ( ! padFormulaIsRange ( $range ) )
        return padFormulaError ( '#VALUE!' );
      $sum  = ( isset ( $args [2] ) and padFormulaIsRange ( $args [2] ) ) ? $args [2] : $range;
      $hits = padFormulaHits ( [ [ $range, $args [1] ] ], $env, $sum );
      if ( padFormulaIsError ( $hits ) )
        return $hits;
      $total = 0.0;
      foreach ( $hits [0] as [ $row, $column ] ) {
        $value = padFormulaAt ( $sum, $row, $column, $env );
        if ( padFormulaIsError ( $value ) )
          return $value;
        if ( is_float ( $value ) or is_int ( $value ) )
          $total += $value;
      }
      return $total;
    }
  ];

?>
