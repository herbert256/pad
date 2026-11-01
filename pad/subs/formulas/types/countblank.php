<?php

  // countblank - COUNTBLANK(range) counts the empty cells of a range, and those holding the empty
  // text a formula answers.
  //
  //   =COUNTBLANK(A1:E8)            5

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args ) {
      $range = $args [0];
      if ( ! padFormulaIsRange ( $range ) )
        return padFormulaError ( '#VALUE!' );
      [ $hits, $past ] = padFormulaMatching ( $range, fn ( $value ) => $value === NULL or $value === '' );
      return (float) ( count ( $hits ) + $past );
    }
  ];

?>
