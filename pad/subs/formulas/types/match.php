<?php

  // match - MATCH(value, array, [type]) is the place of a value in a row or a column, from 1. Type
  // 0 finds it exactly - a text in any case, * and ? as wildcards; 1 (the default) finds, in a list
  // that rises, the last value not above it; -1, in a list that falls, the last value not below
  // it. Not found is #N/A.
  //
  //   =MATCH("Pears", A2:A7, 0)           2
  //   =MATCH(DATE(2024,6,1), D2:D7)       4
  //   =MATCH("Kiwis", A2:A7, 0)           #N/A

  return [
    'group' => 'Lookup',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $wanted = padFormulaScalar ( $args [0], $env );
      $type   = isset ( $args [2] ) ? padFormulaArgNumber ( $args [2], $env ) : 1.0;
      if ( $error = padFormulaFirstError ( $wanted, $type ) )
        return $error;
      $list = padFormulaVector ( $args [1] );
      if ( $list === FALSE or $wanted === NULL )
        return padFormulaError ( '#N/A' );
      $place = padFormulaLookup ( $wanted, $list, $type > 0 ? 1 : ( $type < 0 ? -1 : 0 ) );
      return $place === FALSE ? padFormulaError ( '#N/A' ) : (float) ( $place + 1 );
    }
  ];

?>
