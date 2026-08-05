<?php

  // Runs a sequence action or a whole sequence type from inside an expression.
  //
  // $name is looked up first among the action handlers in sequence/actions/types/, then among
  // the sequence type directories in sequence/types/. Either way the name and the collected
  // arguments are handed to the sequence subsystem through $pqSetAction / $pqSetParms and one
  // of the PQ start/eval/ entry points. Returns nothing when $name is neither.

  if ( file_exists ( PAD . "sequence/actions/types/$name.php" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/action.php';

  } elseif ( file_exists ( PAD . "sequence/types/$name" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/sequence.php';

  }

?>