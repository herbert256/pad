<?php

  // Catch handler for call/_try.php, the include of an application PHP file: reports the
  // throwable with its file and line through padErrorGo() and returns '' so the caller
  // ends up with an empty result instead of a fatal.

  // The PHP of a tag whose level asked for notOk= or error= takes the level's fallback, as
  // a handler that throws in the level itself does: the throwable goes on to the level's
  // guard (try/catch/level/go.php), which honours both. Caught and reported here, a tag of
  // the application, of _common or of the engine never reached its notOk content - the pad
  // action answered 500, the log action an empty tag.

  if ( ( end ( $padTryStack ) [0] ?? '' ) == 'level/go.php' and ( padTagParm ( 'notOk' ) or padTagParm ( 'error' ) ) )
    throw $padTryException;

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

  $padCallPHP = '';
  $padCallOB  = '';

  return '';

?>
