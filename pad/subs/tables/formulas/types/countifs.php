<?php

  // countifs - COUNTIFS(criteria_range, criteria, ...) counts the places where every range meets
  // its criteria - pairs of a range and a criterion (see COUNTIF), the ranges of one size.
  //
  //   =COUNTIFS(A2:A7, "Apples", B2:B7, ">10")   1
  //   =COUNTIFS(B2:B7, ">0", C2:C7, "<2")        3

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 254 ],
    'pairs' => 0,
    'takes' => 'pairs of a range and its criteria',
    'call'  => function ( $args, $env ) {
      $pairs = [];
      for ( $at = 0; $at + 1 < count ( $args ); $at += 2 )
        $pairs [] = [ $args [$at], $args [$at + 1] ];
      $hits = padFormulaHits ( $pairs, $env );
      return padFormulaIsError ( $hits ) ? $hits : (float) ( count ( $hits [0] ) + $hits [1] );
    }
  ];

?>
