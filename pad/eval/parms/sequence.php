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

  if ( file_exists ( PAD . "sequence/actions/types/$name.php" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/action.php';

  }

  if ( file_exists ( PAD . "sequence/types/$name" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/sequence.php';

  }

  return padError ( "Sequence '$name' is not a sequence type or an action" );

?>