<?php

  // index - INDEX(array, row, [column]) is the cell of a range at a row and a column, counted from
  // 1; of a range of one row or one column a single number is the place in it. A row or a column
  // of 0 is the whole column or row. A place past the range is #REF!.
  //
  //   =INDEX(A2:C7, 3, 1)           Cherries
  //   =INDEX(C2:C7, 4)              1.1
  //   =INDEX(A2:C7, 9, 1)           #REF!

  return [
    'group' => 'Lookup',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $array  = $args [0];
      $row    = padFormulaArgInt ( $args [1], $env );
      $column = array_key_exists ( 2, $args ) ? padFormulaArgInt ( $args [2], $env ) : NULL;
      if ( $error = padFormulaFirstError ( $array, $row, $column ) )
        return $error;
      if ( $row < 0 or ( $column !== NULL and $column < 0 ) )
        return padFormulaError ( '#VALUE!' );
      if ( ! padFormulaIsRange ( $array ) )
        return ( $row <= 1 and (int) $column <= 1 ) ? $array : padFormulaError ( '#REF!' );
      if ( $column === NULL ) {
        if ( $array ['h'] == 1 )
          [ $row, $column ] = [ 1, $row ];
        else
          $column = ( $array ['w'] == 1 ) ? 1 : 0;
      }
      if ( $row > $array ['h'] or $column > $array ['w'] )
        return padFormulaError ( '#REF!' );
      return padFormulaPart ( $array, $row ? $row - 1 : 0, $column ? $column - 1 : 0,
                              $row ? 1 : $array ['h'], $column ? 1 : $array ['w'], $env );
    }
  ];

?>
