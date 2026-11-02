<?php

  // randbetween - RANDBETWEEN(bottom, top) is a whole number from bottom to top, both included -
  // bottom rounded up to a whole number and top down, a bottom then above the top #NUM!. Drawn as
  // RAND draws: a spreadsheet gives another on every computation, here it is the same for the
  // same seed (the seed= of {sheet:grid}), cell and call, so a page answers the same twice.
  //
  //   =RANDBETWEEN(1, 6)>=1         TRUE
  //   =RANDBETWEEN(7, 7)            7
  //   =RANDBETWEEN(1.5, 2.5)        2
  //   =RANDBETWEEN(2, 1)            #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, &$env ) {
      $bottom = padFormulaArgNumber ( $args [0], $env );
      $top    = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $bottom, $top ) )
        return $error;
      $bottom = ceil ( padFormulaRound15 ( $bottom ) );
      $top    = floor ( padFormulaRound15 ( $top ) );
      if ( $bottom > $top )
        return padFormulaError ( '#NUM!' );
      return min ( $top, $bottom + floor ( padFormulaRandom ( $env ) * ( $top - $bottom + 1 ) ) );
    }
  ];

?>
