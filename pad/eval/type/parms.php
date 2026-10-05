<?php

  // Gathers the arguments of a type that takes parameters, then dispatches to
  // eval/parms/$kind.php.
  //
  // The tokens after the type token, up to the end position the parser recorded in [3] (or just
  // the next token when it recorded none), are collected into $parm and removed from $result.
  // A VAL token sitting immediately before the type is what is being piped in, so it becomes
  // $value and is consumed too; otherwise the segment's own input $myself is used. Returns
  // whatever the eval/parms/ handler produces.

  // Without parentheses the type takes the next token as its argument - but only a value:
  // {if now gt 5} handed gt to now and compared nothing, always true.

  if ( $result [$k] [3] == 0 ) {
    $padEvalNextKey = padEvalNextKey ( $result, $k );
    $result [$k] [3] = ( $padEvalNextKey and $padEvalNextKey <= $end
                         and ( $result [$padEvalNextKey] [1] ?? '' ) != 'OPR' ) ? $padEvalNextKey + 1 : 0;
  }

  $parm = [];
  foreach ( $result as $key => $val )
    if ( $key > $k and $key <= $result [$k] [3] - 1 ) {
      $parm [] = $val[0];
      unset ( $result [$key] );
    }

  $count = count ( $parm );

  // Inside another call's argument list the value in front is the argument before this
  // one, not a piped value - the commas between arguments are gone by now. replace(5,
  // sequence:fibonacci(6)) handed the 5 to fibonacci and left replace one parameter short.

  $padEvalInCall = FALSE;

  foreach ( $result as $padEvalKey => $padEvalTok )
    if ( $padEvalKey < $k and ( $padEvalTok [1] ?? '' ) == 'TYPE' and ( $padEvalTok [3] ?? 0 ) > $k )
      $padEvalInCall = TRUE;

  if ( $b >= $start and $result [$b] [1] == 'VAL' and ! $padEvalInCall ) {
    $value = $result [$b] [0];
    unset ($result [$b]);
  } else
    $value = $myself;

  // A kind that is only ever a tag - select, common, keep, make, remove - has no handler
  // here; that died on a raw missing include. Strict mode names it, the lenient walk
  // yields empty as for anything else it cannot evaluate.

  if ( ! file_exists ( PAD . "eval/parms/$kind.php" ) ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "a $kind: can not be used inside an expression" );

    return '';

  }

  return include PAD . "eval/parms/$kind.php" ;

?>