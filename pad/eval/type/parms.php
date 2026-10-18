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

  // $padEvalLiteral says of each argument whether it is a string the template wrote (parse.php).

  $parm = $padEvalLiteral = [];
  foreach ( $result as $key => $val )
    if ( $key > $k and $key <= $result [$k] [3] - 1 ) {
      $parm []           = $val[0];
      $padEvalLiteral [] = ! empty ( $val ['lit'] );
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

  // A test written infix as an argument of a call - php:strval($n in (5)) - takes its input
  // from its own argument, which starts where padEvalParse marked it (padEvalArgStart): the
  // value before it stood as an argument of its own, and the test read its parameter list as
  // its input - var_export($n in (5), 1) was handed three arguments. A test that starts its
  // argument - php:max(1, in (3)) - has nothing in front of it, as before.

  $padEvalTest    = ( $kind == 'pad' and in_array ( $name, padEval_tests ) );
  $padEvalArgFrom = ( $padEvalTest and $padEvalInCall ) ? padEvalArgStart ( $result, $start, $k ) : $start;
  $padEvalTestArg = ( $padEvalTest and ( ! $padEvalInCall or $b >= $padEvalArgFrom ) );

  // A test written infix may be denied the way SQL and Python write it - $s not in ('a', 'b'),
  // $name not like 'A%': the value before the not is the test's input, and the not, left
  // standing, denies the test's answer. The test took the not's place as its input - the
  // segment's empty value - and the value and the denied empty test ran together as text,
  // a1, true for every row.

  // The typed references are reduced from the right, so the one a test stands after -
  // array:rows contains 2, field:n in (1, 2), bool:open not in (1) - is still a TYPE token when
  // the test is made. One that takes no parameters (eval/single/) is reduced first, in place:
  // the test was handed the segment's empty value, and the reference was left beside its
  // answer, a list and a value side by side.

  if ( $padEvalTestArg and $b >= $padEvalArgFrom ) {

    $padEvalLeft = $b;

    if ( $result [$b] [1] == 'OPR' and $result [$b] [0] === 'NOT' )
      foreach ( $result as $padEvalKey => $padEvalTok )
        if ( $padEvalKey >= $padEvalArgFrom and $padEvalKey < $b )
          $padEvalLeft = $padEvalKey;

    if ( $result [$padEvalLeft] [1] == 'TYPE' and ( $result [$padEvalLeft] [3] ?? 0 ) == 0
         and file_exists ( PAD . "eval/single/{$result[$padEvalLeft][2]}.php" ) )
      padEvalOpr ( $result, $myself, $padEvalLeft, $padEvalLeft );

  }

  if ( $padEvalTestArg
       and $b >= $padEvalArgFrom and $result [$b] [1] == 'OPR' and $result [$b] [0] === 'NOT' ) {

    $padEvalNotBefore = FALSE;

    foreach ( $result as $padEvalKey => $padEvalTok )
      if ( $padEvalKey >= $padEvalArgFrom and $padEvalKey < $b )
        $padEvalNotBefore = $padEvalKey;

    if ( $padEvalNotBefore !== FALSE and $result [$padEvalNotBefore] [1] == 'VAL' )
      $b = $padEvalNotBefore;

  }

  // The input of a test written infix is the whole arithmetic and concatenation on its left
  // (lib/eval/operand.php), reduced to one value here, as brackets around it would be. The
  // test took the one value just before it: $n + 1 range (9, 9) was 3 + (1 range (9, 9)),
  // true for n = 3, and $p * $q in (30) was 10 * ('3' in (30)).

  if ( $padEvalTestArg
       and $b >= $padEvalArgFrom and $result [$b] [1] == 'VAL' ) {

    $padEvalFirst = padEvalTestLeft ( $result, $padEvalArgFrom, $b );

    if ( $padEvalFirst < $b ) {

      padEvalOpr ( $result, $myself, $padEvalFirst, $b );

      $padEvalLast = -1;

      foreach ( $result as $padEvalKey => $padEvalTok )
        if ( $padEvalKey >= $padEvalFirst and $padEvalKey <= $b )
          $padEvalLast = $padEvalKey;

      $b = $padEvalLast;

    }

  }

  $padEvalPiped = ( $b >= $padEvalArgFrom and $result [$b] [1] == 'VAL' and ( ! $padEvalInCall or $padEvalTestArg ) );

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

  $padEvalShifted = FALSE;

  // An application's function written so - exclaim('ab') - reads its argument the same way:
  // its file is handed $value and $parm as a built-in's is, and got the empty value with the
  // argument as a parameter, ab answered !.

  if ( in_array ( $kind, [ 'pad', 'app', 'function' ], TRUE ) and $padEvalCalled and $count and ! $padEvalPiped
       and ( $padEvalInCall or ( $GLOBALS ['padEvalHead'] ?? FALSE ) ) ) {

    $padEvalShifted = TRUE;

    if ( $kind != 'app' and in_array ( $name, [ 'date', 'time', 'timestamp' ] ) ) {
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

    // Named for what it is: a kind that only tags have, or no kind at all - a:b said "a a:
    // can not be used inside an expression".

    if ( $padCheckSyntax )
      if ( padValid ( $kind ) and file_exists ( PAD . "types/$kind.php" ) )
        padError ( "Expression error: the prefix '$kind:' belongs to tags and can not be used inside an expression - $kind:$name" );
      else
        padError ( "Expression error: there is no prefix '$kind:' - $kind:$name" );

    return '';

  }

  return include PAD . "eval/parms/$kind.php" ;

?>
