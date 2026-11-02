<?php

  // irr - IRR(values, [guess]) is the internal rate of return of cash flows at regular periods -
  // the rate at which their NPV is 0, the first at once - found by Newton's method from guess
  // (10%), as Excel finds it: when 150 steps do not settle it to within 0.0000001, #NUM!. The
  // values need a negative and a positive one; of a range only its numbers count.
  //
  //   =ROUND(IRR(E2:E7), 4)            0.0866
  //   =ROUND(IRR(E2:E6), 4)            -0.0212
  //   =ROUND(IRR(E2:E4, -10%), 4)      -0.4435

  return [
    'group' => 'Finance',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $guess   = isset ( $args [1] ) ? padFormulaArgNumber ( $args [1], $env ) : 0.1;
      if ( $error = padFormulaFirstError ( $numbers, $guess ) )
        return $error;
      if ( ! $numbers or max ( $numbers ) <= 0 or min ( $numbers ) >= 0 )
        return padFormulaError ( '#NUM!' );
      // The root is that of the flows' value at the end of the last period - their NPV times
      // (1+rate)^n, the same root above -100% - which Newton settles from 10% as it settles
      // RATE: on their value now its first step for a mortgage of 360 months landed below
      // -100%, and IRR was #NUM!. The value and its slope by Horner's rule, in one pass.
      return padFormulaNewton ( function ( $rate ) use ( $numbers ) {
        $grow  = 1 + $rate;
        $value = 0.0;
        $slope = 0.0;
        foreach ( $numbers as $number ) {
          $slope = $slope * $grow + $value;
          $value = $value * $grow + $number;
        }
        return [ $value, $slope ];
      }, $guess );
    }
  ];

?>
