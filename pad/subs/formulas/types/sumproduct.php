<?php

  // sumproduct - SUMPRODUCT(array, ...) multiplies the cells of ranges place by place and adds the
  // products up: the ranges are walked in step, row by row, so they must have as many rows and
  // columns as one another, else #VALUE!. A cell that holds no number counts as 0 - a text, even
  // the text "2", a logical value, an empty cell - and an error in any cell is the answer. A value
  // typed into the call is a range of one cell by the same rule: SUMPRODUCT(3, 4) is 12,
  // SUMPRODUCT("3", 4) is 0, SUMPRODUCT(B2:B7, 2) is #VALUE! - the sizes differ - and an error
  // typed in is the answer at once. Excel computes an operator in the arguments on every cell of
  // a range, SUMPRODUCT((A2:A7="Apples")*B2:B7); the formulas have no array evaluation, and such
  // a formula is an error under the strict check and #VALUE! otherwise, never the number of one
  // cell - SUMIFS adds up with criteria.
  //
  //   =SUMPRODUCT(B2:B7, C2:C7)     134.5
  //   =SUMPRODUCT(A2:A7, B2:B7)     0
  //   =SUMPRODUCT(B2:B7, C2:C6)     #VALUE!

  return [
    'group'  => 'Math',
    'args'   => [ 1, 255 ],
    'arrays' => TRUE,
    'call'   => function ( $args ) {
      $arrays = [];
      foreach ( $args as $arg ) {
        if ( padFormulaIsError ( $arg ) )
          return $arg;
        $arrays [] = padFormulaIsRange ( $arg ) ? $arg : [ 'range' => [ [ $arg ] ], 'w' => 1, 'h' => 1 ];
      }
      foreach ( $arrays as $array )
        if ( $array ['w'] != $arrays [0] ['w'] or $array ['h'] != $arrays [0] ['h'] )
          return padFormulaError ( '#VALUE!' );
      foreach ( $arrays as $array )
        foreach ( padFormulaFlat ( $array ) as $value )
          if ( padFormulaIsError ( $value ) )
            return $value;
      // Past the cells the sheet has of any range its places are empty, and their products 0.
      $rows  = min ( array_map ( fn ( $array ) => count ( $array ['range'] ), $arrays ) );
      $total = 0.0;
      for ( $row = 0; $row < $rows; $row++ ) {
        $columns = min ( array_map ( fn ( $array ) => count ( $array ['range'] [$row] ), $arrays ) );
        for ( $column = 0; $column < $columns; $column++ ) {
          $product = 1.0;
          foreach ( $arrays as $array ) {
            $value    = $array ['range'] [$row] [$column];
            $product *= ( is_float ( $value ) or is_int ( $value ) ) ? $value : 0.0;
          }
          $total += $product;
        }
      }
      return $total;
    }
  ];

?>
