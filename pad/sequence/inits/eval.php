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

  // The call names the type and nothing more, as a bare {sequence multiply} does, and so the
  // type's parameter is the TRUE a bare option is - read as the 1 those types default to. It
  // was the '' of a run with no parameter at all, which multiply, add, subtract, power and
  // exponentiation did arithmetic with: sequence:multiply(4) ended the request on int *
  // string, and sequence:range(4) on the undefined $padParm range falls back to.

  $pqParm = TRUE;

  if ( $pqRows < 1 )
    $pqRows = 1;

  // The term asked for may lie beyond the default candidate limit - the 1230th prime is past
  // the 10,000th integer - so a position lookup may test up to the hard ceiling, and
  // sequence/sequence.php reports a term it could not reach instead of answering the last
  // one it found.

  $pqTry      = $padSeqMaxTries ?? 1000000;
  $pqPosition = TRUE;

?>
