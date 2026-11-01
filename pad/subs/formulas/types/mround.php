<?php

  // mround - MROUND(number, multiple) rounds a number to the nearest multiple of another - half
  // away from zero. A number and a multiple of different signs are #NUM!, a multiple of 0 gives 0,
  // and a logical value is #VALUE!, as Excel takes it. It rounds the number as it shows, to its 15
  // digits: MROUND(1.3, 0.2) is 1.4.
  //
  //   =MROUND(10, 3)                9
  //   =MROUND(-10, -3)              -9
  //   =MROUND(1.3, 0.2)             1.4
  //   =MROUND(5, -2)                #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number   = padFormulaScalar ( $args [0], $env );
      $multiple = padFormulaScalar ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $number, $multiple ) )
        return $error;
      if ( is_bool ( $number ) or is_bool ( $multiple ) )
        return padFormulaError ( '#VALUE!' );
      $number   = padFormulaNumber ( $number );
      $multiple = padFormulaNumber ( $multiple );
      if ( $error = padFormulaFirstError ( $number, $multiple ) )
        return $error;
      if ( $number == 0 or $multiple == 0 )
        return 0.0;
      if ( ( $number > 0 ) != ( $multiple > 0 ) )
        return padFormulaError ( '#NUM!' );
      return padFormulaMultiple ( $number, $multiple, 'half' );
    }
  ];

?>
