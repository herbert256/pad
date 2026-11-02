<?php

  // ceiling - CEILING(number, significance) rounds a number up to a multiple of the significance:
  // a positive number away from zero, a negative one toward zero with a positive significance and
  // away from zero with a negative one. A positive number with a negative significance is #NUM!; a
  // significance of 0 gives 0. It rounds the number as it shows, to its 15 digits: CEILING(2.5,
  // 0.1) is 2.5.
  //
  //   =CEILING(2.5, 1)              3
  //   =CEILING(-2.5, 2)             -2
  //   =CEILING(-2.5, -2)            -4
  //   =CEILING(C2, 0.25)            1.25
  //   =CEILING(2.5, -1)             #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number       = padFormulaArgNumber ( $args [0], $env );
      $significance = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $number, $significance ) )
        return $error;
      if ( $number == 0 or $significance == 0 )
        return 0.0;
      if ( $number > 0 and $significance < 0 )
        return padFormulaError ( '#NUM!' );
      return padFormulaMultiple ( $number, $significance, 'up' );
    }
  ];

?>
