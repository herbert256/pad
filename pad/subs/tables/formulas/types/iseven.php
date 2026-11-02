<?php

  // iseven - ISEVEN(number) is TRUE when the number is even and FALSE when it is odd - a fraction
  // is cut toward zero first, so 2.5 is even, and an empty cell is 0. A text that reads as no
  // number is #VALUE!, and so is a logical value, as Excel takes it; an error is the answer.
  //
  //   =ISEVEN(B2)                   TRUE
  //   =ISEVEN(-1)                   FALSE
  //   =ISEVEN(2.5)                  TRUE
  //   =ISEVEN("x")                  #VALUE!

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      if ( is_bool ( $value ) )
        return padFormulaError ( '#VALUE!' );
      $whole = padFormulaArgInt ( $value, $env );
      return padFormulaIsError ( $whole ) ? $whole : $whole % 2 == 0;
    }
  ];

?>
