<?php

  // The left operand of a test written infix - in, like, matches, range, between, contains
  // (padEval_tests). Read as a comparison the test binds weaker than the arithmetic and the
  // concatenation on its left, as eq does: $n + 1 range (9, 9) tests $n + 1, as brackets
  // around it would. eval/type/parms.php asks for it before the test takes its input, and
  // reduces what this answers to one value first.
  //
  // Walked back from $b, the value right before the test: an operand, the signs and ! in
  // front of it, and then - when a binary operator binding stronger than the comparisons
  // (padEval_strong) stands before that - the operator and the operand before it, and so on.
  // An operand is a value, or a call with its arguments: padEvalOpnCls reduced the arguments
  // to values and left the call a TYPE token whose [3] lies past them - or a bare call, which
  // takes no argument when an operator follows it. Such an operator with nothing before it
  // in the range has the piped value as its left side, {$x | * 2 in (10)}, and goes along.
  // Anything else ends the operand - a comparison, a not, and, ??, a second value.
  //
  // Answers the key the operand starts at, $b itself when it is that one value alone.

  const padEval_strong = [ '**', '*', '/', '%', '+', '-', '.' ];

  function padEvalTestLeft ( $result, $start, $b ) {

    $keys = [];

    foreach ( $result as $k => $t ) {
      if ( $k < $start ) continue;
      if ( $k > $b     ) break;
      $keys [] = $k;
    }

    $first = $b;

    for ( $n = count ( $keys ) - 1; $n >= 0; $n -= 2 ) {

      $t = $result [ $keys [$n] ];

      if ( $t [1] == 'VAL' ) {

        for ( $m = 0; $m < $n; $m++ )
          if ( $result [ $keys [$m] ] [1] == 'TYPE' and ( $result [ $keys [$m] ] [3] ?? 0 ) > $keys [$n] ) {
            $n = $m;
            break;
          }

      } elseif ( $t [1] != 'TYPE' or ( $t [3] ?? 0 ) != 0 )
        break;

      $first = $keys [$n];

      while ( $n > 0 and $result [ $keys [$n-1] ] [1] == 'OPR'
              and in_array ( $result [ $keys [$n-1] ] [0], [ '!', 'NEG', 'POS' ], TRUE ) )
        $first = $keys [--$n];

      if ( $n == 0 or $result [ $keys [$n-1] ] [1] != 'OPR'
           or ! in_array ( $result [ $keys [$n-1] ] [0], padEval_strong, TRUE ) )
        break;

      if ( $n == 1 )
        return $keys [0];

    }

    return $first;

  }

?>
