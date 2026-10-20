<?php

  // Runs a sequence action or a sequence type from inside an expression.
  //
  // $name is looked up first among the action handlers in sequence/actions/types/, then among the
  // sequence type directories in sequence/types/. Either way the name and the collected arguments
  // are handed to the subsystem through $pqSetAction / $pqSetParms and one of the PQ start/eval/
  // entry points, but the two answer different questions:
  //
  //   sequence:sum([1,2,3])    an action over values given to it - a list back
  //   sequence:fibonacci(8)    the eighth term of that type      - the term back
  //
  // A name that is neither used to fall out of the bottom of this file, and a file that returns
  // nothing returns 1 from include - so sequence:nosuchtype(3) answered 1, which is a number a
  // sequence could plausibly have produced.

  // A value piped in is the call's argument when it is written without one: [1,2,3] |
  // sequence:sum is the sum of the list and 5 | sequence:fibonacci the fifth term, where the
  // piped value was dropped and the call made over nothing - 0 for both.

  if ( ! $count and $value !== NULL and $value !== '' ) {
    $parm   = [ $value ];
    $count  = 1;
    $pqPiped = TRUE;
  }

  if ( pqAction ( $name ) ) {

    // A single value piped into an action that is no list and names no store has nothing to
    // act on and passes through, as action: lets it: 'z' | sequence:reverse ended on "there is
    // no stored sequence named 'z'", and 5 | sequence:sum on the same for '5'.

    if ( ( $pqPiped ?? FALSE ) and ! pqActionPiped ( $value ) )
      return $value;

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/action.php';

  }

  if ( pqSeq ( $name ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    // The parameter of sequence:eval(3, '@ * 2') is a string the template wrote, which the
    // eval type runs (sequence/types/eval/loop.php) - one from a value, sequence:eval(3, $q),
    // it refuses. Only for the eval type: a literal of sequence:repeat(2, '...') let the same
    // text from a value run in sequence:eval(2, $q).

    if ( strtolower ( $name ) == 'eval' and ( $padEvalLiteral [1] ?? FALSE ) and is_string ( $parm [1] ?? NULL ) )
      $GLOBALS ['pqEvalArguments'] [ $parm [1] ] = TRUE;

    return include PQ . 'start/eval/sequence.php';

  }

  global $padCheckSyntax;

  return ( $padCheckSyntax ) ? padError ( "Sequence '$name' is not a sequence type or an action" ) : '';

?>
