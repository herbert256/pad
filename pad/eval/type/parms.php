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

  $padEvalCalled = ( $result [$k] [3] != 0 );

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

  // A test written infix may be denied the way SQL and Python write it - $s not in ('a', 'b'),
  // $name not like 'A%': the value before the not is the test's input, and the not, left
  // standing, denies the test's answer. The test took the not's place as its input - the
  // segment's empty value - and the value and the denied empty test ran together as text,
  // a1, true for every row.

  if ( $kind == 'pad' and in_array ( $name, padEval_tests ) and ! $padEvalInCall
       and $b >= $start and $result [$b] [1] == 'OPR' and $result [$b] [0] === 'NOT' ) {

    $padEvalNotBefore = FALSE;

    foreach ( $result as $padEvalKey => $padEvalTok )
      if ( $padEvalKey >= $start and $padEvalKey < $b )
        $padEvalNotBefore = $padEvalKey;

    if ( $padEvalNotBefore !== FALSE and $result [$padEvalNotBefore] [1] == 'VAL' )
      $b = $padEvalNotBefore;

  }

  $padEvalPiped = ( $b >= $start and $result [$b] [1] == 'VAL' and ! $padEvalInCall );

  if ( $padEvalPiped ) {
    $value = $result [$b] [0];
    unset ($result [$b]);
  } else
    $value = $myself;

  // A built-in written as a call where nothing is piped into it - at the head of an
  // expression that is no pipe body (eval/eval.php), or as an argument of another call -
  // reads its first argument as the value, as PHP's functions of those names read theirs:
  // {echo upper($s)}, {if left($code, 2) eq 'NL'}, replace('l', upper('x')). The value was
  // the segment's empty one and the argument went unread: {echo trim('  a  ')} printed
  // nothing, upper($s) eq 'HELLO' was false, and inside replace the outer pipe's value was
  // upper-cased instead of the 'x'. date keeps PHP's order - the format, then the moment:
  // date('Y', 0) is 1970. A pipe - {$s | left(2)} - still hands its value in.

  if ( $kind == 'pad' and $padEvalCalled and $count and ! $padEvalPiped
       and ( $padEvalInCall or ( $GLOBALS ['padEvalHead'] ?? FALSE ) ) ) {

    if ( in_array ( $name, [ 'date', 'time', 'timestamp' ] ) ) {
      $value = $parm [1] ?? '';
      if ( is_numeric ( $value ) )
        $value = new DateTimeImmutable ( '@' . (int) $value );
      unset ( $parm [1] );
      $parm = array_values ( $parm );
    } else
      $value = array_shift ( $parm );

    $count = count ( $parm );

  }

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
