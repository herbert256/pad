<?php

  // ln - LN(number) is the natural logarithm; of 0 or less #NUM!.
  //
  //   =LN(EXP(2))                   2
  //   =LN(0)                        #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      if ( padFormulaIsError ( $number ) )
        return $number;
      return $number <= 0 ? padFormulaError ( '#NUM!' ) : log ( $number );
    }
  ];

?>
