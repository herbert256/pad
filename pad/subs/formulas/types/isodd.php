<?php

  // isodd - ISODD(number) is TRUE when the number is odd and FALSE when it is even - a fraction is
  // cut toward zero first, so 2.5 is even, and an empty cell is 0. A text that reads as no number
  // is #VALUE!, and so is a logical value, as Excel takes it; an error is the answer.
  //
  //   =ISODD(B3)                    TRUE
  //   =ISODD(-1)                    TRUE
  //   =ISODD(2.5)                   FALSE
  //   =ISODD(TRUE)                  #VALUE!

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      if ( is_bool ( $value ) )
        return padFormulaError ( '#VALUE!' );
      $whole = padFormulaArgInt ( $value, $env );
      return padFormulaIsError ( $whole ) ? $whole : $whole % 2 != 0;
    }
  ];

?>
