<?php

  // sqrt - SQRT(number) is the square root; of a negative number #NUM!.
  //
  //   =SQRT(16)                     4
  //   =SQRT(-1)                     #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      if ( padFormulaIsError ( $number ) )
        return $number;
      return $number < 0 ? padFormulaError ( '#NUM!' ) : sqrt ( $number );
    }
  ];

?>
