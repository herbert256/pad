<?php

  // log - LOG(number, [base]) is the logarithm to a base, 10 when none is given. A number or a
  // base of 0 or less is #NUM!, a base of 1 #DIV/0!.
  //
  //   =LOG(100)                     2
  //   =LOG(8, 2)                    3
  //   =LOG(10, 1)                   #DIV/0!

  return [
    'group' => 'Math',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      $base   = ( isset ( $args [1] ) ) ? padFormulaArgNumber ( $args [1], $env ) : 10.0;
      if ( $error = padFormulaFirstError ( $number, $base ) )
        return $error;
      if ( $number <= 0 or $base <= 0 )
        return padFormulaError ( '#NUM!' );
      if ( $base == 1 )
        return padFormulaError ( '#DIV/0!' );
      return padFormulaRound15 ( log ( $number ) / log ( $base ) );
    }
  ];

?>
