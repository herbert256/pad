<?php

  // ifs - IFS(test, value, ...) is the value of the first test that holds - pairs of a test and a
  // value; when none holds #N/A.
  //
  //   =IFS(1>2, "a", 2>1, "b")      b
  //   =IFS(FALSE, 1)                #N/A

  return [
    'group' => 'Logic',
    'args'  => [ 2, 254 ],
    'pairs' => 0,
    'takes' => 'pairs of a test and a value',
    'call'  => function ( $args, $env ) {
      for ( $at = 0; $at + 1 < count ( $args ); $at += 2 ) {
        $test = padFormulaArgBool ( $args [$at], $env );
        if ( padFormulaIsError ( $test ) )
          return $test;
        if ( $test )
          return $args [ $at + 1 ];
      }
      return padFormulaError ( '#N/A' );
    }
  ];

?>
