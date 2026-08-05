<?php

  // Gathers the arguments of a type that takes parameters, then dispatches to
  // eval/parms/$kind.php.
  //
  // The tokens after the type token, up to the end position the parser recorded in [3] (or just
  // the next token when it recorded none), are collected into $parm and removed from $result.
  // A VAL token sitting immediately before the type is what is being piped in, so it becomes
  // $value and is consumed too; otherwise the segment's own input $myself is used. Returns
  // whatever the eval/parms/ handler produces.

  if ( $result [$k] [3] == 0 ) {
    $padEvalNextKey = padEvalNextKey ( $result, $k );
    $result [$k] [3] = ( $padEvalNextKey and $padEvalNextKey <= $end ) ? $padEvalNextKey + 1 : 0;
  }

  $parm = [];
  foreach ( $result as $key => $val )
    if ( $key > $k and $key <= $result [$k] [3] - 1 ) {
      $parm [] = $val[0];
      unset ( $result [$key] );
    }

  $count = count ( $parm );

  if ( $b >= $start and $result [$b] [1] == 'VAL' ) {
    $value = $result [$b] [0];
    unset ($result [$b]);
  } else
    $value = $myself;

  return include PAD . "eval/parms/$kind.php" ;

?>