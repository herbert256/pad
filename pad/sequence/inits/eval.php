<?php

  // Opens a run started from an expression that named a sequence *type* - sequence:fibonacci(8).
  //
  // eval/parms/sequence.php parks the name in $pqSetAction and the call's arguments in
  // $pqSetParms, and inits/parms.php has already read those as the run's parameters. The single
  // positional argument matches none of the named ones, so it is still to be placed: it is the
  // position of the term wanted. Generate that many and the last of them is the answer.
  //
  // Without this the run went through actions/set.php, which files the name as an *action* and
  // takes the first argument as the values to act on. sequence:fibonacci(8) therefore looked for
  // an action called fibonacci, found none, and ended the request.

  $pqSeq  = $pqSetAction;
  $pqRows = intval ( $pqSetParms [0] ?? 1 );

  if ( $pqRows < 1 )
    $pqRows = 1;

?>
