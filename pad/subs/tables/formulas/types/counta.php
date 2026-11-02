<?php

  // counta - COUNTA(value, ...) counts the values that are not empty - numbers, texts, logical
  // values and errors, and the empty text a formula answers too.
  //
  //   =COUNTA(A1:A8)                7
  //   =COUNTA(1, "x", #N/A)         3

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $count = 0;
      foreach ( $args as $arg ) {
        if ( padFormulaIsRange ( $arg ) ) {
          foreach ( padFormulaFlat ( $arg ) as $value )
            if ( $value !== NULL )
              $count++;
        } elseif ( $arg !== NULL )
          $count++;
      }
      return (float) $count;
    }
  ];

?>
