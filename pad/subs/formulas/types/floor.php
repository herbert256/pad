<?php

  // floor - FLOOR(number, significance) rounds a number down to a multiple of the significance: a
  // positive number toward zero, a negative one away from zero with a positive significance and
  // toward zero with a negative one. A positive number with a negative significance is #NUM!, a
  // significance of 0 #DIV/0! - for any number but 0, which stays 0. It rounds the number as it
  // shows, to its 15 digits: FLOOR(0.3, 0.1) is 0.3.
  //
  //   =FLOOR(3.7, 2)                2
  //   =FLOOR(-2.5, -2)              -2
  //   =FLOOR(-2.5, 2)               -4
  //   =FLOOR(2.5, -2)               #NUM!
  //   =FLOOR(5, 0)                  #DIV/0!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number       = padFormulaArgNumber ( $args [0], $env );
      $significance = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $number, $significance ) )
        return $error;
      if ( $number == 0 )
        return 0.0;
      if ( $significance == 0 )
        return padFormulaError ( '#DIV/0!' );
      if ( $number > 0 and $significance < 0 )
        return padFormulaError ( '#NUM!' );
      return padFormulaMultiple ( $number, $significance, 'down' );
    }
  ];

?>
