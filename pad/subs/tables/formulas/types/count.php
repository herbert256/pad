<?php

  // count - COUNT(value, ...) counts the numbers: typed into the call a number, TRUE or FALSE and a
  // text that reads as a number count, another text and an error do not; of a range only its
  // numbers.
  //
  //   =COUNT(B1:B7)                 6
  //   =COUNT(1, "2", "x", TRUE)     3

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $count = 0;
      foreach ( $args as $arg ) {
        if ( padFormulaIsRange ( $arg ) ) {
          foreach ( padFormulaFlat ( $arg ) as $value )
            if ( is_float ( $value ) or is_int ( $value ) )
              $count++;
        } elseif ( is_float ( $arg ) or is_int ( $arg ) or is_bool ( $arg ) )
          $count++;
        elseif ( is_string ( $arg ) and padFormulaParseValue ( $arg ) !== NULL )
          $count++;
      }
      return (float) $count;
    }
  ];

?>
